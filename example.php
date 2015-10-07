<html><head><meta charset=utf-8></head><body>
<?php
require_once('vatValidation.class.php');
$vatValidation = new vatValidation( array('debug' => false));


//if($vatValidation->check('BE', '0828639227')) {
if($vatValidation->checkFull('BE0828639227')) {
	echo '<h1>valid one!</h1>';
	echo 'name: ' . $vatValidation->getName(). '<br/>';
	echo 'address: ' . $vatValidation->getAddress(). '<br/>';
} else {
	echo '<h1>Invalid VAT</h1>';
}
?></body></html>
