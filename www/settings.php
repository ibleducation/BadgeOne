<?php 
/** -- IMPORTANT! do not change the order --- **/

//
// APP : BADGES SETTINGS
//

//App: Badges Local Path
if ( !defined("APP_GENERAL_REPO") ) { define ('APP_GENERAL_REPO',"files"); }
define ('APP_GENERAL_REPO_TEMPLATES',APP_GENERAL_REPO."/templates");
define ('APP_GENERAL_REPO_BADGES',APP_GENERAL_REPO."/badges");
define ('APP_GENERAL_REPO_BADGES_ISSUER_LOCAL',APP_GENERAL_REPO_BADGES."/issuers");
define ('APP_GENERAL_REPO_BADGES_EARN_LOCAL',APP_GENERAL_REPO_BADGES."/earns");
define ('APP_GENERAL_REPO_BADGES_IMG_LOCAL',APP_GENERAL_REPO_BADGES."/images");
define ('APP_GENERAL_REPO_BADGES_REVOKED_LOCAL',APP_GENERAL_REPO_BADGES."/revoked");

//App: Badges Templates
define ('APP_BADGES_TEMPLATE_BADGE_ISSUER',APP_GENERAL_REPO_TEMPLATES."/issuer.json");
define ('APP_BADGES_TEMPLATE_BADGE_ASSERTION',APP_GENERAL_REPO_TEMPLATES."/assertion.json");
define ('APP_BADGES_TEMPLATE_BADGE_CLASS',APP_GENERAL_REPO_TEMPLATES."/badgeclass.json");

//App: Badges Remote Path
define ('APP_GENERAL_REPO_BADGES_ISSUER_REMOTE',SERVER_HTTP_HOST."/".str_replace("../","",APP_GENERAL_REPO_BADGES)."/issuers");
define ('APP_GENERAL_REPO_BADGES_EARN_REMOTE',SERVER_HTTP_HOST."/".str_replace("../","",APP_GENERAL_REPO_BADGES)."/earns");
define ('APP_GENERAL_REPO_BADGES_IMG_REMOTE',SERVER_HTTP_HOST."/".str_replace("../","",APP_GENERAL_REPO_BADGES)."/images");
define ('APP_GENERAL_REPO_BADGES_REVOKED_REMOTE',SERVER_HTTP_HOST."/".str_replace("../","",APP_GENERAL_REPO_BADGES)."/revoked");

//App: Badges Assertion Params

//Prefix to create assertion files 
//DO NOT CHANGE when the system is released, do not remove the separation char (-) and the extension json
//DO NOT use the same prefix set in badges_class
define ('BADGES_ASSERTION_PREFIX_JSON_FILES','-'.'bassert'.'.json');
//DO NOT CHANGE de path unless the source code path is set to another page
define ("BADGES_ASSERTION_BADGE_ISSUER_PATH_REMOTE_IMAGE",SERVER_HTTP_HOST."/fileearn.php?bgid=");

define ('BADGES_ASSERTION_VERIFY_METHOD',"hosted");
define ('BADGES_ASSERTION_CONTEXT','https://w3id.org/openbadges/v1');
define ('BADGES_ASSERTION_TYPE',"Assertion");
define ('BADGES_ASSERTION_IDENTITY_TYPE',"email"); //currently the only supported value is 'email'
define ('BADGES_ASSERTION_HASHED',"true"); // required boolean 'true' or 'false'
if (defined('BADGES_ASSERTION_HASHED') && BADGES_ASSERTION_HASHED=='true') {
	define ('BADGES_ASSERTION_SALT',"sha256"); //DO NOT CHANGE. this version only supports sha256 
} else {
	define ('BADGES_ASSERTION_SALT',"");
}

//App: Badges Issuer Institution
define ('BADGES_ISSUER_CONTEXT','https://w3id.org/openbadges/v1');
define ('BADGES_ISSUER_INSTITUTION_TYPE','IssuerOrg'); //old documentation 'Issuer'
define ('BADGES_ISSUER_INSTITUTION_FILE_ID',"badgeone.json");
define ('BADGES_ISSUER_INSTITUTION_ID',APP_GENERAL_REPO_BADGES_ISSUER_REMOTE."/".BADGES_ISSUER_INSTITUTION_FILE_ID);
define ('BADGES_ISSUER_INSTITUTION_NAME','IBLStudios Institution'); //required : obi specs
define ('BADGES_ISSUER_INSTITUTION_URL','http://badgeone.com'); //required : obi specs
define ('BADGES_ISSUER_INSTITUTION_DESC',''); //not used in json constructor. do not change and do not remove on this version
define ('BADGES_ISSUER_INSTITUTION_IMAGE',''); //not used in json constructor. do not change and do not remove on this version
define ('BADGES_ISSUER_INSTITUTION_EMAIL','user@email.local');
define ('BADGES_ISSUER_INSTITUTION_REVOCATION_LIST',''); //not needed for hosted version

//App: Badges Class Params

//Prefix to create badgesclass files
// DO NOT CHANGE when the system is released, do not remove the separation char (-) and the extension json
// DO NOT use the same prefix set in badges_assertion

define ('BADGES_CLASS_PREFIX_JSON_FILES','-'.'bclass'.'.json');
define ('BADGES_CLASS_CONTEXT','https://w3id.org/openbadges/v1');
define ('BADGES_CLASS_TYPE','BadgeClass');
define ('BADGES_CLASS_ISSUER',BADGES_ISSUER_INSTITUTION_ID);

//App: Badges Assertion IMGS
define ('BADGES_ASSERTION_IMAGE_PREFIX','-'.'img'.'.png');

/** -- IMPORTANT! do not change the order --- **/
?>
