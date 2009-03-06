<?php

$alpaca_seller = new Alpacaseller_Model;

$alpacas = $alpaca_seller->get_alpacas();

echo "<ul id=\"alpacas\">\r\n";
foreach ($alpacas as $alpaca)
{
	$imgurl = ($alpaca['image_thumb'] == NULL) ? '/images/alpaca_placeholder.png' : $alpaca['image_thumb'];
	echo "\t" . '<li>';
	echo '<img src="' . $imgurl . '" alt="">';
	echo $alpaca['name'] . ' - &pound;' . $alpaca['price'] . '<br>';
	echo '<a href="http://www.alpacaseller.com/' . $alpaca['details'] . '">View Details</a>';
	echo '</li>' . "\r\n";
}
echo "</ul>\r\n";

?>