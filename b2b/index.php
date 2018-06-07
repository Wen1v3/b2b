<?php
require_once '../app/Mage.php';
umask(0);
Mage::app('default');

Mage::getSingleton('core/session', array('name' => 'frontend'));
$sessionCustomer = Mage::getSingleton("customer/session");
$myEmail = "";
$currentCus = null;
if ($sessionCustomer->isLoggedIn()) {
	$myEmail = $sessionCustomer->getCustomer()->getEmail();
	$currentCus = $sessionCustomer->getCustomer();
} else {
	echo "Please log in!";
	die();
}

$encoded = $_GET["key"];
$customer = $_GET["a"];

$shipping = $currentCus->getPrimaryShippingAddress();
//var_dump($shipping->getTelephone()); die();
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<title>Trade Depot B2B</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
	<script src="https://unpkg.com/react@15/dist/react.js"></script>
	<script src="https://unpkg.com/react-dom@15/dist/react-dom.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.23/browser.min.js"></script>
	
	<link rel="stylesheet" href="style.css" />
</head>
<body>
	<div id="root">
	</div>
	
	<script>
		function money(i) {
			//console.log(i.toLocaleString('de-DE'));
			var n = parseFloat(i);
			return "$" + "" + n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
		}
		
		function getQueryParams(qs) {
			qs = qs.split('+').join(' ');

			var params = {},
				tokens,
				re = /[?&]?([^=]+)=([^&]*)/g;

			while (tokens = re.exec(qs)) {
				params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
			}

			return params;
		}
	</script>

	<script type="text/babel">
		<?php require_once 'cart.php'; ?>
		
		<?php require_once 'side_bar.php'; ?>
		
		<?php require_once 'product_list.php'; ?>
		
		class B2b extends React.Component {
			constructor() {
				super();
				this.state = {
					categories: [],
					cTree: [],
					//categories: [{name:"c1",skus:["111111","222222"]},{name:"c2",skus:["333333"]}],
					orders: [],
					mid: "all",
					pMap: [],
					/*
					pMap: {"111111":{sku:"111111",price:"1.5",imgUrl:"http://la",name:"la la la"},
						"222222":{sku:"222222",price:"2.5",imgUrl:"http://sa",name:"sa sa sa"},
						"333333":{sku:"333333",price:"3.5",imgUrl:"http://an",name:"aa aa aa"}},
					*/
					cart: []
					//cart: [{sku:"111111",qty:1},{sku:"222222",qty:3}]
				};
				
				this.getMainData = this.getMainData.bind(this);
			}
			
			getMainData() {
				//console.log("getMainData");return;
				$("#loader").toggleClass("hide");
				var myUrl = 'http://www.tradedepot.co.nz/b2b/ajax/data.php?key=<?php echo $encoded; ?>&a=<?php echo $customer; ?>&mode=<?php echo $_GET["mode"]; ?>' 
					+ '&ram=' + Math.floor(Math.random() * 10001);
				return $.getJSON(myUrl).then((data) => {
					$("#loader").toggleClass("hide");
					var ps = [];
					for (var i = 0; i < data.categories.length; i++) {
						for (var j = 0; j < data.categories[i].products.length; j++) {
							ps.push(data.categories[i].products[j])	
						}
					}
					var productMap = []; 
					ps.forEach(function(e) { productMap[e.sku] = e; })
					
					var cs = [];
					for (var i = 0; i < data.categories.length; i++) {
						var c = {};
						c.name = data.categories[i].name;
						var skus = [];
						for (var j = 0; j < data.categories[i].products.length; j++) {
							skus.push(data.categories[i].products[j].sku)	
						}
						c.skus = skus;
						cs.push(c);
					}
					
					this.setState({pMap: productMap, categories: cs, orders: data.orders, cTree: data.cTree});
				});
			}
			
			componentDidMount() {
				//console.log("did");
				this.getMainData();
			}

			handleCategoryClick(e) {
				//console.log(e.target.id);
				this.setState({mid: e.target.id});
			}
			
			handleMenuClick(i) {
				//console.log("handleMenuClick: " + i);
				this.setState({mid: i});
			}
			
			handleSendClick(sku) {
				var inputId = '#input-message-' + sku;
				if ($(inputId).val() == 0) return;
				var message = $(inputId).val();
				$(inputId).val("");
				
				var myUrl = 'http://www.tradedepot.co.nz/b2b/ajax/insert_dialogue.php?from=<?php echo $myEmail; ?>&c=<?php echo $customer; ?>&sku=' 
					+ sku + '&message=' + message; 
					+ '&ram=' + Math.floor(Math.random() * 10001);
				$.getJSON(myUrl).then((data) => {
					this.getMainData();
				});
			}
			
			handleAddClick(sku) {
				var inputId = '#input-qty-' + sku;
				if ($(inputId).val() == 0) return;
				var qty = $(inputId).val();
				//$(inputId).val("");
				
				var newCart = jQuery.extend(true, [], this.state.cart);
				var result = $.grep(newCart, function(e){ return e.sku == sku; });
				if (result.length == 0) {
					newCart.push({
						sku: sku,
						qty: qty
					});
					this.setState({cart: newCart});
				} else {
					var exSku = result[0].sku;
					for (var i = 0; i < newCart.length; i++) {
						if (newCart[i].sku == exSku) {
							newCart[i].qty = Number(qty) + Number(newCart[i].qty);
						}
					}
					//console.log(this.state.cart);
					//console.log(newCart);
					this.setState({cart: newCart});
				}				
			}
			
			handleArrivedClick(oid) {
				var urlArray = getQueryParams(window.location.href);	
				var mode = urlArray["mode"];
				if (mode == "sup" || mode == "td-seller") return;
				
				var myUrl = 'http://dataw.tradedepot.co.nz/b2b/set_order_status.php?status=1&ref=' + oid;
				$.getJSON(myUrl).then((data) => {
					this.getMainData();
				});
			}
			
			handleCartDeleteClick(id) {
				var newCart = [];
				for (var i = 0; i < this.state.cart.length; i++) {
					if (i == id) continue;
					newCart.push(this.state.cart[i]);
				}
				this.setState({cart: newCart});
			}
			
			handleCartQtyChange(i) {
				var inputId = '#input-cart-qty-' + i;
				var qty = $(inputId).val();
				$(inputId).val("");
							
				var newCart = jQuery.extend(true, [], this.state.cart);
				newCart[i].qty = qty;
				this.setState({cart: newCart});
			}
			
			handleCreateOrder() {
				var cart = [];
				for (var i = 0; i < this.state.cart.length; i++) {
					cart.push({sku: this.state.cart[i].sku, rid: this.state.pMap[this.state.cart[i].sku].rid, qty: this.state.cart[i].qty, price: this.state.pMap[this.state.cart[i].sku].price});
				}

				$.post("http://dataw.tradedepot.co.nz/b2b/b2b.php", {cart:cart, sender:'<?php echo $_GET["a"]; ?>', note:$("#note").val(), PONumber:$("#PONumber").val()}).then((data) => {
					$('#order-fb').html(data);
					this.getMainData();
				});
			}
			
			cartGo() {
				//console.log("cartGo");
				$("#cart").toggle("slide");
			}
				  
			render() {
				//console.log("b2b render");
				return (
					<div className="container-fluid">
						<div className="row" id="head">
							<img id="logo" src="./img/logo-b2b.png" />
						</div>
						<div id="fix">
							<div onClick={this.cartGo}><a id="mini-title">Mini Cart & Checkout (Open & Close)</a></div>
							<div id="cart" className="collapse">
								<Cart
									categories={this.state.categories}
									cart={this.state.cart}
									pMap={this.state.pMap}
									onCartDeleteClick={(i) => this.handleCartDeleteClick(i)}
									onCartQtyChange={(i) => this.handleCartQtyChange(i)}
									onCreateOrder={() => this.handleCreateOrder()}
									reload={() => this.getMainData()}
								/>
							</div>
						</div>
						<div id="fix-refresh">
							<button onClick={this.getMainData}>Refresh Dialogue</button>
						</div>
						<div className="row">
							<div id="side-bar" className="col-sm-2">
								<SideBar
									categories={this.state.categories}
									cTree={this.state.cTree}
									mid={this.state.mid}
									onMenuClick={(i) => this.handleMenuClick(i)}
									onCategoryClick={(e) => this.handleCategoryClick(e)}
								/>
							</div>
							<div id="product-list" className="col-sm-10">
								<div id="loader" className="loader hide"></div>
								<ProductList
									mid={this.state.mid}
									categories={this.state.categories}
									orders={this.state.orders}
									pMap={this.state.pMap}
									cart={this.state.cart}
									onSendClick={(sku) => this.handleSendClick(sku)}
									onAddClick={(sku) => this.handleAddClick(sku)}
									onArrivedClick={(oid) => this.handleArrivedClick(oid)}
								/>
							</div>
						</div>
					</div>
				);
			}
		}

		ReactDOM.render(
			<B2b />,
			document.getElementById('root')
		);
	</script>
</body>
</html>