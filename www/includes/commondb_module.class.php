<?php
/**
 * Class to interact with data stored in any table DB
 */
class COMMONDB_MODULES
{
	/**
	 * table name
	 */
	public $tablename	= null;
	/**
	 * id
	 */
	public $id = null;

	/**
	 * COMMONDB_MODULES constructor
	 * @param string $tablename
	 * @param int $id
	 * @return object COMMONDB_MODULES
	 */
	public function __construct($tablename, $id)
	{
		(string) $primary_key = UTILS_COMMON::get_table_primary_key_field($tablename);
				 $primary_key = ($primary_key=='') ? UTILS_COMMON::get_table_primary_key_field_view($tablename) : $primary_key;
		(array) $arr_fields   = UTILS_COMMON::get_table_field_list($tablename);
		(string)$list_fields  = "";
		
		if ( is_array($arr_fields) && count($arr_fields) > 0 ){
			foreach ( $arr_fields AS $key => $data ) {
				$field = $data['column'];
				$list_fields .= "`$field`,";
			}
			
			$list_select_fields = substr($list_fields, 0 , -1 );
			
			if ( $list_select_fields !="" ) {
				try {
					$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
					$q = "SELECT $list_select_fields FROM ".DB_NAME.".$tablename WHERE $primary_key='$id' LIMIT 1";
					$stmt = $db->prepare($q);
					$stmt->execute();
				
					$this->tablename = $tablename;
					$this->id = $id;
					
					$rs = $stmt->fetch(PDO::FETCH_ASSOC);

					foreach ( $arr_fields AS $key => $data )
					{
						$field = $data['column'];
						$type  = $data['type'];

						$this->$field = ($type == 'int' && $type == 'bigint' ) ? intval($rs[$field]) : $rs[$field];
					}
				
				} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
					die();
				}
			}
		}
	}

	/**
	 * Get a full list of values from the guiven table
	 * and returns them in an array
	 *
	 *  Usage:
	 *  	get_list();
	 * 			returns $arr_list[$primary_key] = $value;
	 *
	 *  	get_list( "WHERE fieldname=1 LIMIT 1", "column1", "column2" );
	 *  		returns $arr_list[column1] = column2;
	 *  
	 * @param string $tablename
	 * @param string $filterquery
	 * @param string $key_field
	 * @param string $value_field
	 * @return array $arr_list
	 */
	public static function get_list( $tablename='', $filterquery='', $key_field='', $value_field='')
	{
		(string) $primary_key = UTILS_COMMON::get_table_primary_key_field($tablename);
				 $primary_key = ($primary_key=='') ? UTILS_COMMON::get_table_primary_key_field_view($tablename) : $primary_key;

		(string) $key_field = ( $value_field =="" && $key_field =='' ) ? $primary_key : $key_field;
		(array)  $arr_list = array();
		(string) $q_select = ( $value_field !="" ) ? "$key_field,$value_field" : "$key_field";
		
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "SELECT $q_select FROM ".DB_NAME.".$tablename $filterquery";
			$stmt = $db->prepare($q);
			$stmt->execute();

			if( $stmt->rowCount() > 0 )
			{
				while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
					if ( $value_field !="" ) {
						$arr_list [$rs->$key_field] = $rs->$value_field;
					} else {
						$arr_list [] = $rs->$primary_key;
					}
				}
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		// Return the array containing the selected set
		return $arr_list;
	}

	/**
	 * Get a single value for the given field from the database
	 * @param string $tablename
	 * @param string $field_name The name of the table field
	 * @param int $id The record identifier (PRIMARY KEY is ussually given here)
	 * @return string $field_value The value retrived from the database
	 */
	public static function get_value($tablename,$field_name, $id)
	{
		(string) $field_value = "";
		(string) $primary_key = UTILS_COMMON::get_table_primary_key_field($tablename);
				 $primary_key = ($primary_key=='') ? UTILS_COMMON::get_table_primary_key_field_view($tablename) : $primary_key;

		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "SELECT $field_name FROM ".DB_NAME.".$tablename WHERE $primary_key = '$id' LIMIT 1";
			$stmt = $db->prepare($q);
			$stmt->execute();

			if($stmt->rowCount()>0)
			{
				$rs = $stmt->fetch(PDO::FETCH_OBJ);

				//alias
				$field_name = ( preg_match('/AS /', $field_name) ) ? explode("AS", $field_name) : $field_name;
				$field_name = (is_array($field_name)) ? $field_name[1] : $field_name;
				$field_name = trim($field_name);
				
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
	* Get a single value for the given field from the database
	* @param string $tablename 
	* @param string $field_name The name of the table field
	* @param int $id The record identifier (PRIMARY KEY is ussually given here)
	* @return string $field_value The value retrived from the database
	*/
	public static function get_selected_value($tablename,$field_name, $filter)
	{
		(string) $field_value = "";

		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "SELECT $field_name FROM ".DB_NAME.".$tablename $filter LIMIT 1";
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
	 * Get a total count for the given field and where_query
	 *
	 * Usage:
	 *    count_values('colum1', ' WHERE column3=1 LIMIT 1 ');
	 *    count_values(); -> returns the total rows using primary_key field
	 *
	 * @param string $tablename
	 * @param string $field_name the fieldname
	 * @param string $filterquery
	 * @return int $field_counter The total number from resulting query
	 */
	public static function count_values($tablename='', $field_name='', $filterquery='')
	{
		(string) $field_counter = 0;
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "SELECT COUNT($field_name) AS total FROM ".DB_NAME.".$tablename $filterquery ";
			//print "<div class='sql'>$q</div>\n";
			$stmt = $db->prepare($q);
			$stmt->execute();

			if($stmt->rowCount()>0)
			{
				$rs = $stmt->fetch(PDO::FETCH_OBJ);
				$field_counter = $rs->total;
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		// Return the selected value
		return $field_counter;
	}

	/**
	 * Set the given value on the given field in the database
	 * @param string $tablename 
	 * @param string $field_name The name of field to be updated
	 * @param string $new_value The new value for the given field
	 * @param int $id The record identifier (PRIMARY KEY is ussually given here)
	 * @param int $specialhtml substitute mysql_real_escape_string
	 * @return void This method just execute the UPDATE statement using the given params
	 */
	public static function set_value($tablename, $field_name, $new_value, $id, $specialhtml='0')
	{
		(string) $primary_key = UTILS_COMMON::get_table_primary_key_field($tablename);
				 $primary_key = ($primary_key=='') ? UTILS_COMMON::get_table_primary_key_field_view($tablename) : $primary_key;
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			if ( $specialhtml == '1' ) {
				$q = "UPDATE ".DB_NAME.".$tablename SET `$field_name`=\"".$db->quote($new_value)."\" WHERE $primary_key = '$id' LIMIT 1";
			} else {
				$q = "UPDATE ".DB_NAME.".$tablename SET `$field_name`=\"".$new_value."\" WHERE $primary_key = '$id' LIMIT 1";
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
	 * Update multiple values on a table
	 *
	 * Usage:
	 *   Be careful!!!!
	 *    set_multiple_values('colum1=value1, column2=value2', ' WHERE column3=1 LIMIT 1 ');
	 *    set_multiple_values('colum1=value1, column2=value2'); -> limited by default to 0
	 *    set_multiple_values(); -> returns error
	 *
	 * @param string $tablename
	 * @param string $field_set, sentence with the fieldname and the value to be updated
	 * @param string $filterquery default Limit 0 to prevent errors
	 * @return void This method just execute the UPDATE statement using the given params
	 */
	public static function set_multiple_values($tablename, $field_set='', $filterquery='LIMIT 0')
	{

		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "UPDATE ".DB_NAME.".$tablename SET $field_set $filterquery ";
			$stmt = $db->prepare($q);
			$stmt->execute();

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

	}

	/**
	 * Delete the row corresponding to the given value from the database
	 * @param string $tablename 
	 * @param int $id The record identifier (PRIMARY KEY is ussually given here)
	 * @return void This method just execute the DELETE statement using the given param
	 */
	public static function delete_value($tablename, $id)
	{
		(string) $primary_key = UTILS_COMMON::get_table_primary_key_field($tablename);
				 $primary_key = ($primary_key=='') ? UTILS_COMMON::get_table_primary_key_field_view($tablename) : $primary_key;
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "DELETE FROM ".DB_NAME.".$tablename WHERE $primary_key = '$id' LIMIT 1";
			$stmt = $db->prepare($q);
			$stmt->execute();

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

	}

	/**
	 * Delete multiple values on a table
	 *
	 * Usage:
	 *   Be careful!!!!
	 *    delete_multiple_values (' WHERE column3=1 LIMIT 1 ');
	 *    delete_multiple_values (); -> do nothing
	 *
	 * @param string $tablename
	 * @param string $filterquery default Limit is 0 to prevent errors
	 * @return void This method just execute the UPDATE statement using the given params
	 */

	public static function delete_multiple_values($tablename, $filterquery = 'LIMIT 0')
	{
		try {
			$db = new PDO(PDO_DSN, DB_USER, DB_PASS, arr_pdo_attr());
			$q = "DELETE FROM ".DB_NAME.".$tablename $filterquery ";
			$stmt = $db->prepare($q);
			$stmt->execute();

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}

	/**
	 * Destroys object itself
	 * @return void;
	 */
	public function destroy()
	{
		unset($this);
		settype($this, 'null');
	}

	/**
	 * Destroys object itself
	 * @return void;
	 */
	public function __destruct()
	{
		$this->destroy();
	}
}
?>
