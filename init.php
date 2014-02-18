<?php
	
	//logging functions
	/* before removing the comments allowing these logging functions you must first follow the instructions in the README
	function output_error($title="",$message="",$type="error",$log="/var/log/MySQL_class/error.log"){
		if(isset($_SESSION["Username"]) && !empty($_SESSION["Username"])){
			$user = "|".$_SESSION["Username"];
		} else {
			$user = "";
		}
		error_log("[".date('r')."][client: ".$_SERVER['REMOTE_ADDR'].$user."][".$type."] ".$title.": ".$message."\n",3,$log);
		print "<p class='error'>A(n) $type has occurred, please contact the IT department to review the $title.</p>";
	}
	*/
	
	//call file for MySQL db class
  require_once "MySQL.php";
	
	//define and start the MySQL class
	$MySQL = new MySQLclass;
	
	//define db variables for MySQl class
	$MySQL->set_host();				//default localhost
	$MySQL->set_user();				//default root
	$MySQL->set_pswd();	      //default password
	$MySQL->select_db();		  //default test
	$MySQL->set_port();				//default 3306
	
		
?>
