<?php

require_once("config.php");
require_once("databaseinfo.php");

function do_post_request($found) {
	$ch = curl_init(WIREDUX_SERVICE_URL);
	curl_setopt_array($ch, array(
		CURLOPT_HEADER         => false,
		CURLOPT_POST           => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POSTFIELDS     => implode(',', $found)
	));
	$result = curl_exec($ch);
	curl_close($ch);
	if(is_string($result) === true){
		return $result;
	}else{
		return false;
	}
}

function cleanQuery($string)
{
  $link = mysql_connect(C_DB_HOST, C_DB_USER, C_DB_PASS)
    OR die(mysql_error());
  if(get_magic_quotes_gpc())  // prevents duplicate backslashes
  {
    $string = stripslashes($string);
  }
  if (phpversion() >= '4.3.0')
  {
    $string = mysql_real_escape_string($string);
  }
  else
  {
    $string = mysql_escape_string($string);
  }
  return $string;
}


?>
