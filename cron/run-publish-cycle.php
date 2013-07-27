<?php
define('_PS_ADMIN_DIR_', getcwd());
require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../library/codebird-php-2.4.1/src/codebird.php');

// Get parameters from database
$consumer_key = Configuration::get('PLTOTWITTER_CONSUMER_KEY');
$consumer_secret = Configuration::get('PLTOTWITTER_CONSUMER_SECRET');
$access_token = Configuration::get('PLTOTWITTER_ACCESS_TOKEN');
$access_token_secret = Configuration::get('PLTOTWITTER_ACCESS_TOKEN_SECRET');
$product_limit = Configuration::get('PLTOTWITTER_LIMIT');
$default_language_id = Configuration::get('PS_LANG_DEFAULT');
$shop_domain = Configuration::get('PS_SHOP_DOMAIN');
$default_currency_id = Configuration::get('PS_CURRENCY_DEFAULT');

// Validate parameters
if(!$consumer_key || !$consumer_secret || !$product_limit || !$access_token || !$access_token_secret) {
    error_log(__FILE__." - Not all module configuration parameters have been set!");
    exit(0);
}

if(!$default_language_id) {
    error_log(__FILE__." - Could not retrieve the default language for the shop");
    exit(0);
}

if(!$shop_domain) {
    error_log(__FILE__." - Could not retrieve the domain for the shop");
    exit(0);
}

if(!$default_currency_id) {
    error_log(__FILE__." - Could not retrieve the default currency for the shop");
    exit(0);
}

// Set consumer key and secret
\Codebird\Codebird::setConsumerKey($consumer_key, $consumer_secret);

// Get an instance
$codebird_instance = \Codebird\Codebird::getInstance();

// Set access token and secret
$codebird_instance->setToken($access_token, $access_token_secret);

try {
    
    // Get relevant product(s)
    $sql = new DbQuery();
    $sql->select('*');
    $sql->from('product', 'a');
    $sql->innerJoin('product_lang', 'b', 'b.id_product = a.id_product');
    $sql->where('a.active = 1');
    $sql->where('b.id_lang = '.$default_language_id);
    $sql->orderBy('RAND()');
    $sql->limit($product_limit);
    $results =  Db::getInstance()->executeS($sql);
    
    if(!$results) {
        error_log(__FILE__." - No products could be found for the specified conditions");
        exit(0);
    }
    
    $currency = new Currency($default_currency_id);
    
    foreach($results as $arr_record_set) {        
        $status_message = Product::getProductName($arr_record_set['id_product'],null,$default_language_id).
                ' - '.str_replace(' ','',$currency->getSign('left')).
                number_format(Product::getPriceStatic($arr_record_set['id_product'],true,null,2),2).
                ' - http://'.$shop_domain.'/'.$arr_record_set['id_product'].'-'.$arr_record_set['link_rewrite'].'.html';
        
        $reply = $codebird_instance->statuses_update('status='.$status_message);        
    }
    error_log('Product publishing complete');
} catch(Exception $e) {
    error_log(__FILE__.'-'.$e->getMessage());
    exit(0);
}






