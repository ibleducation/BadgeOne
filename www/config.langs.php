<?php
/**
 *
 * @license public under the GNU GPL.
 * $IdProject: IBLOpenBadges-Server $
  * @package multilang
 * 
 * Support for multilang server
 * using php-gettext
 *
 */

// Set PHP getText Path from your SO: required php-gettext
define ( "APP_PHP_GETTEXT_PATH", "/usr/share/php/php-gettext/gettext.inc" );

// Default Constants
define ( "PATH_LOCALE", "locale" );
define ( "CURRENT_CHARSET", "utf-8" );
define ( "DEFAULT_LANGUAGE", "en" );
define ( "DEFAULT_LOCALE", "en_EN" );
define ( "SESSION_GROUP_NAME_APP", "app" );

// Setup session languages
if (! isset ( $_SESSION [SESSION_GROUP_NAME_APP] ["lang"] )) {
	$_SESSION [SESSION_GROUP_NAME_APP] ["lang"] = DEFAULT_LANGUAGE;
	$_SESSION [SESSION_GROUP_NAME_APP] ["locale"] = DEFAULT_LOCALE;
}

// List of available languages
$available_languages = array (
		'en' => array (
				'locale' => 'en_EN',
				'name' => 'English',
				'flag' => 'img/flags/us.png' 
		),
		'es' => array (
				'locale' => 'es_ES',
				'name' => 'Castellano',
				'flag' => 'img/flags/es.png' 
		) 
);

// App: Set Multilanguages or Change Language
if (isset ( $_GET ["lang"] ) && $_GET ["lang"] != '' && array_key_exists( $_GET ["lang"], $available_languages )) {
	if (isset ( $available_languages [$_GET ["lang"]] ["locale"] ) && $available_languages [$_GET ["lang"]] ["locale"] != '' && is_dir ( PATH_LOCALE . "/" . $available_languages [$_GET ["lang"]] ["locale"] )) {
		$_SESSION [SESSION_GROUP_NAME_APP] ["lang"] = $_GET ["lang"];
		$_SESSION [SESSION_GROUP_NAME_APP] ["locale"] = $available_languages [$_GET ["lang"]] ["locale"];
	}
}

// Localization
require (APP_PHP_GETTEXT_PATH);
$encoding = CURRENT_CHARSET;
$domain = $_SESSION [SESSION_GROUP_NAME_APP] ["locale"];
$locale = $_SESSION [SESSION_GROUP_NAME_APP] ["locale"];
// uncomment this lines if the server is running under windows
// putenv("LANG=" . $locale);
// putenv("LANGUAGE=" . $locale);
_setlocale ( LC_MESSAGES, $locale );
_bindtextdomain ( $domain, "./" . PATH_LOCALE . "" );
_bind_textdomain_codeset ( $domain, $encoding );
_textdomain ( $domain );
?>