<?php
/**
 * COMMONDB_MODULE is an extension of COMMONDB_MODULES class
 * @param int $id
 * @return object
 */
class COMMONDB_MODULE extends COMMONDB_MODULES
{

	public $crypted_id = null;

	 /**
	 * COMMONDB_MODULES constructor
	 * @param string $tablename
	 * @param int $obj_int
	 * @return object COMMONDB_MODULES
	 */
	public function __construct($tablename,$id)
	{
		parent::__construct($tablename,$id);

		//
		// Build crypted id.
		// Needs ENCRYPTION_KEY to be alreay defined.
		// Add: define( ENCRYPTION_KEY, "YourSecretPassphaseHere" );
		//
		(string) $primary_key = UTILS_COMMON::get_table_primary_key_field($tablename);
				 $primary_key = ($primary_key=='') ? UTILS_COMMON::get_table_primary_key_field_view($tablename) : $primary_key;
		$this->crypted_id = SHA1(crypt($this->$primary_key * 1024, ENCRYPTION_KEY));
	}

	/**
	 * Decrypt an encrypted user_id
	 * @param string $token (is the crypted user_id)
	 * @return int $decrypted_id
	 */
	public static function decrypt_id($tablename, $token)
	{

		(string) $decrypted_id = "";
		(string) $primary_key = UTILS_COMMON::get_table_primary_key_field($tablename);
				 $primary_key = ($primary_key=='') ? UTILS_COMMON::get_table_primary_key_field_view($tablename) : $primary_key;
		
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "SELECT $primary_key FROM ".DB_NAME.".$tablename WHERE SHA1(encrypt($primary_key * 1024, \"".ENCRYPTION_KEY."\" ) ) = \"".$token."\" LIMIT 1";
			$stmt = $db->prepare($q);
			$stmt->execute();

			if($stmt->rowCount()>0)
			{
				$rs = $stmt->fetch(PDO::FETCH_OBJ);
				$decrypted_id = $rs->$primary_key;
			}


		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		return $decrypted_id;
	}
	
	
	/**
	 * Get array relations with alias
	 * @param string $tablename
	 * @param string $fields
	 * @param string $where_compare
	 * @param string $arr_key
	 * @return array $arr_res
	 */
	public static function get_arr_relations_lists_aliases ($tablename='', $fields='', $where_compare='', $arr_key=''){
		(int) $arr_res = array();
		if ($tablename!='' && $fields!='' && $where_compare!='')
		{
			try {
				$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
				$q = "SELECT $fields FROM ".DB_NAME.".$tablename $where_compare";
				$stmt = $db->prepare($q);
				$stmt->execute();

				if( $stmt->rowCount() > 0 )
				{
					$i=0;
					while ($rs = $stmt->fetch(PDO::FETCH_OBJ))
					{
						foreach (explode(",", $fields) AS $field)
						{
							$field = ( preg_match('/AS /', $field) ) ? explode("AS", $field) : $field;
							$field = (is_array($field)) ? $field[1] : $field;
							$field = trim($field);
							
							$field_dateformat = ( preg_match('/^DATE_FORMAT/', $field, $matches ) ) ? "dateformat" : "";
							if ( $field_dateformat =='' ) 
							{
								if ($arr_key!=""){
									$arr_res[$rs->$arr_key][$field] = $rs->$field;
								} else {
									$arr_res[$i][$field] = $rs->$field;
								}								
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
	 * Launch direct query
	 * @param string $query
	 * @return void $res
	 */
	public static function launch_direct_system_query ($query=''){
		if ($query!='' )
		{
			try {
				$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
				$q = "$query";
				$stmt = $db->prepare($q);
				$stmt->execute();

			} catch (PDOException $e) {
				print "Error!: " . $e->getMessage() . "<br/>";
				die();
			}
		}
	}

	/**
	 * Launch direct query
	 * @param string $query
	 * @return int $lastInsertId
	 */
	public static function launch_direct_system_query_get_lastId ($query=''){
		(int) $lastInsertId = 0;
		if ($query!='' )
		{
			try {
				$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
				$q = "$query";
				$stmt = $db->prepare($q);
				$stmt->execute();
				$lastInsertId = $db->lastInsertId();
			} catch (PDOException $e) {
				print "Error!: " . $e->getMessage() . "<br/>";
				die();
			}
		}
		return $lastInsertId;
	}	
}
?>