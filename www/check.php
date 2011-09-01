<?php
/*
 * Copyright (c) 2007 - 2011 Contributors, http://opensimulator.org/, http://aurora-sim.org/
 * See CONTRIBUTORS for a full list of copyright holders.
 *
 * See LICENSE for the full licensing terms of this file.
 *
 */
 
namespace{

	if(defined('RECAPTCHA_PUBLIC_KEY') === false){
		define('RECAPTCHA_PUBLIC_KEY', false);
	}
	if(defined('RECAPTCHA_PRIVATE_KEY') === false){
		define('RECAPTCHA_PRIVATE_KEY', false);
	}
}

namespace Aurora\WebUI{
	use RuntimeException;
	use PDOException;

	$PDODB = DB::i();
	$required = array(
		'AuroraWebUI',
		'Aurora',
		'AuroraUsers',
	);
	foreach($required as $dbLabel){
		if(isset($PDODB[$dbLabel]) === false){
			throw new RuntimeException(sprintf('%1$s database PDO object not specified', $dbLabel));
		}
	}

	try{
		$sth1 = $PDODB['AuroraWebUI']->prepare('DELETE FROM wi_codetable WHERE (time + 86400) < :stale AND info="pwreset"');
		$sth2 = $PDODB['AuroraWebUI']->prepare('SELECT UUID FROM wi_codetable WHERE (time + :deletetime) < :stale AND info="confirm"');
		$sth3 = $PDODB['AuroraUsers']->prepare('DELETE FROM useraccounts WHERE PrincipalID=:REGUUID');
		$sth4 = $PDODB['AuroraWebUI']->prepare('DELETE FROM wi_codetable WHERE UUID=:REGUUID');
	}catch(PDOException $e){
		throw new RuntimeException('Could not prepare query to run garbage collection on codes table');
	}

	try{
		$time = time();
		$sth1->bindValue(':stale', $time, \PDO::PARAM_INT);
		if($unconfirmed_deltime != " "){
			$deletetime=60*60*$unconfirmed_deltime;
			$sth2->bindValue(':stale', $time, \PDO::PARAM_INT);
			$sth2->bindValue(':deletetime', $deletetime, \PDO::PARAM_INT);
		}
	}catch(PDOException $e){
		throw new RuntimeException('Could not bind time to query');
	}

	try{
		$sth1->execute();
	}catch(PDOException $e){
		throw new RuntimeException('Could not run garbage collection on codes');
	}
	if($unconfirmed_deltime != " "){
		$REGUUIDS = array();
		try{
			$sth2->execute();
			$REGUUIDS = $sth2->fetchAll(\PDO::FETCH_COLUMN);
		}catch(PDOException $e){
			throw new RuntimeException('Could not fetch account IDs for garbage collection');
		}

		foreach($REGUUIDS as $REGUUID){
			$REGUUID = array(
				':REGUUID' => $REGUUID
			);
			try{
				$sth3->execute($REGUUID);
				$sth4->execute($REGUUID);
			}catch(PDOException $e){
				throw new RuntimeException('Could not complete garbage collection');
			}
		}
	}
}
?>