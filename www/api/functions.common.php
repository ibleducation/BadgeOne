<?php 
/**
 * Functions common
 */

/**
 * Setup PDO attributes
 * @return array $arr_pdo_attrs
 */
function arr_pdo_attr() {
	$arr_pdo_attrs = array (
		PDO::ATTR_AUTOCOMMIT => true,
	    PDO::ATTR_EMULATE_PREPARES => true,
	    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	    PDO::ATTR_PERSISTENT => false,
	    PDO::ATTR_PREFETCH => true,
	    PDO::ATTR_TIMEOUT => 10,
	    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    );
	return $arr_pdo_attrs;
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
 * Get array relations with alias
 * @param string $tablename
 * @param string $fields 
 * @param string $where_compare
 * @param string $arr_key  
 * @return array $arr_res
 */
function get_arr_data_from_db ($tablename='', $fields='', $where_compare='', $arr_key='' ){
	(int) $arr_res = array();
	if ($tablename!='' && $fields!='' && $where_compare!='')
	{
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr() );
			$q = "SELECT $fields FROM ".DB_NAME.".$tablename $where_compare";
			//print "<div class='sql'>$q</div>\n";
			$stmt = $db->prepare($q);
			$stmt->execute();

			if( $stmt->rowCount() > 0 )
			{
				$i=0;
				while ($rs = $stmt->fetch(PDO::FETCH_OBJ))
				{
					foreach (explode(",", $fields) AS $field){
							
						$field = ( preg_match('/AS /', $field) ) ? explode("AS", $field) : $field;
						$field = (is_array($field)) ? $field[1] : $field;
						$field = trim($field);
							
						if ($arr_key!=""){
							$arr_res[$rs->$arr_key][$field] = $rs->$field;
						} else {
							$arr_res[$i][$field] = $rs->$field;
						}

					}

					$i+=1;
				}
			}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	return $arr_res;
}


/**
 * Get a single value for the given field from the database
 * @param string $field_name The name of the table field
 * @param int $application_score_group_id The record identifier (PRIMARY KEY is ussually given here)
 * @return string $field_value The value retrived from the database
 */
function get_selected_value($tablename,$field_name, $filter)
{
	(string) $field_value = "";

	try {
		$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
		$q = "SELECT $field_name FROM ".DB_NAME.".$tablename $filter LIMIT 1";
		//print "<div class='sql'>$q</div>\n";
		$stmt = $db->prepare($q);
		$stmt->execute();

		if($stmt->rowCount()>0)
		{
			$rs = $stmt->fetch(PDO::FETCH_OBJ);
			$field_value = $rs->$field_name;
		}

	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}

	// Return the selected value
	return $field_value;
}


/**
 * Set the given value on the given field in the database
 * @param string $field_name The name of field to be updated
 * @param string $new_value The new value for the given field
 * @param string $where_query The record identifier (PRIMARY KEY is ussually given here)
 * @param int $specialhtml substitute mysql_real_escape_string
 * @return void This method just execute the UPDATE statement using the given params
 */
function set_selected_value($tablename, $field_name, $new_value, $where_query, $specialhtml='0')
{
	try {
		$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
		if ( $specialhtml == '1' ) {
			$q = "UPDATE ".DB_NAME.".$tablename SET `$field_name`=\"".$db->quote($new_value)."\" $where_query LIMIT 1";
		} else {
			$q = "UPDATE ".DB_NAME.".$tablename SET `$field_name`=\"".$new_value."\" $where_query LIMIT 1";
		}
		//print "<div class='sql'>$q</div>\n";
		$stmt = $db->prepare($q);
		$stmt->execute();

	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}

}


/**
 * Launch direct query
 * @param string $query
 * @return boolean $res
 */
function launch_direct_system_query ($query=''){
	(int) $res = 0;
	if ($query!='' )
	{
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "$query";
			//print "<div class='sql'>$q</div>\n";
			$stmt = $db->prepare($q);
			$stmt->execute();

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	return $res;
}


/**
 * Launch direct query and get lastid
 * @param string $query
 * @return int $res
 */
function launch_direct_system_query_get_lastId ($query=''){
	(int) $res = 0;
	if ($query!='' )
	{
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "$query";
			//print "<div class='sql'>$q</div>\n";
			$stmt = $db->prepare($q);
			$stmt->execute();
			$res = $db->lastInsertId();
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	return $res;
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
        // Replace \r to \n
        $new_str = preg_replace("/\r/", "\n", $new_str);
	// Replace duplicated \n chars
	$new_str = preg_replace("/\n\n/", "\n", $new_str);
	// Clean spaces beging and end
	$new_str = trim ( $new_str );
	return $new_str;
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
?>
