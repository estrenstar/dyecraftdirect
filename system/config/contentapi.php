<?php
// Cron Route
$cron_route = 'extension/feed/contentapi/cron';

// Site
$_['site_url']           = HTTP_SERVER;
$_['site_ssl']           = HTTPS_SERVER;

// Url
$_['url_autostart']      = false;

// Database
$_['db_autostart']       = true;
$_['db_engine']          = DB_DRIVER; // mpdo, mysqli or pgsql
$_['db_hostname']        = DB_HOSTNAME;
$_['db_username']        = DB_USERNAME;
$_['db_password']        = DB_PASSWORD;
$_['db_database']        = DB_DATABASE;
$_['db_port']            = DB_PORT;

// Session
$_['session_autostart']  = false;
$_['session_engine']     = 'db';
$_['session_name']       = 'OCSESSID';

// Autoload Libraries
$_['library_autoload']   = array();

// SEO URL
$registry = new Registry(); 
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);
$query = $db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE store_id = '0' AND `key`='config_seo_url_type'");
if ($query->num_rows) {
	$seo_type = $query->row['value'];
} else {
	$seo_type = 'seo_url';
}

// Actions
$_['action_pre_action']  = array(
	'startup/session',
	'startup/startup',
	'startup/error',
	'startup/event',
	'startup/maintenance',
	'startup/'.$seo_type
);

// Action Events
$_['action_event'] = array(
	'controller/*/before' => array(
		'event/language/before'
	),
	'controller/*/after' => array(
		'event/language/after'
	),	
	'view/*/before' => array(
		500  => 'event/theme',
		998  => 'event/language',
	),
	'language/*/after' => array(
		'event/translation'
	)
);

// Action list
$_['action_error'] = 'error/not_found';
$_['action_default'] = $cron_route;
$_['action_router'] = $cron_route;