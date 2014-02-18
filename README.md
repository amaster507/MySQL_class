#MySQL_class

This is a PHP class developed for use in custom built applications where constant queries were being performed. This class dramatically eased working with the database layer. 

Follow the author, Anthony Master, at http://amasterdesigns.com

##CONTENTS OF THIS FILE
  1. Class Requirements
  2. Connecting to a Database
  3. Closing Database Connection
  4. SQL Injection Protection
  5. Sending Queiries
  6. Testing Queries
  7. Count Returned Rows
  8. Seek Results
  9. Loop Through Results
  10. Retrieve Last Insert Id
  11. Fething Datum
  12. Fethcing Column Names
  13. Fetching Column Data Types
  14. Selecting a Table Row
  15. Inserting Arrays
  16. Affected Rows
  17. Deleting Rows
  18. Updating Rows
  19. Inserting or Updating Rows
 
### Class Requirements

Before the class can be used within pages there are two requirements

  1. The `init.php` file is included somewhere on the page before use of the MySQL class.
  2. The variable `$MySQL` must be defined globally to look for the class within the included init.php --> included MySQL.php file

```
global $MySQL;
```

For good practice, this variable should be defined globally on each use in case the previous global definition is removed in a later coding.

### Connecting to a Database

To connect to a different serverhost or database then use the connect() class function and define the variables

```
$mysql_host = "localhost";
$mysql_user = "root"
$mysql_pswd = "password";
$mysql_db = "test";
$mysql_port = 3306;
$MySQL->connect($mysql_host, $mysql_user, $mysql_pswd, $mysql_db, $mysql_port);
```	
	
### Closing Database Connection

To close out any active MySQL database connections use the close() class function

```
$MySQL->close();
```

### SQL Injection Protection

In order to protect the database from SQl injection string escaping must be done before running any queries to the database

String escaping can be done in two different ways

####By single string

```
$string = "my string to escape";
$string = $MySQL->escape($string);
```

####With a multidimensional or single dimensional array of strings

```
$string_array = array(
  "key_1" => "string to escape 1",
  "key_2" => "string to escape 2",
  "key_3" => array(
    "key_3" => "string to escape 3"
  ),
);
$string_array = $MySQL->escape_array($string_array);
```

####By using the `insert()`, `update()`, or `insert_update()` class functions with the proper parameters. __See actual functions for reference.__

### Sending Queries

To send a query to the database use the `query()` function. The class will auto connect if a connection does not already exist. If there are errors with the query or connection, then the page will die with the error message. Returns __FALSE__ on failure. For successful _SELECT_, _SHOW_, _DESCRIBE_ or _EXPLAIN_ queries `mysqli_query()` will return a `mysqli_result` object. For other successful queries `mysqli_query()` will return __TRUE__.

```
$query = "UPDATE users SET username='amaster' WHERE user_id='11'";
MySQL->query($query);
```	

### Testing Queries

To test if the query was successful perform the following. There is no need to check for failure.

```
$query = "UPDATE users SET username='amaster' WHERE user_id='11'";
if(MySQL->query($query)){
  //do something on success
}
```

### Count Returned Rows

To get the number of rows for the last query use the `num_rows()` function.

```
$query = "SELECT username FROM users WHERE user_id='11'";
MySQL->query($query);
$count = $MySQL->num_rows();
//do something with $count
```

### Seek Results

To seek to a different row of the last query results use the `data_seek()` class function.
 
 __Note: remember that the offset starts with the first row being row 0.__
 
```
$query = "SELECT username FROM users";
MySQL->query($query);
MySQL->data_seek(2);
//do something with the third row
```

### Loop through Results

To loop through the results set of the last ran query use the `fetch_array()` function. To fetch the array as a numeric array use instead the `fetch_row()` function

```
$query = "SELECT username FROM users";
MySQL->query($query);
while($row = $MySQL->fetch_array()){
	//do something with each $row["username"]
}

$query = "SELECT username FROM users";
MySQL->query($query);
while($row = $MySQL->fetch_row()){
	//do something with each $row[0]
}
```

### Retrieve Last Insert ID

To get the id of the last inserted query use the `insert_id()` function

```
$query = "INSERT INTO users (user_id) VALUES (NULL)";
MySQL->query($query);
$last_insert_id = $MySQL->insert_id();
//do something with $last_insert_id
```

### Fetching Datum

To get the data about a certain row and a specific column then use the `fetch_data()` class function. **NOTE: _CAUTION_ Be careful NOT to use this inside of a `fetch_array()` or `fetch_row()` loop as the row count will then be distorted!**

```
$query = "SELECT * FROM users";
MySQL->query($query);
$data = $MySQL->fetch_data('username', 10);
//do something with $data
```

### Fetching Column Name

To get the name of the specified field number use the `field_name()` class function

```
$query = "SELECT * FROM users";
MySQL->query($query);
$field_name = $MySQL->field_name(1);
do something with $field_name
```

### Fetching Column Data Type

To get the type of the specified field number use the `field_type()` class function

```
$query = "SELECT * FROM users";
MySQL->query($query);
$field_type = $MySQL->field_type(1);
do something with $field_type
```
 	
### Inserting Arrays

There is also a quicker way to insert data into a table when you make an array of the column=>value as the key=>value pair using the `insert()` class function

**NOTE: Look at this function for more detail of other parameters**

This function could be used to insert the entire `$_POST` to a table.

```
$data_array = array(
	"user_id"=>"NULL",
	"user_name"=>"amaster",
);
$MySQL->insert('users',$data_array);
```

### Affected Rows

To get the number of rows affected by the last query, use the `affected_rows()` class function.

```
$query = "DELETE FROM users WHERE username='amaster'";
$MySQL->query($query);
$rows_deleted = $MySQL->affected_rows();
```

### Deleting Rows

An easier way to delete rows is by using the `delete()` class function.

```
//with parameters
$MySQL->delete('users','username','amaster');

//or with where clause
$MySQL->delete('users',NULL,NULL,"username='amaster'");

//or to truncate the entire table USE WITH CAUTION!
$MySQL->delete('users',NULL,NULL,NULL,NULL,true);
```

### Updating Rows

An easier way to update rows is to use the `update()` class function.

```
$my_array = array("password"=>"new_password","first_name"=>"Anthony");
$MySQL->update('users',$my_array,'user_id','1');
```

### Inserting or Updating Rows

When inserting data and duplicate keys may exist where the rows need updating instead, you should use the `insert_or_update()` class function.

```
$insert_array = array(
  "user_id"=>"1",
  "user_name"=>"amaster",
  "password"=>"new_password",
);
$update_array = array(
  "password"=>"new_password",
);
$MySQL->insert_or_update('users',$insert_array,$update_array);
```

## For more information regarding particular function please see comments in `MySQL.php`.

Thank you for your interest in MySQL class found on github at https://github.com/amaster507/MySQL_class/
