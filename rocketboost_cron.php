<?php
chdir(__DIR__);

require_once('config.php');
$domain = parse_url(HTTP_SERVER);
$host = $domain['host'];
//putenv('SERVER_NAME='.$host);
$_SERVER['SERVER_NAME'] = $host;
$_SERVER['REQUEST_METHOD'] = 'GET';

if (!isset($_SERVER['SERVER_PROTOCOL'])) {
  $_SERVER['SERVER_PROTOCOL'] = '';
}

if (!isset($_SERVER['REMOTE_ADDR'])) {
  $_SERVER['REMOTE_ADDR'] = '';
}

if (!isset($_SERVER['SERVER_PORT'])) {
  $_SERVER['SERVER_PORT'] = 80;
}

if (!empty($argv)) {
  array_shift($argv);
  parse_str(implode('&', $argv), $_GET);
}

define('GKD_BOOST_CRON', true);

$_GET['route'] = 'tool/boost/cron';

require_once('index.php');