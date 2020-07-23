<?php

include 'app.config.php';

class Secure
{
    function secureSuperGlobalGET(&$value, $key)
    {
        $_GET[$key] = htmlspecialchars(stripslashes($_GET[$key]));
        $_GET[$key] = str_ireplace("script", "blocked", $_GET[$key]);
        $_GET[$key] = mysql_escape_string($_GET[$key]);
        return $_GET[$key];
    }
    
    function secureSuperGlobalPOST(&$value, $key)
    {
        $_POST[$key] = htmlspecialchars(stripslashes($_POST[$key]));
        $_POST[$key] = str_ireplace("script", "blocked", $_POST[$key]);
        $_POST[$key] = mysql_real_escape_string($_POST[$key]);
        return $_POST[$key];
    }
        
    function secureGlobals()
    {
        array_walk($_GET, array($this, 'secureSuperGlobalGET'));
        array_walk($_POST, array($this, 'secureSuperGlobalPOST'));
    }
}

function connect(){
	$con = mysql_connect(Settings::URL, Settings::USER, Settings::PASSWORD);
	@mysql_query("SET NAMES 'utf8'");
	if (!$con)
	  {
	  	die('Could not connect: ' . mysql_error());
	  }else{
	  	mysql_select_db(Settings::DB_NAME);
	  	return $con;
	  }
}
?>