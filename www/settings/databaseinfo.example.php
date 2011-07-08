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
define("C_DB_PASS","zfq4tMUbWPqvrbsD");

}

namespace Aurora\WebUI{
	use Aurora\WebUI\PDO\helper as PDOH;

##################### PDO #############################
require('pdo.php');
$PDODB = DB::i();
$PDODB['Aurora']      = null; // replace with a call to PDOH::PDO()
$PDODB['AuroraUsers'] = null; // replace with a call to PDOH::PDO()
}
?>
