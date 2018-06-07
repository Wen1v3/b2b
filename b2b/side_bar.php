class SideBar extends React.Component {
	getName(i) {
		if (i == 0) return this.props.categories[i].name.replace(":", "->");
		var piza = this.props.categories[i].name.split(":");
		var upperPiza = this.props.categories[i - 1].name.split(":");
		if (piza[0] == upperPiza[0]) {
			return (
				<span>
					<span style={{visibility:'hidden'}}>{piza[0]}</span>->{piza[1]}
				</span>
			);
		} else {
			return this.props.categories[i].name.replace(":", "->");
		}
	}

	isLevelOneHasProducts(cname) {
		for (var i = 0; i < this.props.categories.length; i++) {
			var piza = this.props.categories[i].name.split("->");
			if (cname == piza[0]) return 1;
		}
		return 0;
	}
	
	isLevelTwoHasProducts(cname) {
		for (var i = 0; i < this.props.categories.length; i++) {
			var piza = this.props.categories[i].name.split(":");
			if (cname == piza[0]) return 1;
		}
		return 0;
	}
	
	getLevelTwoParams(cname) {
		var ps = [];
		
		for (var i = 0; i < this.props.categories.length; i++) {
			var piza = this.props.categories[i].name.split(":");
			if (cname == piza[0]) {
				if (piza.length > 1) ps.push(piza[1]);
			}
		}
		return ps;
	}
	
	getCidByName(cname) {
		for (var i = 0; i < this.props.categories.length; i++) {
			if (cname == this.props.categories[i].name) return i;
		}
		return 0;
	}

	render() {
		//console.log("SideBar");
		//return <div></div>;
		
		var cTree = [];
		if (this.props.cTree != null) {
			for (var i = 0; i < this.props.cTree.length; i++) {
				var levOneC = this.props.cTree[i];
				var levOneCName = levOneC.name;
				if (this.isLevelOneHasProducts(levOneC.name) == 0) continue;
				
				var cBranch = [];
				for (var j = 0; j < levOneC.subCategories.length; j++) {
					var levTwoCName = levOneC.subCategories[j];
					if (this.isLevelTwoHasProducts(levOneCName + "->" + levTwoCName) == 0) continue;
					
					var ps = this.getLevelTwoParams(levOneCName + "->" + levTwoCName);
					if (ps.length > 0) {
						var cLeaf = [];
						for (var k = 0; k < ps.length; k++) {
							var cid = this.getCidByName(levOneCName + "->" + levTwoCName + ":" + ps[k]);
							cLeaf.push(
								<div key={i + "-" + j + "-" + k} 
									id={cid}
									className={(this.props.mid == cid ? 'selected' : '') + " level-3 clickable"}	
									onClick={(e) => this.props.onCategoryClick(e)}>
									{ps[k] + " | " + this.props.categories[cid].skus.length}	
								</div>
							);
						}
						
						cBranch.push(
							<div key={i + "-" + j}>
								<div id={"cname-" + levOneCName + "->" + levTwoCName}
									className={(this.props.mid == ("cname-" + levOneCName + "->" + levTwoCName) ? 'selected' : '') + " level-2 clickable"}
									onClick={(e) => this.props.onCategoryClick(e)}>{levTwoCName}</div>
								{cLeaf} 
							</div>
						);				
					} else {
						var cid = this.getCidByName(levOneCName + "->" + levTwoCName);
						cBranch.push(
							<div key={i + "-" + j}
								id={cid} 
								className={(this.props.mid == cid ? 'selected' : '') + " level-2 clickable"}	
								onClick={(e) => this.props.onCategoryClick(e)}>
								{levTwoCName + " | " + this.props.categories[cid].skus.length}
							</div>
						);	
					}					

				}
				
				cTree.push(
					<div key={i}>
						<div id={"cname-" + levOneCName}
							className={(this.props.mid == ("cname-" + levOneCName) ? 'selected' : '') + " level-1 clickable"}
							onClick={(e) => this.props.onCategoryClick(e)}>{levOneCName}</div>
						{cBranch}
					</div>
				);				
			}
		}
		
		return (
			<div>
				<br />
				{cTree}
				<br />
				<div onClick={() => this.props.onMenuClick("all")} className={'clickable ' + (this.props.mid == "all" ? 'selected' : '')}>
					All Products
				</div>
				<div onClick={() => this.props.onMenuClick("cart")} className={'clickable ' + (this.props.mid == "cart" ? 'selected' : '')}>
					In Cart Products
				</div>
				<div onClick={() => this.props.onMenuClick("order")} className={'clickable ' + (this.props.mid == "order" ? 'selected' : '')}>
					In Orders Products
				</div>
				<div onClick={() => this.props.onMenuClick("orders")} className={'clickable ' + (this.props.mid == "orders" ? 'selected' : '')}>
					<?php
						if ($_GET["mode"] == "cus" || $_GET["mode"] == "td-buyer") {
							echo "My Orders";
						} else if ($_GET["mode"] == "td-seller") {
							echo "Customer Orders";
						} else {
							echo "TD Orders";
						}
					?>
				</div>
			</div>
		);
	}
}