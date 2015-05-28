<?php
//
// Params : config
require_once __DIR__.'/config.php';

$dsn_engine	= DB_ENGINE;
$dsn_host 	= DB_HOST;
$dsn_dbname = DB_NAME;
$dsn_dbuser = DB_USER;
$dsn_dbpwd 	= DB_PASS;

// Setup Server Oauth Connection
$dsn      = "$dsn_engine:dbname=$dsn_dbname;host=$dsn_host";
$username = "$dsn_dbuser";
$password = "$dsn_dbpwd";

// debug reporting (uncomment this to debug errors)
//ini_set('display_errors',1);error_reporting(E_ALL);

// Autoloading (composer is preferred, but for this example let's just do this)
require_once('../OAuth2/Autoloader.php');
OAuth2\Autoloader::register();

// $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

// Pass a storage object or array of storage objects to the OAuth2 server class
$server = new OAuth2\Server($storage);

// Add the "Client Credentials" grant type (it is the simplest of the grant types)
$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

// Add the "Authorization Code" grant type (this is where the oauth magic happens)
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
?>
