class ProductList extends React.Component {
	dialog(messages) {
		//return <div></div>;
		var ms = messages.map((item, i) => {
			return (
				<div key={i} className={item["sender"] == '<?php echo $myEmail; ?>' ? 'mine' : 'others'}>
					<div>{item["datetime"]}</div>
					<div>{item["message"]}</div>
				</div>
			);
		});
		return ms; 
	}
	
	isInCart(sku, cart) {
		for (var i = 0; i < cart.length; i++) {
			if (sku == cart[i].sku) return 1;
		}
		return 0; 
	}
	
	getQtyInCart(sku, cart) {
		for (var i = 0; i < cart.length; i++) {
			if (sku == cart[i].sku) return cart[i].qty;
		}
		return 0; 
	}
	
	getQtyInOrders(sku, orders) {
		var result = 0;
		orders.forEach(function(order) {
			if (order.is_arrived == 0) {
				order.products.forEach(function(product) {
					//console.log(product);
					if (sku == product.sku) result += parseInt(product.qty);
				});
			}
		});
		return result;
	}
	
	handleAddPress(e) {
		if (e.key === 'Enter') {
			var bid = "#" + e.target.id.replace("input-qty", "bn-add-cart");
			$(bid).click();
		}
	}
	
	handleSendPress(e) {
		if (e.key === 'Enter') {
			var bid = "#" + e.target.id.replace("input-message", "bn-send");
			//console.log(bid);
			$(bid).click();
		}
	}
	
	getOrderProducts(order) {
		var products = order.products.map((product, i) => {
			return ( 
				<div key={product.sku} className="row product">
					<div className="col-sm-2"><img src={this.props.pMap[product.sku] == null ? "" : this.props.pMap[product.sku].imgUrl} /></div>							
					<div className="col-sm-1">{product.sku}</div>
					<div className="col-sm-4">{this.props.pMap[product.sku] == null ? "" : this.props.pMap[product.sku].name}</div>
					<div className="col-sm-2">{money(product.price)}</div>
					<div className="col-sm-1">{Math.round(product.qty, 0)}</div>
					<div className="col-sm-2">{money(product.price * product.qty)}</div>
				</div>
			);
		});	
		return products;
	}
	
	getOrderTotal(order) {
		var total = 0;
		order.products.forEach(function(product) {	
			total += product.price * product.qty;
		});
		return total;
	}
	
	orderClicked(orderNum) {
		var id = '#' + orderNum;
		$(id).slideToggle("slide");
	}
	
	render() {
		//console.log("ProductList");
		//return <div></div>;
		
		var orders = null;
		
		var products = null;
		if (this.props.categories.length != 0) {
			//console.log(this.props.mid);
			var ps = [];
			if (this.props.mid >= 0) {
				for (var i = 0; i < this.props.categories[this.props.mid].skus.length; i++) {
					ps.push(this.props.pMap[this.props.categories[this.props.mid].skus[i]]);	
				}
			} else if (this.props.mid.indexOf("cname-") == 0) {
				var piza = this.props.mid.split("cname-");
				for (var i = 0; i < this.props.categories.length; i++) {
					if (this.props.categories[i].name.indexOf(piza[1]) == 0) {
						for (var j = 0; j < this.props.categories[i].skus.length; j++) {
							ps.push(this.props.pMap[this.props.categories[i].skus[j]]);
						}
					}	
				}
			} else if (this.props.mid == "all") {
				var categories = this.props.categories;
				for (var i = 0; i < categories.length; i++) {
					for (var j = 0; j < categories[i].skus.length; j++) {
						ps.push(this.props.pMap[categories[i].skus[j]]);
					}
				}
			} else if (this.props.mid == "cart") {
				for (var i = 0; i < this.props.cart.length; i++) {
					ps.push(this.props.pMap[this.props.cart[i].sku]);
				}
			} else if (this.props.mid == "order") {
				var orderSkus = [];
				this.props.orders.forEach(function(order) {
					if (order.is_arrived == 0) {
						order.products.forEach(function(product) {	
							var sku = product.sku;
							if (orderSkus.indexOf(sku) == -1) {
								orderSkus.push(sku);
							}
						});
					}
				});
				for (var i = 0; i < orderSkus.length; i++) {
					if (this.props.pMap[orderSkus[i]] != null) {
						ps.push(this.props.pMap[orderSkus[i]]);
					}
				}
			} else if (this.props.mid == "orders") {
				if (this.props.orders != null) {
					orders = this.props.orders.map((order, i) => {
						return ( 
							<div key={order.ref_num} className="list-item order">
								<div className="row">
									<div className="col-sm-2 order-num">{order.ref_num}</div>
									<div className="col-sm-2">{order.created_date}</div>
									<div className="col-sm-4 detail" onClick={() => this.orderClicked(order.ref_num)}><a>Detail</a></div>
									<div className="col-sm-2">
										<button disabled={order.is_arrived == 1} className={(order.is_arrived == 1 ? "btn-success" : "btn-danger") + " btn"}
											onClick={() => this.props.onArrivedClick(order.ref_num)}>
											{order.is_arrived == 0 ? "Mark as Arrived" : "Arrived"}
										</button>
									</div>
									<div className="col-sm-2 order-total">{money(this.getOrderTotal(order))}</div>
								</div>
								<div id={order.ref_num} className="collapse">
									<div className="row order-detail-title">
										<div className="col-sm-2"></div>
										<div className="col-sm-1">Sku</div>
										<div className="col-sm-4">Name</div>
										<div className="col-sm-2">Price</div>
										<div className="col-sm-1">Qty</div>
										<div className="col-sm-2"></div>
									</div>
									{this.getOrderProducts(order)}
								</div>
							</div>
						);
					});								
				}	
			}	
			
			if (ps != null) {
				var urlArray = getQueryParams(window.location.href);	
				var mode = urlArray["mode"];
				products = ps.map((item, i) => {
					return ( 
						<div key={item.sku} className="list-item row">
							<div className="col-sm-2">
								<a href={"product_detail.php?sku=" + item.sku} target="_blank"><img src={item.imgUrl} /></a>
								<div className={item.soh <= 0 ? 'sold-out' : 'in-stock'}>Sold Out</div>
							</div>
							<div className="col-sm-3">
								<div id="plist-sku">Sku: {item.sku}</div>
								<div id="plist-name">{item.name}</div>
								<div id="plist-sd">{item.desc}</div>
							</div>
							<div className="col-sm-1">
								{(item.is_tiered_price == 1) ? (
									<div>
										<div id="">Cost1: {money(item.cost1)}</div>
										<div id="">Cost2: {money(item.cost2)}</div>
										<div id="">Cost3: {money(item.cost3)}</div>
									</div>
								) : (
									<div></div>
								)}
							</div>
							<div className="col-sm-2">
								<div>Cost(excl): {money(item.price)}</div>
								{mode == "sup" ? (
									<div></div>
								) : (
									<div>In Stock: {item.soh}</div>
								)}
								<div>On Order: {item.soo > 0 ? 'Yes' : 'No'}</div>
								<div>ETA: {item.eta}</div>
								<div>Box Qty: {item.box_qty}</div>
								
								
								{(mode == "sup" || mode == "td-buyer") ? (
									<div>Reorder Qty: {item.reorder_qty}</div>
								) : (
									<div></div>
								)}
								
								{mode == "td-buyer" ? (
									<div>Reorder Notes: {item.reorder_notes}</div>
								) : (
									<div></div>
								)}
								
								
								<input id={"input-qty-" + item.sku} className="input-qty" placeholder="qty" onKeyPress={this.handleAddPress} />
								<button id={"bn-add-cart-" + item.sku} className={this.isInCart(item.sku, this.props.cart) ? 'in' : 'nin'} onClick={() => this.props.onAddClick(item.sku)}>Add to Cart</button>
								<div className={(this.getQtyInCart(item.sku, this.props.cart) == 0 ? '' : 'have') + ' qty-in-cart'}>Qty in Cart: {this.getQtyInCart(item.sku, this.props.cart)}</div>
								<div className={(this.getQtyInOrders(item.sku, this.props.orders) == 0 ? '' : 'have') + ' qty-in-cart'}>Qty in Orders: {this.getQtyInOrders(item.sku, this.props.orders)}</div>
							</div>
							<div className="col-sm-4">
								<div className="message">		
									{this.dialog(item.messages)}								
								</div>
								<input id={"input-message-" + item.sku} className="input-message" placeholder="Message to Trade Depot" onKeyPress={this.handleSendPress} />
								<button id={"bn-send-" + item.sku} onClick={() => this.props.onSendClick(item.sku)}>Send</button>
							</div>
						</div>
					);
				});								
			}									
		}
		return (
			<div>
				<div>{products}</div>
				<div>{orders}</div>
			</div>
		);
	}
}