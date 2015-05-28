<?php

/**
 * Common Get Data From Form
 * @return array $form
 */
function get_form() {

    $form = array();

    if (getenv("REQUEST_METHOD") == "POST") {
        while  (list($name,  $value)  =  each($_POST)) {
            $form[$name]  =  utf8_encode(strip_tags ($value));
        }
    }
    else {
        $query_string  =  getenv("QUERY_STRING");
        $query_array  =  explode("&",  $query_string);
        while  (list($key,  $val)  =  each($query_array)) {
            list($name,  $value)  =  explode("=",  $val);
            $name  =  urldecode($name);
            $value  =  strip_tags (urldecode($value));
            $form[$name]  =  utf8_encode(htmlspecialchars($value));
        }
    }
    return $form;
}

/**
 * Setup server config based on HOSTNAME or FQDN
 * 
 * @return void
 */
function setupServer($fqdn_devel='1') {

	switch ($fqdn_devel) {
		//
		// PHP settings
		//
		case "1" :
			error_reporting ( E_ALL );
			ini_set ( "display_startup_errors", "1" );
			ini_set ( "display_errors", "1" );
			ini_set ( "html_errors", "1" );
			ini_set ( "log_errors", "On" );
			break;
		default :
			error_reporting ( E_ALL & ~ E_DEPRECATED & ~ E_STRICT );
			ini_set ( "display_startup_errors", "0" );
			ini_set ( "display_errors", "0" );
			ini_set ( "html_errors", "0" );
			ini_set ( "log_errors", "On" );
			break;
	}
}

/**
 * Return PDO:MySQL attributes
 */
function arr_pdo_attr() {
	$arr_pdo_attrs = array (
			PDO::ATTR_AUTOCOMMIT => true,
			PDO::ATTR_EMULATE_PREPARES => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_ORACLE_NULLS => true,
			PDO::ATTR_PERSISTENT => false,
			PDO::ATTR_PREFETCH => true,
			PDO::ATTR_TIMEOUT => 10,
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
	);
	return $arr_pdo_attrs;
}

/**
 * Return Site Protocol
 * return $protocol (http or https)
 */
function getSiteProtocol() {
	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	return $protocol;
}

/**
 * Return Site URL base
 * return $siteurl
 */
function getSiteURL() {
	$protocol 	= ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$domainName = $_SERVER['HTTP_HOST'];
	$siteurl	= $protocol.$domainName;
	return $siteurl;
}

/**
 * Common cryted_id
 * @param int $id
 * @return string crypted_id
 */
function get_crypted_id($id) {
	$crypted_id = SHA1(crypt($id * 1024, ENCRYPTION_KEY));
	return $crypted_id;
}

/**
 * Returns the file extension
 * @param string $filename
 * @return string
 */
function get_file_extension($filename='') {
	$arr_ext = explode('.',strtolower($filename) );
	return array_pop($arr_ext);
}

/**
 * Check the file extension
 * @param string $filename
 * @return string
 */
function check_file_extension_allowed($filename='',$arr_allowed=array()) {
	$file_extension = get_file_extension($filename);
	if ( in_array($file_extension, $arr_allowed) ) {
		return $file_extension;
	} else {
		return "";
	}
}

/**
 * Returns the mime_content_type
 * @param string $filename
 * @return string
 */
function get_mime_content_type($filename) {

	$mime_types = array(
			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',
			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',
			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',
			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',
			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',
			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',
			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
	);


	$ext = get_file_extension($filename);

	if (array_key_exists($ext, $mime_types)) {
		return $mime_types[$ext];
	}elseif (function_exists('finfo_open')) {
		$finfo = finfo_open(FILEINFO_MIME);
		$mimetype = finfo_file($finfo, $filename);
		finfo_close($finfo);
		return $mimetype;
	} else {
		return 'application/octet-stream';
	}
}

/**
 * Get BlobImgData
 * @return string
 */
function set_file_blob($fieldname='') {
	$res = array('error','none');
	$arr_extensions = explode("|", BADGES_IMAGE_ALLOWED_EXTENSIONS);
	$fileobject = (isset($_FILES[$fieldname]) ) ? $_FILES[$fieldname] : "";
	if ( $fileobject!='' && $fileobject['name']!="" && $fileobject['size']>0 )
	{
		$check_size 		= ( $fileobject['size'] <= BADGES_IMAGE_MAX_SIZE  ) ? '1' :'0';
		if ($check_size == '0') return array('error','size');
		$file_extension		= strtolower( get_file_extension($fileobject['name']) );
		$file_type			= get_mime_content_type($fileobject['name']);
		$check_extension 	= ( in_array($file_extension, $arr_extensions) ) ? '1' : '0';
		if ($check_extension == '0') return array('error','extension');
		if ( $check_size ==1 && $check_extension == 1 ) {
			return array('ok','badge_img'=>file_get_contents($fileobject['tmp_name']), 'badge_img_extension'=>$file_extension, 'badge_img_type'=>$file_type, 'badge_img_name'=>$fileobject['name'] );
		}
	}
	return $res;
}

/**
 * Get BlobImgData
 * @return string
 */
function show_file_blob($id='0',$type='') {
	switch ($type) {
		case "issuer": $tablename = "badges_issuers"; $field='badge_id'; break;
		case "earner": $tablename = "badges_earns"; $field='earn_id'; break;
		default: $tablename = ""; break;
	}
	$arr_data = array();
	if ( $id>0  && $tablename!='')
	{
		$res = COMMONDB_MODULE::get_value($tablename, 'badge_img', $id);
		$arr_data = COMMONDB_MODULE::get_arr_relations_lists_aliases($tablename,"$field,badge_img, badge_img_type,badge_img_name","WHERE $field='$id'","$field");
	}
	return $arr_data;
}


/**
 * Replace possible conflictive or syntax characters in the string
 * @param string $str
 * @return string $new_str
 */
function cleanup_string ($str) {
	$new_str = $str;
	// Strip HTML tags
	$new_str = strip_tags($new_str);
	// Strip slashes
	$new_str = stripslashes($new_str);
	// Strip ecaped strings
	$new_str = stripcslashes($new_str);
	// Strip ecaped strings
	$new_str = preg_replace("/'/", "´", $new_str);
	$new_str = preg_replace("/\"/","´", $new_str);
	// Replace duplicated spaces
	$new_str = preg_replace("/  /", " ", $new_str);
	// Replace duplicated \n chars
	$new_str = preg_replace("/\n\n/", "\n", $new_str);
	// Clean spaces beging and end
	$new_str = trim ( $new_str );
	return $new_str;
}

/**
 * Generate random seed
 * @param int $n
 * @return string $new_str
 */
function random_seed($n = 0) {
	$rseed = abs(intval($n)) % 9999999 + 1;
	return $rseed;
}

/**
 * Generate random value
 * @param string $str
 * @return string $new_str
 */
function random_num($rseed=0, $min = 0, $max = 9999999) {
	$rseed = ($rseed == 0) ? random_seed(mt_rand()) : $rseed;
	$rseed = ($rseed * 125) % 2796203;
	return $rseed % ($max - $min + 1) + $min;
}

/**
 * Generate random value
 * @param int $l ( size of returned string )
 * @param string $c (chars to be used)  
 * @param string $u (true or false  whether or not a character can appear beside itself )
 * @return string $s 
 */
function rand_chars($l=6, $c='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890',$u = FALSE) {
	if (!$u) for ($s = '', $i = 0, $z = strlen($c)-1; $i < $l; $x = rand(0,$z), $s .= $c{$x}, $i++);
	else for ($i = 0, $z = strlen($c)-1, $s = $c{rand(0,$z)}, $i = 1; $i != $l; $x = rand(0,$z), $s .= $c{$x}, $s = ($s{$i} == $s{$i-1} ? substr($s,0,-1) : $s), $i=strlen($s));
	return $s;
}

/**
 * Get path info
 * @param string $filename
 * @return array
 */
function get_current_page_info () {

	(string) $this_page   	= $_SERVER['PHP_SELF'];
	(array) $arr_page_info 	= ($this_page!="") ? pathinfo($this_page) : array();
	(array) $arr_dirname 	= array();
	(array) $arr_query		= array();
	(string)$mod_name		= "";
	(string)$mod_file		= "";
	(string)$mod_page 		= "";
	(string)$theme_page		= "";
	(string)$id				= "";
	(string)$token			= "";

	$arr_dirname	= explode("/", $arr_page_info['dirname']);
	$dirname_page  	= ( count($arr_dirname) > 0 ) ? $arr_dirname[(count($arr_dirname))-1] : "";
	$arr_query   	=  explode("?", $_SERVER['REQUEST_URI'] );
	$arr_vals_query	= ( is_array($arr_query) && count($arr_query) > 0 && array_key_exists(1,$arr_query) ) ? explode ( "&", $arr_query[1] ) : array();

	if ( count($arr_vals_query) > 0 ) {
		$max_arr_vals = ( count($arr_vals_query) <= MAX_QUERY_STRING_PROCESS ) ? count($arr_vals_query) : MAX_QUERY_STRING_PROCESS;
		for ($i = 0; $i < $max_arr_vals ; $i++) {
			$test_vals = ( explode ( "=", $arr_vals_query[$i] ) );
			if ( $test_vals[0] == 'page') $mod_page = $test_vals[1];
		}
	}

	$arr_page_info = array_merge($arr_page_info, array( 'dirname_page' => $dirname_page )  );
	return $arr_page_info;
}

/**
 * Validate url
 * @param string $url
 * @return boolean (1:ok , 0:ko)
 */
function validateURL($url){
	$regex = "|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i";
	if(preg_match("$regex", $url))
	{
		return 1;
	}
	return 0;
}	

/**
* trims text to a space then adds ellipses if desired
* @param string $input text to trim
* @param int $length in characters to trim to
* @param bool $ellipses if ellipses (...) are to be added
* @param bool $strip_html if html tags are to be stripped
* @return string
*/
function trim_text($input='', $length='0', $ellipses = true, $strip_html = true) {
	//strip tags, if desired
	if ($strip_html) {
		$input = strip_tags($input);
	}
	
	//no need to trim, already shorter than trim length
	if (strlen($input) <= $length || $length==0 ) {
		return $input;
	}
	
	//find last space within length
	$last_space = strrpos(substr($input, 0, $length), ' ');
	$trimmed_text = substr($input, 0, $last_space);
	
	//add ellipses (...)
	if ($ellipses) {
		$trimmed_text .= '...';
	}
	return $trimmed_text;
}

/**
 * Validate email syntax
 * @param string $email
 * @return string $valid_data
 */
function isValidateEmailSyntax($email = "") {
	(string) $valid_data  = "";
	(string) $test_data   = $email;

	if( strlen($test_data)>0 AND preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+[.]+[a-zA-Z0-9-.]+$/", $test_data) ) {
		$valid_data = $test_data;
	}
	return $valid_data;
}

/**
 * Get email hashed 
 * @param string $email
 * @param string $salt 
 * 		this version only supports sha256
 * @return string
 */
function hashEmailAddress($email='', $salt='sha256') {
	return ($email!='') ? "$salt$" . hash("$salt", $email . $salt) : "";
}


/**
 * Check if dir or file exists|writable
 * @param string $dir
 * @param string $type (writable or empty)
 * @return boolean (1: true | 0 :false)
 */
function check_system_dir_files($dir='',$perms='')
{
	$res = 0;
	if ( $dir !='' ) {
		switch ($perms) {
			case "writable":
				if ( is_file($dir) ) {
					$res = ( file_exists( $dir ) && is_writable($dir) ) ? 1 : 0;
				}  else {
					$res = ( is_dir($dir) && is_writable($dir) ) ? 1 : 0;
				}
				break;
					
			default:
				if ( is_file($dir) ) {
					$res = ( file_exists( $dir ) ) ? 1 : 0;
				} else {
					$res = ( is_dir($dir) ) ? 1 : 0;
				}
				break;
		}
	}
	return $res;
}

/**
 * Check valid json contents
 * @param string $jsondata
 * @return int as boolean (1: true | 0 :false)
 */
function is_json($jsondata) {
	$data = json_decode($jsondata);
	return (json_last_error() == JSON_ERROR_NONE) ? 1 : 0;
}

/**
 * Check json contents from given json file
 * @param string $jsonfile
 * @return int as boolean (1: true | 0 :false)
 */
function is_valid_file_json($jsonfile) {
	if ($jsonfile!="" && file_exists($jsonfile) ) {
		$data = file_get_contents($jsonfile, FILE_USE_INCLUDE_PATH );
		if ($data!='')
		{
			return is_json($data);
		}
	}
	return 0;
}

/**
 * Delete files
 * @param string $filetodelete (complete path)
 * @return void
 */
function delete_files($filetodelete=''){
	if ( $filetodelete!='' && is_file($filetodelete) && file_exists( $filetodelete ) ) 
	{
		unlink( $filetodelete );
	}
}

/**
 * Move files
 * @param string $filetomove (complete path)
 * @param string $filemoved (complete paht) 
 * @return void
 */
function move_files($filetomove='',$filemoved=''){
	if ( $filetomove!='' && is_file($filetomove) && $filemoved!='' && file_exists( $filetomove ) ) 
	{
		rename( $filetomove, $filemoved );
	}
}
?>