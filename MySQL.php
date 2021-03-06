<?php

// Further documentation on how to use this class can be found in the README file

class MySQLclass {
	//database connection data
	private $mysql_host;
	private $mysql_user;
	private $mysql_pswd;
	private $mysql_db;
	private $mysql_port;
	private $link = NULL;
	public $result = NULL;
	public $field = NULL;
	
	function connect($mysql_host = NULL,$mysql_user = NULL,$mysql_pswd = NULL,$mysql_db = NULL,$mysql_port=3306){
		$this->close();
		if($mysql_host!==NULL){
			$this->mysql_host = $mysql_host;
		}
		if($mysql_user!==NULL){
			$this->mysql_user = $mysql_user;
		}
		if($mysql_pswd!==NULL){
			$this->mysql_pswd = $mysql_pswd;
		}
		if($mysql_db!==NULL){
			$this->mysql_db = $mysql_db;
		}
		if($mysql_host!==3306){
			$this->mysql_host = $mysql_host;
		}
		$this->link = mysqli_connect(
			$this->mysql_host,
			$this->mysql_user,
			$this->mysql_pswd,
			$this->mysql_db,
			$this->mysql_port
		) or die("Error ".mysqli_connect_error());
		return $this->link;
	}
	
	function close($link = NULL){
		if($link===NULL){
			$link = $this->link;
		}
		if($link!==NULL){
			$this->link = NULL;
			return mysqli_close($link);
		} else {
			return false;
		}
	}
	
	function set_host($mysql_host = "localhost"){
		$this->close();
		$this->mysql_host = $mysql_host;
	}
	
	function set_user($mysql_user = "root"){
		$this->close();
		$this->mysql_user = $mysql_user;
	}
	
	function set_pswd($mysql_pswd = "password"){
		$this->close();
		$this->mysql_pswd = $mysql_pswd;
	}
	
	function select_db($mysql_db = "test"){
		$this->close();
		$this->mysql_db = $mysql_db;
	}
	
	function set_port($mysql_port = 3306){
		$this->close();
		$this->mysql_port = $mysql_port;
	}
	
	function error($link = NULL){
		if($link===NULL){
			$link = $this->link;
		}
		return mysqli_error($link);
	}
	
	function escape($string, $link = NULL){
		if($link===NULL){
			if(empty($this->link)){
				$this->connect();
			}
			$link = $this->link;
		}
		if(is_null($string) || empty($string)){
			return $string;
		}
		return mysqli_real_escape_string($link,$string);
	}
	
	/**
	 * @param array $array single or multidimensional array to be escaped
	 * @return array the escaped single or multidimensional array
	 */
	function escape_array(array $array, $link = NULL){
		if($link===NULL){
			$link = $this->link;
		}
		$new_array = array();
		foreach($array as $key=>$value){
			if(is_array($value)){
				if(is_numeric($key)){
					$new_array[$key] = $this->escape_array($value,$link);
				} else {
					$new_array[$this->escape($key,$link)] = $this->escape_array($value,$link);
				}
			} else {
				if(is_numeric($key)){
					$new_array[$key] = $this->escape($value,$link);
				} else {
					$new_array[$this->escape($key,$link)] = $this->escape($value,$link);
				}
			}
		}
		return $new_array;
	}
	
	/**
	 * @return mixed Returns FALSE on failure. For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries mysqli_query() will return a mysqli_result object. For other successful queries mysqli_query() will return TRUE.
	 */
	function query($query,$link = NULL){
		if($link===NULL){
			if(empty($this->link)){
				$this->connect();
			}
			$link = $this->link;
		}
		$time1 = microtime(true);
		$this->result = mysqli_query($link,$query);
		$time2 = microtime(true);
		$time_to_run = $time2 - $time1;
		if(!$this->result){
		  /* In order to use error logging please follow the directions in the README then uncomment this section
			output_error("Query",$query,"invalid query");
			output_error("Error",$this->error(),"MySQL error");
			die();
			*/
			die("MySQL Error: ".$this->error());
		}
		return $this->result;
	}
	
	/**
	 * @return int Indicates the number of rows affected or retrieved. Zero indicates that no records were updated for an UPDATE statement, no rows matched the WHERE clause in the query or that no query has yet been executed. -1 indicates that the query returned an error.
	 */
	function affected_rows($link = NULL){
		if($link===NULL){
			$link = $this->link;
		}
		return mysqli_affected_rows($link);
	}
	
	/**
	 * @return int Returns number of rows in the result set.
	 */
	function num_rows($result = NULL){
		if($result===NULL){
			$result = $this->result;
		}
		return mysqli_num_rows($result);
	}
	
	/**
	 * @return array Returns an array of strings that corresponds to the fetched row or NULL if there are no more rows in resultset.
	 */
	function fetch_array($result = NULL,$resulttype = MYSQLI_ASSOC){
		if($result===NULL){
			$result = $this->result;
		}
		return mysqli_fetch_array($result,$resulttype);
	}
	
	/**
	 * @return array returns an array of strings that corresponds to the fetched row or NULL if there are no more rows in result set.
	 */
	function fetch_row($result = NULL){
		if($result===NULL){
			$result = $this->result;
		}
		return mysqli_fetch_row($result);
	}
	
	/**
	 * @return string returns data of specified column of the specified row
	 */
	function fetch_data($column, $row = 0, $result = NULL){
		if($result===NULL){
			$result = $this->result;
		}
		if(!$this->data_seek($row, $result)){
			return false;
		}
		$data = $this->fetch_array($result);
		$this->data_seek(0, $result);
		if(isset($data[$column])){
			return $data[$column];
		} else {
			return false;
		}
	}
	
	function data_seek($offset, $result = NULL){
		if($result===NULL){
			$result = $this->result;
		}
		return mysqli_data_seek($result,$offset);
	}
	
	/**
	 * @return int id of last inserted row
	 */
	function insert_id($link = NULL){
		if($link===NULL){
			$link = $this->link;
		}
		return mysqli_insert_id($link);
	}
	
	function delete($table,$column_where=NULL,$value=NULL,$condition=NULL,$link = NULL,$truncate_table=false){
		$table = $this->escape($table);
		$chk_table_query = 'SHOW TABLES LIKE "'.$table.'"';
		$this->query($chk_table_query,$link);
		if($this->num_rows()!=1){
			die("DELETE: The MySQL class function was presented with an unrecognized parameter.");
		}
		$table = "`".$table."`";
		if($column_where===NULL && $value===NULL && $condition===NULL && $truncate_table===true){
			//truncate table
			$query = "TRUNCATE $table";
			return $this->query($query,$link);
		} else if($column_where===NULL && $value===NULL && $condition===NULL) {
			die("Empty arguments sent to function please contact IT for support");
		} else if($column_where===NULL && $value===NULL && $condition!==NULL){
			//delete with condition
			$query = "DELETE FROM $table WHERE $condition";
			return $this->query($query,$link);
		} else if($column_where!==NULL && $value!==NULL && $condition===NULL){
			//delete by column and value
			$column_where = $this->escape($column_where,$link);
			$value = $this->escape($value,$link);
			$query = "DELETE FROM $table WHERE $column_where='$value'";
			return $this->query($query,$link);
		} else {
			die("Ambiguous arguments sent to function please contact IT for support");
		}
	}
	
	/**
	 * @param string $table the table to insert array into
	 * @param array $array contains column => value pairs values may be MySQL Function
	 * @param bool $escape true to escape $array; false to leave $array as is with no escaping
	 * @param bool $mysql_functions true to insert mysql functions as function, false to insert mysql functions as string
	 * @param bool $null_string_is_null true to insert string "NULL" as NULL; false to insert string "NULL" as string "NULL"
	 * @param bool $empty_string_is_null true to insert string "" as NULL; false to insert string "" as string ""
	 * @param $link link to the mysql database to use. Will use existing if NULL or create new if link does not exist.
	 * @return bool Returns FALSE on failure. For successful queries will return TRUE.
	 */
	function insert($table,array $array,$escape = true,$mysql_functions = false,$null_string_is_null = true,$empty_string_is_null = false,$link = NULL){
		if($link===NULL){
			$link = $this->link;
		}
		if($escape){
			$array = $this->escape_array($array);
		}
		$table = $this->escape($table);
		$chk_table_query = 'SHOW TABLES LIKE "'.$table.'"';
		$this->query($chk_table_query,$link);
		if($this->num_rows()!=1){
			die("INSERT: The MySQL class function was presented with an unrecognized parameter.");
		}
		$table = "`".$table."`";
		$columns = "";
		$values = "";
		foreach($array as $key => $value){
			$columns = ($columns=="") ? "`".$key."`" : $columns.", `".$key."`";
			if($value===NULL || ($value=="NULL" && $null_string_is_null) || ($value=="" && $empty_string_is_null)){
				$value = "NULL";
			} else if(preg_match("/^[A-Z_]+\(.*?\)/", $value) && $mysql_functions) {
				$value = "".$value."";
			} else {
				$value = "'".$value."'";
			}
			$values = ($values=="") ? "".$value."" : $values.", ".$value."";
		}
		$query = "INSERT INTO $table ($columns) VALUES ($values)";
		return $this->query($query,$link);
	}
	
	
	function insert_or_update($table,array $array,array $update,$escape = true,$mysql_functions = false,$null_string_is_null = true,$empty_string_is_null = false,$link = NULL){
		if(empty($update)){
			die("EMPTY UPDATE: The MySQL class function was presented with an unrecognized parameter.");
		}
		if($link===NULL){
			$link = $this->link;
		}
		if($escape){
			$array = $this->escape_array($array);
		}
		$table = $this->escape($table);
		$chk_table_query = 'SHOW TABLES LIKE "'.$table.'"';
		$this->query($chk_table_query,$link);
		if($this->num_rows()!=1){
			die("INSERT: The MySQL class function was presented with an unrecognized parameter.");
		}
		$table = "`".$table."`";
		$columns = "";
		$values = "";
		foreach($array as $key => $value){
			$columns = ($columns=="") ? "`".$key."`" : $columns.", `".$key."`";
			if($value===NULL || ($value=="NULL" && $null_string_is_null) || ($value=="" && $empty_string_is_null)){
				$value = "NULL";
			} else if(preg_match("/^[A-Z_]+\(.*?\)/", $value) && $mysql_functions) {
				$value = "".$value."";
			} else {
				$value = "'".$value."'";
			}
			$values = ($values=="") ? "".$value."" : $values.", ".$value."";
		}
		$changes = "";
		foreach($update as $key => $value){
			if(is_null($value) || ($value=="NULL" && $null_string_is_null) || ($value=="" && $empty_string_is_null)){
				$value = "NULL";
			} else if(preg_match("/^[A-Z_]+\(.*?\)/", $value) && $mysql_functions) {
				$value = "".$value."";
			} else {
				$value = "'".$value."'";
			}
			$changes = ($changes=="") ? "`".$key."`=".$value : $changes.", `".$key."`=".$value;
		}
		$query = "INSERT INTO $table ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE $changes";
		return $this->query($query,$link);
	}
	
	/**
	 * @param string $table the table to update array into
	 * @param array $array contains column => value pairs, values may be MySQL Function
	 * @param string $column_where the column to use in the where clause
	 * @param string $value the unescaped value to compare to the column in the where clause
	 * @param bool $escape true to escape $array; false to leave $array as is with no escaping
	 * @param bool $mysql_functions true to insert mysql functions as function, false to insert mysql functions as string
	 * @param bool $null_string_is_null true to insert string "NULL" as NULL; false to insert string "NULL" as string "NULL"
	 * @param bool $empty_string_is_null true to insert string "" as NULL; false to insert string "" as string ""
	 * @param $link link to the mysql database to use. Will use existing if NULL or create new if link does not exist.
	 * @return bool Returns FALSE on failure. For successful queries will return TRUE.
	 */
	function update($table,array $array,$column_where,$where_value,$escape = true,$mysql_functions = false,$null_string_is_null = true,$empty_string_is_null = false,$link = NULL){
		if($link===NULL){
			$link = $this->link;
		}
		if($escape){
			$array = $this->escape_array($array,$link);
		}
		$table = $this->escape($table,$link);
		$column_where = "`".$column_where."`";
		$where_value = $this->escape($where_value,$link);
		$chk_table_query = 'SHOW TABLES LIKE "'.$table.'"';
		$this->query($chk_table_query,$link);
		if($this->num_rows()!=1){
			die("UPDATE: The MySQL class function was presented with an unrecognized parameter.");
		}
		$table = "`".$table."`";
		$changes = "";
		$columns = "";
		foreach($array as $key => $value){
			if(is_null($value) || ($value=="NULL" && $null_string_is_null) || ($value=="" && $empty_string_is_null)){
				$value = "NULL";
			} else if(preg_match("/^[A-Z_]+\(.*?\)/", $value) && $mysql_functions) {
				$value = "".$value."";
			} else {
				$value = "'".$value."'";
			}
			$changes = ($changes=="") ? "`".$key."`=".$value : $changes.", `".$key."`=".$value;
		}
		$query = "UPDATE $table SET $changes WHERE $column_where='$where_value'";
		return $this->query($query,$link);
	}
	
	function field_name($fieldnr,$result = NULL){
		if($result===NULL){
			$result = $this->result;
		}
		$field = mysqli_fetch_field_direct($result,$fieldnr);
		$this->field = $field;
		return $field->name;
	}
	
	function field_type($fieldnr,$result = NULL){
		if($result===NULL){
			$result = $this->result;
		}
		$field = mysqli_fetch_field_direct($result,$fieldnr);
		$this->field = $field;
		return $field->type;
	}
	
	function num_fields($link = NULL){
		if($link===NULL){
			$link = $this->link;
		}
		return mysqli_field_count($link);
	}
	
	/**
	 * @param string $table the table to retrieve row from
	 * @param string $column_where the column to use in the where clause
	 * @param string $value the unescaped value to compare to the column in the where clause
	 * @param string $order the column(s) and ASC/DESC for the ORDER BY clause
	 * @param $link link to the mysql database to use. Will use existing if NULL or create new if link does not exist
	 * @return array Returns an array of strings that corresponds to the fetched row or NULL if there are no more rows in result set.
	 */
	function get_table_row($table,$column_where,$value,$order = NULL,$resulttype = MYSQLI_ASSOC,$link = NULL){
		$table = "`".$table."`";
		$column_where = "`".$column_where."`";
		$value = $this->escape($value,$link);
		if(!empty($order)){
			$order = "ORDER BY $order";
		} else {
			$order = "";
		}
		$query = "SELECT * FROM $table WHERE $column_where='$value' $order";
		$this->query($query,$link);
		if($this->num_rows()==1){
			return $this->fetch_array(NULL,$resulttype);
		} else {
			return NULL;
		}
	}
	
	/**
	 * @param string $table the table to retrieve row from
	 * @param array $columns array of column names to retrieve data from
	 * @param string $column_where the column to use in the where clause
	 * @param string $value the unescaped value to compare to the column in the where clause
	 * @param string $order the column(s) and ASC/DESC for the ORDER BY clause
	 * @param $link link to the mysql database to use. Will use existing if NULL or create new if link does not exist
	 * @return array Returns an array of strings that corresponds to the fetched row or NULL if there are no more rows in result set.
	 */
	function get_table_columns_from_row($table,array $columns,$column_where,$value,$order = NULL,$resulttype = MYSQLI_ASSOC,$link = NULL){
		$table = "`".$table."`";
		$get_columns = "";
		foreach($columns as $column){
			$get_columns = ($get_columns=="") ? "`".$column."`" : ", `".$column."`";
		}
		$column_where = "`".$column_where."`";
		$value = $this->escape($value,$link);
		if(!empty($order)){
			$order = "ORDER BY $order";
		} else {
			$order = "";
		}
		$query = "SELECT $get_columns FROM $table WHERE $column_where='$value' $order";
		$this->query($query,$link);
		if($this->num_rows()==1){
			return $this->fetch_array(NULL,$resulttype);
		} else {
			return NULL;
		}
	}
	
	/**
	 * @param string $table the table to retrieve row from
	 * @param string $column_where the column to use in the where clause
	 * @param string $value the unescaped value to compare to the column in the where clause
	 * @param string $order the column(s) and ASC/DESC for the ORDER BY clause
	 * @param $link link to the mysql database to use. Will use existing if NULL or create new if link does not exist
	 * @return array Returns anumbered array of an array of strings that corresponds to the fetched row or NULL if there are no more rows in result set.
	 */
	function get_table_array($table,$column_where,$value,$order = NULL,$resulttype = MYSQLI_ASSOC,$link = NULL){
		$table = "`".$table."`";
		$column_where = "`".$column_where."`";
		$value = $this->escape($value,$link);
		if(!empty($order)){
			$order = "ORDER BY $order";
		} else {
			$order = "";
		}
		$query = "SELECT * FROM $table WHERE $column_where='$value' $order";
		$this->query($query,$link);
		$data_array = array();
		while($row = $this->fetch_array(NULL,$resulttype)){
			$data_array[] = $row;
		}
		return $data_array;
	}
	
	/**
	 * @param string $table the table to retrieve row from
	 * @param string $column_where the column to use in the where clause
	 * @param string $value the unescaped value to compare to the column in the where clause
	 * @param string $order the column(s) and ASC/DESC for the ORDER BY clause
	 * @param $link link to the mysql database to use. Will use existing if NULL or create new if link does not exist
	 * @return array Returns anumbered array of an array of strings that corresponds to the fetched row or NULL if there are no more rows in result set.
	 */
	function get_table_array_for_column($table,array $columns,$column_where,$value,$order = NULL,$resulttype = MYSQLI_ASSOC,$link = NULL){
		$table = "`".$table."`";
		$get_columns = "";
		if(empty($columns)){
			die("No columns were selected for return please contact IT for support.");
		}
		foreach($columns as $column){
			$get_columns = ($get_columns=="") ? "`".$column."`" : ", `".$column."`";
		}
		$column_where = "`".$column_where."`";
		$value = $this->escape($value,$link);
		if(!empty($order)){
			$order = "ORDER BY $order";
		} else {
			$order = "";
		}
		$query = "SELECT $get_columns FROM $table WHERE $column_where='$value' $order";
		$this->query($query,$link);
		$data_array = array();
		while($row = $this->fetch_array(NULL,$resulttype)){
			$data_array[] = $row;
		}
		return $data_array;
	}
	
	/**
	 * @param string $table the table to look in
	 * @param string $column the column name to look for
	 * @param $link link to the mysql database to use. Will use existing if NULL or create new if link does not exist
	 */
	
	function check_for_column_in_table($table,$column,$link = NULL){
		$table = $this->escape($table);
		$column = $this->escape($column);
		$query = "
			SELECT
			  COLUMN_NAME
			FROM
			  information_schema.COLUMNS
			WHERE
			  TABLE_SCHEMA = '".$this->mysql_db."' AND
			  TABLE_NAME = '$table' AND
			  COLUMN_NAME = '$column'
		";
		$this->query($query);
		$result = NULL;
		while($row = $this->fetch_array()){
			$result = $row["COLUMN_NAME"];
		}
		return $result;
	}
	
}

?>
