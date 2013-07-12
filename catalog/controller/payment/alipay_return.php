<?php 
// Configuration
$configFile = str_replace('catalog/controller/payment','',dirname(__FILE__)).'config.php';

if (file_exists($configFile)) {
	require_once($configFile);
} 

// Install 
if (!defined('DIR_APPLICATION')) {
	//header('Location: install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Application Classes
require_once(DIR_SYSTEM . 'library/customer.php');
require_once(DIR_SYSTEM . 'library/affiliate.php');
require_once(DIR_SYSTEM . 'library/currency.php');
require_once(DIR_SYSTEM . 'library/tax.php');
require_once(DIR_SYSTEM . 'library/weight.php');
require_once(DIR_SYSTEM . 'library/length.php');
require_once(DIR_SYSTEM . 'library/cart.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

// Database 
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

// Store
if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
	$store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`ssl`, 'www.', '') = '" . $db->escape('https://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
} else {
	$store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`url`, 'www.', '') = '" . $db->escape('http://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
}

if ($store_query->num_rows) {
	$config->set('config_store_id', $store_query->row['store_id']);
} else {
	$config->set('config_store_id', 0);
}
		
// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0' OR store_id = '" . (int)$config->get('config_store_id') . "' ORDER BY store_id ASC");

foreach ($query->rows as $setting) {
	if (!$setting['serialized']) {
		$config->set($setting['key'], $setting['value']);
	} else {
		$config->set($setting['key'], unserialize($setting['value']));
	}
}

if (!$store_query->num_rows) {
	$config->set('config_url', HTTP_SERVER);
	$config->set('config_ssl', HTTPS_SERVER);	
}

// Url
$url = new Url($config->get('config_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));	
$registry->set('url', $url);

// Log 
$log = new Log($config->get('config_error_filename'));
$registry->set('log', $log);

function error_handler($errno, $errstr, $errfile, $errline) {
	global $log, $config;
	
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			break;
		default:
			$error = 'Unknown';
			break;
	}
		
	if ($config->get('config_error_display')) {
		echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
	}
	
	if ($config->get('config_error_log')) {
		$log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
}
	
// Error Handler
set_error_handler('error_handler');

// Request
$request = new Request();
$registry->set('request', $request);
 
// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response); 
		
// Cache
$cache = new Cache();
$registry->set('cache', $cache); 
// Language Detection
$languages = array();

$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE status = '1'"); 

foreach ($query->rows as $result) {
	$languages[$result['code']] = $result;
}

$detect = '';

if (isset($request->server['HTTP_ACCEPT_LANGUAGE']) && $request->server['HTTP_ACCEPT_LANGUAGE']) { 
	$browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);
	
	foreach ($browser_languages as $browser_language) {
		foreach ($languages as $key => $value) {
			if ($value['status']) {
				$locale = explode(',', $value['locale']);

				if (in_array($browser_language, $locale)) {
					$detect = $key;
				}
			}
		}
	}
}

if (isset($session->data['language']) && array_key_exists($session->data['language'], $languages) && $languages[$session->data['language']]['status']) {
	$code = $session->data['language'];
} elseif (isset($request->cookie['language']) && array_key_exists($request->cookie['language'], $languages) && $languages[$request->cookie['language']]['status']) {
	$code = $request->cookie['language'];
} elseif ($detect) {
	$code = $detect;
} else {
	$code = $config->get('config_language');
}

//if (!isset($session->data['language']) || $session->data['language'] != $code) {
//	$session->data['language'] = $code;
//}
//
//if (!isset($request->cookie['language']) || $request->cookie['language'] != $code) {	  
//	setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $request->server['HTTP_HOST']);
//}			

$config->set('config_language_id', $languages[$code]['language_id']);
$config->set('config_language', $languages[$code]['code']);

// Language	
$language = new Language($languages[$code]['directory']);
$language->load($languages[$code]['filename']);	
$registry->set('language', $language); 

// Document
$registry->set('document', new Document()); 		

// Customer
$registry->set('customer', new Customer($registry));

// Affiliate
$registry->set('affiliate', new Affiliate($registry));

if (isset($request->get['tracking'])) {
	setcookie('tracking', $request->get['tracking'], time() + 3600 * 24 * 1000, '/');
}
		
// Currency
$registry->set('currency', new Currency($registry));

// Tax
$registry->set('tax', new Tax($registry));

// Weight
$registry->set('weight', new Weight($registry));

// Length
$registry->set('length', new Length($registry));

// Cart
$registry->set('cart', new Cart($registry));

// Encryption
$registry->set('encryption', new Encryption($config->get('config_encryption')));


if (isset($_GET['out_trade_no'])) 
{
	$order_id = $_GET['out_trade_no'];
}
 else 
{
	$order_id = 0;
}

$success_flag = false;

$loader->model('checkout/order');

$order_info = $loader->model_checkout_order->getOrder($order_id);

if($order_info)
{	
	//verify success
	$loader->load->model('payment/alipay');
	$alipay_config['sign_type'] = strtoupper('MD5');
	$alipay_config['partner'] = $config->get('alipay_merchant');
	$alipay_config['key'] = $config->get('alipay_signature');
	$alipay_config['cacert'] = getcwd().'/cacert.pem';
	$alipay_config['transport'] = "http";
			
	$loader->model_payment_alipay->init($alipay_config);
			
	$verify_result = $loader->model_payment_alipay->verifyReturn();
	
	if($verify_result)
	{
		//verify success
		
		$success_flag = true;

		$trade_no = $_GET['trade_no'];

		$trade_status = $_GET['trade_status'];
		
		if($trade_status == 'WAIT_SELLER_SEND_GOODS')
		{
			$order_status_id = $config->get("alipay_wait_seller_send_goods_status_id");
		}
		else if($trade_status == 'TRADE_FINISHED')
		{
			$order_status_id = $config->get("alipay_trade_finished_status_id");
		}
		else 
		{
			$order_status_id = $config->get("alipay_wait_seller_send_goods_status_id");
		}
				
	}
	else
	{
		$success_flag = false;
		//verify fail
		$order_status_id = $config->get("alipay_trade_error_status_id");
	}
	
	
	if (!$order_info['order_status_id']) 
	{
		$loader->model_checkout_order->confirm($order_id, $order_status_id);
	} 
	else 
	{
		$loader->model_checkout_order->update($order_id, $order_status_id);
	}
}



if($success_flag)
{
	echo "<script type=\"text/javascript\">".
		"location = '" . $loader->url->link('checkout/success')."';"	.
	"</script>"; 
}
else
{
	//header('Location: '. $loader->url->link('checkout/success'));
	echo "<script type=\"text/javascript\">".
		"location = '" . $loader->url->link('checkout/fail')."';"	.
	"</script>"; 
}

?>