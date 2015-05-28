<?php
class UTILS_COMMON {
	
	/**
	* Get table field list
	* @param string $tablename
	* @return array $arr_table_field_list
	*/
	public static function get_table_field_list($tablename) {
		(array) $arr_table_field_list = array();
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY FROM information_schema.columns WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME='$tablename' ORDER BY ORDINAL_POSITION";
			$stmt = $db->prepare($q);
			$stmt->execute();
		
			if( $stmt->rowCount() > 0 )
			{
				while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
					$arr_table_field_list [] = array('column'=>$rs->COLUMN_NAME, 'type'=>$rs->DATA_TYPE, 'key'=>$rs->COLUMN_KEY);
				}
			}
		
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		return $arr_table_field_list;
	}
	
	/**
	* Get table primary key field (if it's guessable)
	* @param string $tablename
	* @return string $primary_key_name
	*/
	public static function get_table_primary_key_field($tablename) {
	
		(string) $primary_key_name = "";
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "SHOW KEYS FROM $tablename WHERE Key_name ='PRIMARY'";
			$stmt = $db->prepare($q);
			$stmt->execute();

			if($stmt->rowCount()>0)
			{
				$rs = $stmt->fetch(PDO::FETCH_OBJ);
				$primary_key_name = $rs->Column_name;
			}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		return $primary_key_name;
	}


	/**
	* Get table primary key field (if it's guessable)
	* @param string $tablename
	* @return string $primary_key_name
	*/
	public static function get_table_primary_key_field_view($tablename) {

		(string) $primary_key_name = "";
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "SELECT COLUMN_NAME, DATA_TYPE FROM information_schema.columns WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME='$tablename' ORDER BY ORDINAL_POSITION LIMIT 1";
			$stmt = $db->prepare($q);
			$stmt->execute();

			if($stmt->rowCount()>0)
			{
				$rs = $stmt->fetch(PDO::FETCH_OBJ);
				$primary_key_name = $rs->COLUMN_NAME;
			}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		return $primary_key_name;
	}
	

	/**
	 * Array human readable printing utility.
	 * @param array $data
	 * @return void
	 */
	public static function pre ($data) {
		print "<div class='utils_html'>\n";
		print "<pre>\n";
		print_r($data);
		print "</pre>\n";
		print "</div>\n";
	}	
}
?>