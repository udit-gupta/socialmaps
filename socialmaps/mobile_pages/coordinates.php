<?php
	error_reporting(0);
	if( strlen($_POST['data']) != 0 )
	{
		$_POST = array_map('stripslashes',$_POST);
		$_POST = array_map('mysql_real_escape_string',$_POST);
                $data = explode(":",base64_decode($_POST['data']));
		$uid = $data[0];
		$mcc = $data[1];
		$mnc = $data[2];
		$lac = $data[3];
		$cellId = $data[4];
		$output = array();
		exec("lib/my_location.py $mcc $mnc $lac $cellId",$output);
		$latitude = "'".$output[0]."'";
		$longitude = "'".$output[1]."'";
		$accuracy = $output[2];
                require('../lib/mysql/connect_db.php');
		require_once('../lib/mysql/mysql_class.php');
		$ob = new mysql("location");
		$ob->insert("$uid,CURRENT_TIMESTAMP,$latitude,$longitude,$accuracy");  // Potential problem due to foreign key.
		echo "error:0";
	}
	else
            exit("error:1");
?>
