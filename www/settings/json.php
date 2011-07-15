<?php

require_once("config.php");
require_once("databaseinfo.php");

function do_post_request($found) {
    $params = array('http' => array(
            'method' => 'POST',
            'content' => implode(',', $found)
            ));
    if (isset($optional_headers)) {
        $params['http']['header'] = $optional_headers;
    }
    $ctx = stream_context_create($params);
    $timeout = 3;
    $old = ini_set('default_socket_timeout', $timeout);
	$fp = false;
	try{
		$fp = @fopen(WIREDUX_SERVICE_URL, 'rb', false, $ctx);
	}catch(ErrorException $e){}
    ini_set('default_socket_timeout', $old);
    if ($fp) {
        stream_set_timeout($fp, $timeout);
        stream_set_blocking($fp, 3);
    } else{
        //throw new Exception("Problem with " . WIREDUX_SERVICE_URL . ", $php_errormsg");
        return false;
	}
    $response = @stream_get_contents($fp);
    if ($response === false) {
        //throw new Exception("Problem reading data from " . WIREDUX_SERVICE_URL . ", $php_errormsg");
    }
	if ($fp) {
		fclose($fp);
	}
    return $response;
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
