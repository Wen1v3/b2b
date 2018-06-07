<?php
require_once '../class/dialogue.php';

//$encoded = $_GET["key"];
//$categories = json_encode($categories, true);

$d = new Dialogue();
var_dump($d->get("100431", "wen.zhou.nz@gmail.com"));
?>