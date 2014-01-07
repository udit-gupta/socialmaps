<?php
    error_reporting(0);
    if (strlen($_POST['data']) != 0)
    {
        $_POST = array_map('stripslashes',$_POST);
	$_POST = array_map('mysql_real_escape_string',$_POST);
        $data = explode(":",base64_decode($_POST['data']));
        $username = $data[0];
        $password = $data[1];
        $output = array();
        exec("lib/fb_login.py $username $password",$output);
        if($output[0] == "error:1")
            exit(base64_encode("error:1"));
        $fb_id = $output[0];
        $access_token = $output[1];
        // Create the user if it doesn't exists and updates it otherwise.
        require('../lib/mysql/connect_db.php');
        require_once('../lib/mysql/mysql_class.php');
        $ob = new mysql("users");
        $result = $ob->select("*","WHERE fb_id='$fb_id'");
        $row = mysql_fetch_array($result);        
        if($row)
        {
            $result = $ob->select("access_token","WHERE fb_id='$fb_id' AND access_token_permanent=1");
            $row = mysql_fetch_array($result);
            if($row)
                $access_token = $row['access_token'];
            else
                $ob->update("access_token,access_token_permanent","'$access_token',0","fb_id='$fb_id'");
        }
        else
            $ob->insert("fb_id,access_token","'$fb_id','$access_token'");
        // End of user creation/update.
        $result = $ob->select("uid","WHERE fb_id='$fb_id'");
        $row = mysql_fetch_array($result);
        echo base64_encode($row['uid']);
    }
    else
        exit(base64_encode("error:1"));
?>
