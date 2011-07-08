<?php
/*
 * Copyright (c) 2007 - 2011 Contributors, http://opensimulator.org/, http://aurora-sim.org/
 * See CONTRIBUTORS for a full list of copyright holders.
 *
 * See LICENSE for the full licensing terms of this file.
 *
 */
 
namespace{
	$DbLink = new DB;

	$DbLink->query("DELETE FROM ".C_CODES_TBL." where (time + 86400) < ".time()." and info='pwreset'");

	if($unconfirmed_deltime != " "){
	$deletetime=60*60*$unconfirmed_deltime;

	$DbLink->query("SELECT UUID FROM ".C_CODES_TBL." where (time + $deletetime) < ".time()." and info='confirm'");	
	while(list($REGUUID) = $DbLink->next_record()){

	$DbLink1 = new DB;
	$DbLink1->query("DELETE FROM ".C_USERS_TBL." where PrincipalID='".cleanQuery($REGUUID)."'");
	$DbLink1->query("DELETE FROM ".C_CODES_TBL." where UUID='".cleanQuery($REGUUID)."'");

	}
	}
}

namespace Aurora\WebUI{
	use RuntimeException;

	$PDODB = DB::i();
	if(isset($PDODB['Aurora']) === false){
		throw new RuntimeException('Aurora database PDO object not specified');
	}
}
?>