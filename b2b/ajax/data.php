<?php
require_once '../../app/Mage.php';
umask(0);
Mage::app('default');

require_once '../class/dialogue.php';

ini_set("allow_url_fopen", 1);
$apiData = file_get_contents("http://dataw.tradedepot.co.nz/v2/controllers/api/inventory/listAll.php");
$apiData = json_decode($apiData, true);

function findProductBySku($sku) {
	global $apiData;
	foreach ($apiData as $product) {
		if ($product["sku"] == $sku) {
			return $product;
		}
	}
}


$key = "555";
$encoded = $_GET["key"];
$decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encoded), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
//echo $decoded;
//if ($decoded != "wen.zhou.nz@gmail.com") die();

Mage::getSingleton('core/session', array('name' => 'frontend'));
$sessionCustomer = Mage::getSingleton("customer/session");
$sEmail = "";
$currentCus = null;
if ($sessionCustomer->isLoggedIn()) {
	$sEmail = $sessionCustomer->getCustomer()->getEmail();
	$currentCus = $sessionCustomer->getCustomer();
} else {
	echo "Please log in!";
	die();
}

$customer = Mage::getModel("customer/customer"); 
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId()); 
if ($sEmail == "b2b@tradedepot.com") {
	$customer->loadByEmail($_GET["a"]);
} else {
	$customer->loadByEmail($sEmail);
}

$orderEmail = $_GET["a"];
if ($_GET["mode"] == "td-buyer" || $_GET["mode"] == "sup") {
	$orderEmail = "b2b@tradedepot.com";
}

$homepage = file_get_contents('http://dataw.tradedepot.co.nz/b2b/get_range_by_account.php?email=' . $_GET["a"]);
$homepage = json_decode($homepage);

$orders = file_get_contents('http://dataw.tradedepot.co.nz/b2b/get_orders_by_account.php?email=' . $_GET["a"]);
$orders = json_decode($orders);

$cTree = file_get_contents('http://dataw.tradedepot.co.nz/b2b/get_category_tree.php?email=' . $customer->getEmail());
$cTree = json_decode($cTree);

//$string = "c1:11032,11040,11050,11065,11080,11240,11250,11340,11350,13150,13632,13640,13650,13732,13740,13750,13765,13780,14065,15050,71090,73690,81015,81020,81032,81050,81215,81232,81315,81320,81332,81350,83715,91104,91105,91315,91505,100323,100326,100406,100503,100506,100656,100803,100806,110100,110150,127100,129100,137100,137150,140100,150100,159100,165128,168100,210050,210051,210052,210053,210106,210107,210109,210112,210115,210201,210202,210203,210206;c2:210207,210209,210212,210310,210350,210351,210390,210391,210395,210396,210397,210398,210399,210410,210412,210480,210490,210906,210907,210909,210912,211101,211102,211106,211107,211108,211109,211112,211113,211119,211191,211192,211193,211194,211206;c3:211207,211209,211212,211340,211375,211376,211377,211378,211390,211391,211392,211393,211412,211413,211501,211502,211503,211504,211505,211556,211557,211605,211607,211609,211610,211611,211612,211613,211614,211615,211616,211617,211701,211702,211703,211704,211705,211706,211707,211708,211709,211710,211711,211712,211713,211714,211715,211716,211717,211718;c4:211719,211720,211906,211907,211909,211912,211916,212006,212007,212009,212012,212016,212017,212019,212026,212027,212029,212039,212050,212051,212052,212053,212112,212115,212122,212125,212212,212350,212351,212605,212705,212905,212906,212907,212909,212912,214542,214543,214544,214545,214546,215101,215102,215103,215104,215105,215106,215107,215108,215201,215202,215203,215204,215205,215206,215207,215208,215209,215210,215211,215212,215213,215214,215215,215216,215217,215218,215219,215220,215221,215222,215301,215302,215303;c5:215304,215305,215306,215307,215308,215309,215310,215311,215312,215314,218801,218802,218803,218804,218805,218806,218807,218808,218809,218810,218811,218812,218813,218814,218815,218816,218817,218818,218819,218820,218821,218822,218823,218824,218825,218826,218827,218828,218829,218830,218831,218832,218833,218834,218835,218836,218837,218838,218839,218840,218841,218842,218843,218901,218902,218903,218904,218905,218906,218907,218908,218909,218910,218911,218912,218913,218914,218915,218916,218917,218918,218919,218920,218921,218922,218923,218924,218925,218926,218927,218928,218929,218930,218931,218932,218933,218934,218935,218936,218937,218938,218939,218940,218941,218942,218943,218944,218945,218946,218947,218948,218949,218950,218951,218952,218953,221010,221011,221012,221013,221020,221021,221022,221023,221024,221025,221090,221091,221093,221094,221095,221096,221097,221510,221511,221512,221513,221519,221520,221525,221527,221590,221591,221592,221593,221594,221595,221596,221607,222010,222011,222012,222018,222090,222091,222218,222510,222512,222590,222593,222901,222902,223010,223013,223015,223016,223021,223025,223090,223091,223092,223093,223095,223097,223510,223511,223512,223517,223520,223521,223590,223591,223593,223595,223596,223597,224010,224012,224090,224095,224141,224149,224150,225012,225014,225030,225045;c6:225070,225075,225082,225083,225084,225090,225093,225099,225100,225101,225120,225820,225900,225901,229001,229002,229003,230100,230101,230102,230103,230104,230105,230106,230107,230108,230109,230110,230111,230112,230113,230114,230115,230116,230117,230118,230119,230120,230121,230122,230123,230124,230125,230126,230127,230128,230129,230130,230131,230132,230133,230135,230136,230137,230140,230141,230142,230145,233031,233100,233106,233110,233114,233118,233124,233130,233131,233134,234100,234110,234130,235001,235002,235003,235004,235005,235130,235501,235502,235503,235504,235505,235506,235507,235508,235509,235510,235511,235512,235513,235910,235912,236101,236102,236103,236104,236105,236106,236109,238001,239109,239150,239151,239152,239153,239154,239155,239156,239157,239158,239159,239160,239161,239162,239163,239164,239165,239166,239167,239168,239169,239170,239171,239172,239173,239174,239175,239176,239177,239178,239180,239181,241100,241101,241105,241110,241115,241120,241125,241130,241135,241140,241145,241150,241155,241160,241165,241170,241175,241180,241185,241190,241195,241200,241205,241210,241215,241220,241225,241250,241255,241260,241265,241270,241275,241300,241305,241310,241315,241320,241325,241335,241340,241341,241350,241351,241355,241360,241365,241370,241374,241380,241381";

$categories = array();
foreach ($homepage as $c) {
	$products = array();
	foreach ($c->products as $p) {
		$productObj = Mage::getModel('catalog/product')->loadByAttribute('sku', $p->sku);
		//var_dump($productObj); die();
		if ($productObj == null) continue;
		
		$productData = findProductBySku($p->sku);
		
		$qHand = $productData['qty'];//Mage::getResourceModel('catalog/product')->getAttributeRawValue($productObj->getId(), 'reckon_stock', Mage_Core_Model_App::ADMIN_STORE_ID);
		$qOrder = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productObj->getId(), 'reckon_q_on_order', Mage_Core_Model_App::ADMIN_STORE_ID);
		$qSalesOrder = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productObj->getId(), 'reckon_q_on_sales_order', Mage_Core_Model_App::ADMIN_STORE_ID);
		$eta = $productData['eta'];//Mage::getResourceModel('catalog/product')->getAttributeRawValue($productObj->getId(), 'eta', Mage_Core_Model_App::ADMIN_STORE_ID);
		$rid = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productObj->getId(), 'reckon_id', Mage_Core_Model_App::ADMIN_STORE_ID);
		//$desc = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productObj->getId(), 'description', Mage_Core_Model_App::ADMIN_STORE_ID);
		$desc = $productObj->getShortDescription();
		if ($qHand == null) $qHand = 0;
		if ($qOrder == null) $qOrder = 0;
		if ($qSalesOrder == null) $qSalesOrder = 0;
		$pieces = explode(" ", $eta);
		$pieces = explode("-", $pieces[0]);
		$eta = $pieces[2] . "/" . $pieces[1];
		
		$product = new StdClass;
		$product->sku = $p->sku;
		$product->name = $productObj->getName();
		if ($p->price != null) {
			$product->price = $p->price;
		} else {
			$product->price = $productObj->getFinalPrice();
		}
		$product->is_tiered_price = $p->is_tiered_price;
		$product->cost1 = $p->cost1;
		$product->cost2 = $p->cost2;
		$product->cost3 = $p->cost3;
		$product->cbm = $p->cube_metre;
		$product->box_qty = $p->box_quantity;
		$product->imgUrl = "http://www.tradedepot.co.nz/media/catalog/product/" . $productObj->getThumbnail();
		$product->url = "http://www.tradedepot.co.nz/catalog/product/view/id/" . $productObj->getId();
		$product->soh = $qHand;
		$product->soo = $qOrder;
		$product->eta = $eta;
		$product->rid = $rid;
		$product->desc = $desc;
		$product->reorder_qty = $p->reorder_qty;
		$product->reorder_notes = $p->reorder_notes;
		
		$d = new Dialogue();
		$product->messages = $d->get($p->sku, $sEmail);
		//var_dump($productObj); die();

		array_push($products, $product);
	}

	$category = new StdClass;
	$category->name = $c->name;
	$category->products = $products;

	array_push($categories, $category);
}

$data = new StdClass;
$data->orders = $orders;
$data->categories = $categories;
$data->cTree = $cTree;

$data = json_encode($data, true);
echo $data;
?>