class Cart extends React.Component {
	sumPrice() {
		var sum = 0;
		for (var i = 0; i < this.props.cart.length; i++) {
			sum += this.props.pMap[this.props.cart[i].sku].price * this.props.cart[i].qty;
		}
		return money(sum);
	}

	sumCbm() {
		var sum = 0;
		for (var i = 0; i < this.props.cart.length; i++) {
			sum += this.props.pMap[this.props.cart[i].sku].cbm * this.props.cart[i].qty;
		}
		return Math.round(sum * 100) / 100;
	}
	
	render() {
		//console.log("ProductList");
		var body = null;
		if (this.props.cart != null) {
			body = this.props.cart.map((item, i) => {
				return (														
					<tr key={i}>
						<td><img src={this.props.pMap[item.sku].imgUrl} /></td>
						<td>{item.sku}</td>
						<td>{this.props.pMap[item.sku].name}</td>
						<td>
							{item.qty}
							<input id={"input-cart-qty-" + i} className="input-cart-qty" />
							<span onClick={() => this.props.onCartQtyChange(i)}>R</span>
						</td>
						<td>{money(this.props.pMap[item.sku].price)}</td>
						<td>{money(item.qty * this.props.pMap[item.sku].price)}</td>
						<td>{this.props.pMap[item.sku].cbm}</td>
						<td>{Math.round(item.qty * this.props.pMap[item.sku].cbm * 100) / 100}</td>
						<td><div onClick={() => this.props.onCartDeleteClick(i)}>X</div></td>
					</tr>
				);
			});
		}
		
		var phone = <?php echo "'" . $shipping->getTelephone() . "'"?>;
		
		//console.log(body);
		return (
			<div>
				<table className="table">
					<thead>
						<tr>
							<th></th>
							<th>Sku</th>
							<th>Name</th>
							<th>Qty</th>
							<th>Price</th>
							<th></th>
							<th>Cbm</th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>Total:</td>
							<td>{this.sumPrice()}</td>
							<td></td>
							<td>{this.sumCbm()}</td>
						</tr>
					</tfoot>
					<tbody>
						{body}
					</tbody>
				</table>
				<div className="ilabel">My Name:</div>
				<div><?php echo $currentCus->getName(); ?></div>
				<div className="ilabel">My Email:</div>
				<div><?php echo $currentCus->getEmail(); ?></div>
				<div className="ilabel">My Shipping Phone:</div>
				<div>{phone}</div>
				<div className="ilabel">My Shipping Address:</div>
				<div><?php echo $shipping->getStreet()[0]; ?></div>
				<div><?php echo $shipping->getStreet()[1]; ?></div>
				<div><?php echo $shipping->getCity(); ?></div>
				<div><textarea id="note" placeholder="notes and instructions"/></div>
				<div><input id="PONumber" placeholder="Please enter your PO#"/></div>
				<button onClick={() => this.props.onCreateOrder()}>Place Order</button>
				<span id="order-fb"></span>
			</div>
		);
	}
}