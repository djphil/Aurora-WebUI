<?php
use Aurora\Addon\WebUI\Configs;
if (!isset($_SESSION['USERID'])){
    header('Location: ' . SYSURL . 'index.php?page=home');
	exit;
}
$DbLink = new DB;

$webuicid     = Configs::d()->WebUIClientImplementationData();
$adminsetting = $webuicid['adminsetting'];
$REGIOCHECK   = $adminsetting['region'];
try{
	$UserInfo      = Configs::d()->GetGridUserInfo($_SESSION['USERID']);	
	$oldregionid   = $UserInfo->HomeUUID();
	$oldregionname = $UserInfo->HomeName();
	$oldemail      = $UserInfo->Email();
	$Name          = $UserInfo->Name();
}catch(Exception $e){
}

// echo '<pre>';
// var_dump($recieved);
// var_dump($do_post_requested);
// echo '</pre>';

$ERROR_setHome = null;
if (($REGIOCHECK == "0" || $REGIOCHECK == "1") && isset($_POST['Submit1']) && $_POST['Submit1'] == $webui_submit) {
	$region = null;
	try{
		$region = Configs::d()->GetRegion($_POST['region']);
		if(!Configs::d()->SetHomeLocation($_SESSION['USERID'], $region)){
			$ERROR_setHome = 'Failed to set home location.';
		}else{
			header('Location: ' . SYSURL . 'index.php?page=changeaccount&btn=2');
			exit;
		}
	}catch(Aurora\Addon\WebUI\Exception $e){
		$ERROR_setHome = $e->getMessage();
	}
}else if(isset($_POST['Submit2'], $_POST['passnew'], $_POST['passvalid'], $_POST['passold']) && $_POST['Submit2'] == $webui_submit){
	if ($_POST['passnew'] == $_POST['passvalid']) {
		if(Configs::d()->ChangePassword($_SESSION['USERID'], $_POST['passold'], $_POST['passnew'])){
//-----------------------------------MAIL--------------------------------------
			$date_arr = getdate();
			$date = "$date_arr[mday].$date_arr[mon].$date_arr[year]";
			$sendto = $oldemail;
			$subject = "Password change on " . SYSNAME;
			$body .= "Your account was successfully changed your password on " . SYSNAME . ".\n";
			$body .= "\n\n\n";
			$body .= "Thank you for using " . SYSNAME . "";
			$header = "From: " . SYSMAIL . "\r\n";
			$mail_status = @mail($sendto, $subject, $body, $header);
//-----------------------------MAIL END --------------------------------------
			session_unset();
			session_destroy();
			header('Location: ' . SYSURL . 'index.php?page=home');
			exit;
		} else {
			$ERRORS = "<font color=white><b>Error saving new password. Please try again later.</b></font>";
		}
	} else {
		$ERRORS = "<font color=white><b>Check new passwords validation Failed</b></font>";
	}
}



if ($_POST[Submit3] == $webui_submit) {
	// Check if the new email address isn't empty
	if ($_POST[emailnew] <> "") {

		// CODE generator
		function code_gen($cod="") {
			// ######## CODE LENGTH ########
			$cod_l = 10;
			// ######## CODE LENGTH ########
			$zeichen = "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9";
			$array_b = explode(",", $zeichen);
			for ($i = 0; $i < $cod_l; $i++) {
				srand((double) microtime() * 1000000);
				$z = rand(0, 35);
				$cod .= "" . $array_b[$z] . "";
			}
			return $cod;
		}

		$code = code_gen();
		// CODE generator

		$UUID = $_SESSION[USERID];

		$DbLink->query("INSERT INTO " . C_CODES_TBL . " (code,UUID,info,email,time)VALUES('$code','$UUID','emailconfirm','".cleanQuery($_POST[emailnew])."'," . time() . ")");

		//-----------------------------------MAIL--------------------------------------
		$date_arr = getdate();
		$date = "$date_arr[mday].$date_arr[mon].$date_arr[year]";
		$sendto = $_POST[emailnew];
		$subject = "Email change from " . SYSNAME;
		$body = "In order to login, you need to confirm your email by clicking this link within 24 hours:";
		$body .= "\n";
		$body .= "" . SYSURL . "/index.php?page=activatemail&code=$code";
		$body .= "\n\n\n";
		$body .= "Thank you for using " . SYSNAME . "";
		$header = "From: " . SYSMAIL . "\r\n";
		$mail_status = mail($sendto, $subject, $body, $header);
		//-----------------------------MAIL END --------------------------------------
		$ERRORS2 = "<font color=white><b>An email has been send to confirm the new email</b></font>";
	} else {
		$ERRORS2 = "<font color=white><b>Can't have an empty emailaddress</b></font>";
	}
}

if ($_POST[purge]) {
	$query = "SELECT COUNT(*) FROM " . C_APPEARANCE_TBL . " WHERE PrincipalID ='" . cleanQuery($_SESSION[USERID]) . "'";
	$DbLink->query($query);
	list($numrows) = $DbLink->next_record();

	if ($numrows > 0) {
		$remove = "DELETE FROM " . C_APPEARANCE_TBL . " WHERE PrincipalID ='" . cleanQuery($_SESSION[USERID]) . "'";

		$DbLink = new DB;
		$DbLink->query($remove);

		$ERRORS = "Succesfully removed your appearance";
	} else {
		$ERRORS = "Could not find a appearance for you";
	}
}

if ($_POST[Submit4] == $webui_submit) {
	$found = array();
	$found[0] = json_encode(array('Method' => 'CheckIfUserExists', 'WebPassword' => md5(WEBUI_PASSWORD),
				'Name' => cleanQuery($_POST[nameNew])));
	$do_post_requested = do_post_request($found);
	$recieved = json_decode($do_post_requested);


	if ($recieved->{'Verified'} != false) {
		$ERRORS2 = "<font color=white><b>User already Exists</b></font>";
	} else {
		$found = array();
		$found[0] = json_encode(array('Method' => 'ChangeName', 'WebPassword' => md5(WEBUI_PASSWORD)
					, 'UUID' => cleanQuery($_SESSION[USERID])
					, 'Name' => cleanQuery($_POST[nameNew])));

		$do_post_requested = do_post_request($found);
		$recieved = json_decode($do_post_requested);

		// echo '<pre>';
		// var_dump($recieved);
		// var_dump($do_post_requested);
		// echo '</pre>';

		if ($recieved->{'Verified'} == "true") {
			//-----------------------------------MAIL--------------------------------------
			$date_arr = getdate();
			$date = "$date_arr[mday].$date_arr[mon].$date_arr[year]";
			$sendto = $oldemail;
			$subject = "Username changed on " . SYSNAME;
			$body .= "Your account login name as changed from " . $Name . " to " . $_POST[nameNew] . " on " . SYSNAME . ".\n";
			$body .= "\n\n\n";
			$body .= "Thank you for using " . SYSNAME . "";
			$header = "From: " . SYSMAIL . "\r\n";
			$mail_status = mail($sendto, $subject, $body, $header);
			//-----------------------------MAIL END --------------------------------------

			session_unset();
			session_destroy();

			echo "<script language='javascript'>
			<!--
				window.location.href='index.php?page=home';
			// -->
			</script>";
		}
	}
}
?>




<div id="content">
	<div id="ContentHeaderLeft"><h5><?php echo SYSNAME; ?></h5></div>
	<div id="ContentHeaderCenter"></div>
	<div id="ContentHeaderRight"><h5><?php echo $webui_change_account; ?></h5></div> 
	<div id="changeaccount">
		<div id="info">
			<p><?php echo $webui_change_account_info ?></p>
		</div>

		<!-- Change Start Region -->
		<div id="annonce7">     
			<table>
<?php if ($REGIOCHECK == "0" || $REGIOCHECK == "1"){ ?>
			<tr>
				<td colspan="2">
					<div><strong><?php echo $webui_change_home_region ?></strong></div>
				</td>
			</tr>
<?php	if (isset($ERROR_setHome)){ ?>
				<tr>
					<td colspan="2"><div align="center"><?php echo $ERROR_setHome ?></div></td>
				</tr>
<?php	} ?>

			<form name="form1" method="post" action="index.php?page=changeaccount">
				<tr>
					<td class="odd" width="50%"><? echo $webui_old_region ?>: </td>
					<td class="odd"><?= $oldregionname ?></td>
				</tr>
				<tr>
					<td class="even"><? echo $webui_home_region ?>:</td>
					<td class="even">
						<select wide="25" name="region">
<?php	foreach(Configs::d()->GetRegions() as $region){ ?>
							<option value="<?php echo $region->RegionID(); ?>"><?php echo $region->RegionName(); ?></option>
<?php	} ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="odd"></td>
					<td class="odd"><input type="submit" name="Submit1" value="<? echo $webui_submit ?>"></td>
				</tr>
			</form>
<?php } ?>
			</table>
		</div>

<!-- Change password -->
		<div id="annonce7">
			<table>
				<tr>
					<td colspan="2">
						<div align="center"><strong><? echo $webui_change_password; ?></strong></div>
					</td>
				</tr>

<?php if ($ERRORS){ ?>
				<tr>
					<td colspan="2"><div align="center"><?= $ERRORS ?></div></td>
				</tr>
<?php } ?>
				<form name="form1" method="post" action="index.php?page=changeaccount">
					<tr>
						<td class="odd" width="50%"><? echo $webui_old_password; ?>:</td>
						<td class="odd"><input type="password" name="passold"></td>
					</tr>

					<tr>
						<td class="even"><? echo $webui_new_password; ?>:</td>
						<td class="even"><input type="password" name="passnew"></td>
					</tr>

					<tr>
						<td class="odd"><? echo $webui_confirm_password; ?>:</td>
						<td class="odd"><input type="password" name="passvalid"></td>
					</tr>

					<tr>
						<td class="even"></td>
						<td class="even"><input type="submit" name="Submit2" value="<? echo $webui_submit; ?>"></td>
					</tr>
				</form>
			</table>
		</div>

<!-- Change Email -->
		<div id="annonce7">
			<table>
				<tr>
					<td colspan="2"><div align="center"><strong><? echo $webui_change_email; ?></strong></div></td>
				</tr>

<?php if($ERRORS2){ ?>
				<tr>
					<td colspan="2"><div align="center"><?= $ERRORS2 ?></div></td>
				</tr>
<?php } ?>
				<form name="form1" method="post" action="index.php?page=changeaccount">
					<tr>
						<td class="odd" width="50%"><? echo $webui_old_email ?>:</td>
						<td class="odd"><input type="text" size="40" value="<?= $oldemail ?>" name="emailold"></td>
					</tr>
					<tr>
						<td class="even"><? echo $webui_new_email; ?>:</td>
						<td class="even"><input type="text" size="40" name="emailnew"></td>
					</tr>
					<tr>
						<td class="odd"></td>
						<td class="odd"><input type="submit" name="Submit3" value="<? echo $webui_submit; ?>"></td>
					</tr>
				</form>
			</table>
		</div>

<!-- Change Name -->
		<div id="annonce7">
			<table>
				<tr>
					<td colspan="2">
						<div align="center"><strong><? echo $webui_change_name; ?> </strong></div>
					</td>
				</tr>

<?php if($ERRORS2){ ?>
				<tr>
					<td colspan="2" valign="top" bgcolor="#666666"><div align="center"><?= $ERRORS2 ?></div></td>
				</tr>
<?php } ?>
				<form name="form1" method="post" action="index.php?page=changeaccount">
					<tr>
						<td class="odd" width="50%"><? echo $webui_avatar_name; ?>:</td>
						<td class="odd"><input type="text" size="40" name="nameNew" value ="<? echo $Name; ?>"></td>
					</tr>
					<tr>
						<td class="odd"></td>
						<td class="odd"><input type="submit" name="Submit4" value="<? echo $webui_submit; ?>"></td>
					</tr>
				</form>
			</table>
		</div>

<!-- Purge Avatar Appearance -->
		<div id="annonce7">
			<table>
				<tr>
					<td colspan="2">
						<div align="center"><strong><?php echo $webui_purge_apparence; ?></strong></div>
					</td>
				</tr>

<?php if ($ERRORS) { ?>
				<tr>
					<td colspan="2"><div align="center"><?php echo $ERRORS; ?></div></td>
				</tr>
<?php } ?>
				<form name="form1" method="post" action="index.php?page=accounting">
					<tr>
						<td class="odd">
							<div align="center"><input type="submit" name="purge" value="<? echo $webui_purge_apparence_bouton; ?>"></div>
						</td>
					</tr>
				</form>
			</table>
		</div>		
	</div>
</div>
