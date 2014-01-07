<?php
// connect_db.php


// Connection constants (Configure Here)
define('server', 'localhost');
define('database', 'socialmaps');
define('username', '<db_user>');
define('password', '<db_passwd>');

// Connecting to the database
$con = mysql_connect(server,username,password);
$db = mysql_select_db(database,$con) or die("Unable to select database");
?>
