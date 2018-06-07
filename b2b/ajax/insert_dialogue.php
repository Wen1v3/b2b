<?php
require_once '../class/dialogue.php';

$d = new Dialogue();
if ($_GET["from"] != "b2b@tradedepot.com") {
	$d->insert($_GET["from"], "b2b@tradedepot.com", $_GET["sku"], $_GET["message"]);
} else {
	$d->insert("b2b@tradedepot.com", $_GET["c"], $_GET["sku"], $_GET["message"]);
}

// Sending  Email to notify SalesRep
$sender    = $_GET["from"];
$sent_from = "sales@tradedepot.com";
$reply_to  = $sender;
$sent_to   = "david@tradedepot.com";
$cc_to     = "alex@tradedepot.com, swati@tradedepot.com, scott@tradedepot.com";
$Bcc_to    = "jack@tradedepot.com";
$subject   = "Notice: A new B2B Message!!";
$message = "===============================<br><br>Hello,<br>A new B2B Message has been post:<br><br>[SKU]  " . $_GET["sku"] . "<br>[Customer]  " . $sender . "<br>[Message]  " . $_GET["message"] . "<br>===============================<br>            Tradedepot.com";
$message = str_replace("&#10;","<br>",$message);

$headers = "From: " . $sent_from . "\r\n" . "Reply-To: " . $reply_to ."\r\n" . "Cc: " . $cc_to . "\r\n" . "Bcc: " . $Bcc_to . "\r\n"; 
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
$ok = @mail($sent_to, $subject, $message, $headers, "-f " . $sent_from);
// End of Sending 

$categories = [1, 2, 3];
$categories = json_encode($categories, true);
echo $categories;
//echo "It is sent!".
?>