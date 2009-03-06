<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package  Payment
 *
 * Settings related to the Payment library.
 * This file has settings for each driver.
 * You should copy the 'default' and the specific
 * driver you are working with to your application/config/payment.php file.
 *
 * Options:
 *  driver - default driver to use
 *  test_mode - Turn TEST MODE on or off
 *  curl_settings - Set any custom cURL settings here. These defaults usualy work well.
 *                  see http://us.php.net/manual/en/function.curl-setopt.php for details
 */
$config['default'] = array
(
	'driver'        => 'Paypal',
	'test_mode'     => TRUE,
	'curl_config'   => array(CURLOPT_HEADER         => FALSE,
	                         CURLOPT_RETURNTRANSFER => TRUE,
	                         CURLOPT_SSL_VERIFYPEER => FALSE)
);

/**
 * PayPal Options:
 *  API_UserName - the username to use
 *  API_Password - the password to use
 *  API_Signature - the api signature to use
 *  ReturnUrl - the URL to send the user to after they login with paypal
 *  CANCELURL - the URL to send the user to if they cancel the paypal transaction
 *  CURRENCYCODE - the Currency Code to to the transactions in (What do you want to get paid in?)
 */
$config['Paypal'] = array
(
	'API_UserName' => 'seller_1210674130_biz_api1.googlemail.com',
	'API_Password' => 'WMLCAWQDRDUEU6CN',
	'API_Signature' => 'AxU9rXkOy6tnSXOVN6IK69-1DOGqATM60kxnHhZ-xbj3hx5kJvk4UFUJ',
	'ReturnUrl' => 'payment/return_test/',
	'CANCELURL' => 'payment/cancel/',
	'CURRENCYCODE' => 'GBP'
);