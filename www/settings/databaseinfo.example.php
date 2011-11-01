<?php
/*
 * Copyright (c) 2007, 2008, 2011 Contributors, http://opensimulator.org/
 * See CONTRIBUTORS for a full list of copyright holders.
 *
 * See LICENSE for the full licensing terms of this file.
 *
*/
namespace{
##################### Database ########################
define("C_DB_TYPE","mysql");
// Your Hostname here:
define("C_DB_HOST","localhost");
// Your Databasename here:
define("C_DB_NAME","aurora");
// Your Username from Database here:
define("C_DB_USER","aurora");
// Your Database Password here:
define("C_DB_PASS","changeme");

}

namespace Aurora\WebUI{
	use InvalidArgumentException;

	use Aurora\WebUI\PDO\helper as PDOH;

##################### PDO #############################
	require('pdo.php');
	$PDODB = DB::i();
	try{
		$PDODB['Aurora']      = null; // replace with a call to PDOH::PDO()
		$PDODB['AuroraUsers'] = null; // replace with a call to PDOH::PDO()
		$PDODB['AuroraWebUI'] = PDOH::PDO('mysql:host=localhost;dbname=aurora', 'aurora', 'changeme'); // this is an example of how to use PDOH::PDO()
	}catch(InvalidArgumentException $e){
		switch($e->getCode()){
			case 4:
				header('HTTP/1.1 500 Internal Server Error',true,500);
				die('You forgot to setup the call for the \'' . $PDODB->lastKey() . '\' tables!');
			break;
			case 5:
				header('HTTP/1.1 500 Internal Server Error',true,500);
				die('You already configured the \'' . $PDODB->lastKey() . '\' tables, did you accidentally duplicate a line?');
			break;
			default:
				throw $e;
			break;
		}
	}
}
?>
