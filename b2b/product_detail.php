<?php
require_once '../app/Mage.php';
umask(0);
Mage::app('default');

$sku = $_GET["sku"];
$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
$product = Mage::getModel('catalog/product')->load($product->getId());

$desc = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'description', Mage_Core_Model_App::ADMIN_STORE_ID);
$sd = $product->getShortDescription();
$name = $product->getName();

foreach ($product->getMediaGalleryImages() as $image) {
	//echo Mage::helper('catalog/image')->init($product, 'image', $image->getFile())->resize($600, 600);
	//echo $image->getUrl();
}
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
	
	<link rel="stylesheet" href="style.css" />
</head>
<body>
	<div className="container-fluid">
		<div className="row" id="head">
			<img id="logo" src="./img/logo-b2b.png" />
		</div>
		<div class="container">
			<div class="col-sm-3">
				<?php	
				foreach ($product->getMediaGalleryImages() as $image) {
					?>
					<image style="cursor:pointer; cursor:hand;" onclick="jQuery('#modalImg').attr('src', '<?php echo $image->getUrl(); ?>')" 
						data-toggle="modal" data-target="#myModal" 
						class="img-responsive" src="<?php echo $image->getUrl(); ?>">
					<?php
				}
				?>
				
				<div id="myModal" class="modal fade" role="dialog">
					<div class="modal-dialog" style="width:100%">
						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-body">
								<image id="modalImg" src="" class="img-responsive" style="margin:0 auto"/>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-9">
				<h1><?php echo $name; ?></h1>
				<h3><?php echo $sd; ?></h3>
				<?php echo $desc; ?>
			</div>
		</div>
	</div>
</body>
</html>