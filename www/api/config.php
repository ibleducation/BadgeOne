<?php
//
// Set HOSTNAME
//
define("HOSTNAME", php_uname('n'));

//
// Defaultsettings
//
date_default_timezone_set('UTC');
ini_set ( "display_startup_errors", "0" );
ini_set ( "display_errors", "1" );
ini_set ( "html_errors", "0" );
ini_set ( "log_errors", "On" );

//
// FS and path settings
//
define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']."");
define("DOCUMENT_HTTP", $_SERVER['SERVER_NAME']."");
define("DOCUMENT_HTTP_PORT", $_SERVER['HTTP_HOST']."");
define("PROTOCOL_HTTP", "http://"); //http or https
define ("PATH_IMG_BADGE",PROTOCOL_HTTP.DOCUMENT_HTTP_PORT."/filebadge.php?bgid=");
define ("PATH_IMG_EARN",PROTOCOL_HTTP.DOCUMENT_HTTP_PORT."/filebadge.php?eaid=");
define("PATH_EARN_BADGE", PROTOCOL_HTTP.DOCUMENT_HTTP_PORT."/view_badge_earn.php?badge_id=");

//
// Database settings
//
define("DB_ENGINE", "mysql");
define("DB_HOST", "localhost");
define("DB_PORT", 3306 );
define("DB_NAME", "badgeone");
define("DB_USER", "usr_iblbadges");
define("DB_PASS", "FIiQiLfAGkOrU@1Z3x");
define("DB_PREFIX", "");
define('ENCRYPTION_KEY', 'S9kv9034kLAU0338dh2rfSFW3');

// PDO setup
define ("PDO_DSN", sprintf("mysql:host=%s;port=%d;dbname=%s", DB_HOST, DB_PORT, DB_NAME));

/*--- DO NOT CHANGE THIS ORDER --*/

//PDO:MySQL attributes
$arr_pdo_attrs = array (
	PDO::ATTR_AUTOCOMMIT => true,
    PDO::ATTR_EMULATE_PREPARES => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_PERSISTENT => false,
    PDO::ATTR_PREFETCH => true,
    PDO::ATTR_TIMEOUT => 10,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    );

//
// Load libraries - relative to server path
//
require_once ('../includes/commondb_module.class.php');
require_once ('../includes/commondb_module.extend.class.php');
require_once ('../includes/utils_common.class.php');
require_once ('../includes/ibl_openbadges.class.php');

//include functions
require 'functions.common.php';

//define badges server http_host
define ('SERVER_HTTP_HOST', getSiteURL() );

//include badges settings - relative to server path
define ('APP_GENERAL_REPO',"../files");
include ( '../settings.php' );
?>
