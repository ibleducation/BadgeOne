<?php
/***
 *  Badges : OBI base on latest documentation 
 *  Dec 2013: https://github.com/mozilla/openbadges-specification/blob/master/Assertion/latest.md
 *  May 2015: https://openbadgespec.org/
 * 
 */


//
// Set HOSTNAME
//
define("HOSTNAME", php_uname('n'));

//
// Default timezone
//
date_default_timezone_set('UTC');

//
// Setup FQDN
//
(string) $fqdn = "";
$fqdn = (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : "";
$fqdn = (empty($fqdn)) ? $_SERVER['SERVER_NAME'] : "";
$fqdn_devel = (preg_match('/.test$/', $fqdn)) ? '1' : '0';
define("FQDN", $fqdn);

//
// FS and path settings
//
define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']."");
define("DOCUMENT_HTTP", $_SERVER['SERVER_NAME']."");
define("PATH_INCLUDES", "includes");

//
// Define extra include paths
//
set_include_path ( get_include_path () . PATH_SEPARATOR . PATH_INCLUDES );
set_include_path ( get_include_path () . PATH_SEPARATOR . "../".PATH_INCLUDES );
set_include_path ( get_include_path () . PATH_SEPARATOR . "../../".PATH_INCLUDES );
//print get_include_path ();

//
// Autoload libraries
//

/**
 * Autoload Functions
*/
function myClasses($class) {
	$classFile = strtolower ( $class ) . '.class.php';
	require_once ($classFile);
}

function myExtensions($class) {
	$classFile = strtolower ( $class ) . '.extend.class.php';
	require_once ($classFile);
}

/**
 * nullify any existing autoloads and define mine
 */
spl_autoload_register ( null, false );
spl_autoload_register ( 'myClasses', true );
spl_autoload_register ( 'myExtensions', true );

/**
 * specify extensions that may be loaded
*/
spl_autoload_extensions ( '.php, .class.php, .extend.class.php' );

//
// Database settings
//
define("DB_ENGINE", "mysql");
define("DB_HOST", "localhost");
define("DB_PORT", 3306 );
define("DB_NAME", "iblstudiosbadges");
define("DB_USER", "usr_iblbadges");
define("DB_PASS", "FIiQiLfAGkOrU@1Z3x");
define("DB_PREFIX", "");
define('ENCRYPTION_KEY', 'S9kv9034kLAU0338dh2rfSFW3'); //DO NOT CHANTE when system is released (salt to create hashed files and unique badge_id)

//
// PDO tweaks
//
define ("PDO_DSN", sprintf("mysql:host=%s;port=%d;dbname=%s", DB_HOST, DB_PORT, DB_NAME));

//
// Let's load common functions : not classes
//
// constans for functions
define ("MAX_QUERY_STRING_PROCESS",5); //page info
require_once("functions.php");

//
// Setup APP SERVER : based on HOSTNAME or FQDN
//
setupServer($fqdn_devel);
define ('SERVER_HTTP_PROTOCOL', getSiteProtocol() ); //returns http or https
define ('SERVER_HTTP_HOST', getSiteURL() );

//
// Constants APP
//

//App: Cache
define("NOCACHE", sha1(microtime()));

//App : Info Settings
define ('APP_NAME', "IBLStudiosBadges");
define ('APP_OWNER', "IBL");
define ('APP_VERSION', "v.0.1");
define ('APP_PREFIX', "[iblstudiosbadges]"); //used in composed emails if needed
define ('APP_USER_MIN_CHARS_PWD', 5); //min chars for user passwords

//App: General Options
define ('APP_ALLOW_NOCONFIRM_REGISTRATION',1); //1: allows direct registration instead email confirmation 
define ('APP_EMAIL',"user@email");

//App: Badges App Params
define ('BADGES_PARAMS_NUM_MAX',1); //do not change : OBI specs : 1 evidence -> type URL
define ('BADGES_IMAGE_MAX_SIZE',1000000); //in bytes
define ('BADGES_IMAGE_ALLOWED_EXTENSIONS','png|jpg'); //separated by |

//
// APP : BADGES SETTINGS
// 
include('settings.php');

//
// Current page :Evaluation
//
$arr_current_page_info  = get_current_page_info () ;
$current_page			= ( isset($arr_current_page_info['basename']) && $arr_current_page_info['basename']!='' ) ? $arr_current_page_info['basename'] : 'index.php';
define ("CURRENT_PAGE", $current_page);

//
// SESSION START
//
session_start();

//
// Setup DB Connection
//
try {
	$dbh = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
} catch(PDOException $e) {
    echo ( $fqdn_devel==1 ) ?  "ERROR: " . $e->getMessage() : "";
}

//
// Setup Auth
// segcheck, si tiene UID y SEED valida esta logueado, si tiene UID y semilla invalida a logout, si no tiene UID pasa de su culo

if(isset($_COOKIE["UID"]) && isset($_COOKIE["SEED"]) && $_COOKIE["UID"]!='' && $_COOKIE["SEED"]!=''){

	$sdata = array($_COOKIE["UID"],$_COOKIE["SEED"]);
    $stmt = $dbh->prepare("SELECT * FROM users WHERE id_user = ? and seed = ? and activated=1 LIMIT 1");
    $stmt->execute($sdata);
    $yosoy = $stmt->fetch();
	
    if(!$stmt->rowCount()){
        header("Location: ./logout.php");
    } else {
    	// Logged user info
    	$logged_user 	 = $yosoy['id_user'];
    	$logged_user_name= $yosoy['name'];
    	$logged_profile  = $yosoy['profile'];
    }
}
?>
