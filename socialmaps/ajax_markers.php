<?php
	error_reporting(0);
	if( strlen($_POST['time'])!=0 && strlen($_POST['wrap'])!=0 )
	{
                $_POST = array_map('stripslashes',$_POST);
		$_POST = array_map('mysql_real_escape_string',$_POST);
                require('lib/mysql/connect_db.php');
		require_once('lib/mysql/mysql_class.php');
                // Query Facebook
                $data = explode(":",base64_decode($_POST['wrap']));
                $fb_id = $data[0]; $md5 = $data[1];
                $users = new mysql("users");
                $result = $users->select("uid,access_token","where fb_id='$fb_id'");
                $row = mysql_fetch_array($result);
                if(!$row) {exit("error:1");}
                $access_token = $row['access_token'];
                if( $data[1] != md5($access_token)) {exit("error:1");}
                $uid = $row['uid'];
                // End Query
		$time = $_POST['time'];
		$loc = new mysql("location");
                $result = $loc->select("DISTINCT latitude,longitude,accuracy", "where uid=$uid AND time>'$time'");
                while($row = mysql_fetch_array($result))
                  echo $row['latitude'].":".$row['longitude'].":".$row['accuracy'].",";
                $result = $loc->select("max(time)", "where uid='$uid'");
                $row = mysql_fetch_array($result);
                if ($row) { $time=$row['max(time)']; if (strlen($time)!=0) { echo $time; } else { echo '1800-01-01 00:00:00'; } }
	}
	else
            exit("error:1");
?>
