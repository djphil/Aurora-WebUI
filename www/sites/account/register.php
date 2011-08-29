<?php
require_once('recaptchalib.php ');
$DbLink = new DB;
$DbLink->query("SELECT adress,region,allowRegistrations,verifyUsers,ForceAge FROM " . C_ADM_TBL . "");
list($ADRESSCHECK, $REGIOCHECK,$ALLOWREGISTRATION,$VERIFYUSERS,$FORCEAGE) = $DbLink->next_record();

//GET IP ADRESS
if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $userIP = $_SERVER["HTTP_X_FORWARDED_FOR"];
} elseif (isset($_SERVER["REMOTE_ADDR"])) {
    $userIP = $_SERVER["REMOTE_ADDR"];
} else {
    $userIP = "This user has no ip";
}

// CODE generator
function code_gen($cod="") {
	$cod_l = 10;
	$zeichen = str_getcsv('a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9');

	for ($i = 0; $i < $cod_l; $i++) {
		srand((double) microtime() * 1000000);
		$z = rand(0, 35);
		$cod .= "" . $zeichen[$z] . "";
	}
	return $cod;
}

if($ALLOWREGISTRATION == '1'){
	$error_400 = array();
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		if(empty($_POST) === true){
			$error_400[] = 'No POST data';
		}else if(isset($_POST['action']) === false){
			$error_400[] = 'No action specified';
		}else if($_POST['action'] !== 'check'){
			$error_400[] = 'Unsupported action specified';
		}else if(isset($_POST['accountfirst'], $_POST['accountlast'], $_POST['wordpass'], $_POST['wordpass2'], $_POST['email'], $_POST['emaic']) === false){
			if(isset($_POST['accountfirst']) === false){
				$error_400[] = 'First name not specified';
			}
			if(isset($_POST['accountlast']) === false){
				$error_400[] = 'Last name not specified';
			}
			if(isset($_POST['wordpass']) === false){
				$error_400[] = 'Password missing';
			}
			if(isset($_POST['wordpass2']) === false){
				$error_400[] = 'Confirmation password missing';
			}
			if(isset($_POST['email']) === false){
				$error_400[] = 'Email address missing';
			}
			if(isset($_POST['emaic']) === false){
				$error_400[] = 'Confirmation email address missing';
			}
		}
		if(isset($_POST['wordpass'], $_POST['wordpass2']) && $_POST['wordpass'] !== $_POST['wordpass2']){
			$error_400[] = 'Passwords do not match';
		}
		if(isset($_POST['email'], $_POST['emaic']) && $_POST['email'] !== $_POST['emaic']){
			$error_400[] = 'Emails do not match';
		}
		if($REGIOCHECK == '1'){
			if(isset($_POST['startregion']) === false){
				$error_400[] = 'Start region missing';
			}
		}

		if ($FORCEAGE == "1"){
			if(isset($_POST['tag'], $_POST['monat'], $_POST['jahr'])){
				$tag   = $_POST['tag'];
				$monat = $_POST['monat'];
				$jahr  = $_POST['jahr'];

				$tag2   = date("d", time());
				$monat2 = date("m", time());
				$jahr2  = date("Y", time()) - 18;

				$agecheck1 = $tag  + $monat  + $jahr ;
				$agecheck2 = $tag2 + $monat2 + $jahr2;

				if ($agecheck1 > $agecheck2){
					$error_400[] = 'Sorry, you must be 18 to sign up.';
				}
			}else{
				if(isset($_POST['tag']) === false){
					$error_400[] = 'Day of birth missing';
				}
				if(isset($_POST['monat']) === false){
					$error_400[] = 'Month of birth missing';
				}
				if(isset($_POST['jahr']) === false){
					$error_400[] = 'Year of birth missing';
				}
			}
		}

		if(RECAPTCHA_PUBLIC_KEY !== false && RECAPTCHA_PRIVATE_KEY !== false){
			if(isset($_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field'])){
				$resp = recaptcha_check_answer(
						RECAPTCHA_PRIVATE_KEY,
						$_SERVER["REMOTE_ADDR"],
						$_POST["recaptcha_challenge_field"],
						$_POST["recaptcha_response_field"]
				);
				if(isset($resp, $resp->is_valid) === false || !$resp->is_valid){
					$error_400[] = 'The reCAPTCHA wasn\'t entered correctly. Please try it again.';
				}
			}else{
				if(isset($_POST['recaptcha_challenge_field']) === false){
					$error_400[] = 'recaptcha challenge field missing';
				}
				if(isset($_POST['recaptcha_response_field']) === false){
					$error_400[] = 'recaptcha response field missing';
				}
			}
		}
		if(empty($error_400) === true){
			$passneu = $_POST['wordpass'];
			$passwordHash = md5(md5($passneu) . ":");

			$found = array(json_encode(array(
				'Method' => 'CheckIfUserExists',
				'WebPassword' => md5(WIREDUX_PASSWORD),
				'Name' => cleanQuery($_POST['accountfirst'].' '.$_POST['accountlast'])
			)));
			$do_post_requested = do_post_request($found);
			$recieved = json_decode($do_post_requested);

			if(empty($recieved) === true || isset($recieved, $recieved->Verified) === false){
				$error_400[] = 'POST request failed, could not check if user exists';
			}else if($recieved->Verified != 'False'){
				$error_400[] = 'User already exists in Database';
			}
		}
		$recieved = null;
		if(empty($error_400) === true){
			$userLevel = $VERIFYUSERS == 0 ? 0 : -1;
			$tag   = isset($_POST['tag'  ]) ? $_POST['tag'  ] : '';
			$monat = isset($_POST['monat']) ? $_POST['monat'] : '';
			$jahr  = isset($_POST['jahr' ]) ? $_POST['jahr' ] : '';
			$found = array(json_encode(array(
					'Method'        => 'CreateAccount',
					'WebPassword'   => md5(WIREDUX_PASSWORD),
					'Name'          => cleanQuery($_POST['accountfirst'].' '.$_POST['accountlast']),
					'Email'         => cleanQuery($_POST['email']),
					'HomeRegion'    => cleanQuery($_POST['startregion']),
					'PasswordHash'  => cleanQuery($passneu),
					'AvatarArchive' => cleanQuery(isset($_POST['AvatarArchive']) ? $_POST['AvatarArchive'] : ''),
					'UserLevel'     => cleanQuery($userLevel),
					'RLFisrtName'   => cleanQuery(isset($_POST['firstname'    ]) ? $_POST['firstname'    ] : ''),
					'RLLastName'    => cleanQuery(isset($_POST['lastname'     ]) ? $_POST['lastname'     ] : ''),
					'RLAdress'      => cleanQuery(isset($_POST['adress'       ]) ? $_POST['adress'       ] : ''),
					'RLCity'        => cleanQuery(isset($_POST['city'         ]) ? $_POST['city'         ] : ''),
					'RLZip'         => cleanQuery(isset($_POST['zip'          ]) ? $_POST['zip'          ] : ''),
					'RLCountry'     => cleanQuery(isset($_POST['country'      ]) ? $_POST['country'      ] : ''),
					'RLDOB'         => cleanQuery($tag . "/" . $monat . "/" . $jahr),
					'RLIP'          => cleanQuery($userIP)
			)));

			$recieved = json_decode(do_post_request($found));
			if(is_object($recieved) && isset($recieved->Verified) && $recieved->Verified === 'true'){
				$recieved->Verified = true;
			}

			if($recieved === false){
				$error_400[] = 'POST request failed';
			}else if(isset($recieved->Verified) === false){
				$error_400[] = 'Could not determine verified status';
			}else if($recieved->Verified !== true){
				$error_400[] = 'Unknown error. Please try again later.';
			}else if(isset($recieved->UUID) === false){
				$error_400[] = 'UUID was absent';
			}else{
				$code = code_gen();
				$DbLink = new DB;
				$DbLink->query(sprintf('INSERT INTO %1$s (code,UUID,info,email,time) VALUES("%2$s","%3$s","confirm","%4$s",%5$u)', C_CODES_TBL, cleanQuery($code), $recieved->UUID, cleanQuery($_POST['email']), time()));
	?>
	<div id="content">
		<h2><?php echo $webui_successfully; ?></h2>
		<div id="info">
			<p><?php echo htmlentities($webui_successfully_info); ?></p><br />
			<p><?php echo htmlentities(SYSNAME),' ',htmlentities($webui_avatar_first_name),': <b>',htmlentities(isset($_POST['accountfirst']) ? $_POST['accountfirst'] : ''); ?></b></p><br />
			<p><?php echo htmlentities(SYSNAME),' ',htmlentities($webui_avatar_last_name) ,': <b>',htmlentities(isset($_POST['accountlast' ]) ? $_POST['accountlast' ] : ''); ?></b></p><br />
			<p><?php echo htmlentities(SYSNAME),' ',htmlentities($webui_email)            ,': <b>',htmlentities(isset($_POST['email'       ]) ? $_POST['email'       ] : ''); ?></b></p><br />
		</div>
	</div>
	<?php
				$date_arr = getdate();
				$date = "$date_arr[mday].$date_arr[mon].$date_arr[year]";
				$sendto = $_POST['email'];
				$subject = "Account Activation from " . SYSNAME;
				$body  = "Your account was successfully created at " . SYSNAME . ".\n";
				$body .= "Your first name: " . $_POST['accountfirst'] . "\n";
				$body .= "Your last name:  " . $_POST['accountlast' ] . "\n";
				$body .= "Your password:  "  . $_POST['wordpass'    ] . "\n\n";
				$body .= "In order to login, you need to confirm your email by clicking this link within $deletetime hours:";
				$body .= "\n";
				$body .= "" . SYSURL . "/index.php?page=activate&code=$code";
				$body .= "\n\n\n";
				$body .= "Thank you for using " . SYSNAME . "";
				$header = "From: " . SYSMAIL . "\r\n";
				$mail_status = mail($sendto, $subject, $body, $header);
			}
		}
	}


	if(empty($error_400) === false){
		header('HTTP/1.0 400 Bad Request');
	}
		
	function printLastNames(){
		$DbLink = new DB;
		$DbLink->query("SELECT lastnames FROM " . C_ADM_TBL . "");
		list($LASTNAMESC) = $DbLink->next_record();
		if ($LASTNAMESC == "1") {
			echo '<div class="roundedinput"><select id="register_input" wide="25" name="accountlast">';
			$DbLink->query("SELECT name FROM " . C_NAMES_TBL . " WHERE active=1 ORDER BY name ASC ");
			while (list($NAMEDB) = $DbLink->next_record()) {
				echo '<option>',htmlentities($NAMEDB),'</option>';
			}
			echo "</select></div>";
		} else {
			echo '<div class="roundedinput"><input minlength="3" require="true" label="accountlast_label" id="register_input" name="accountlast" type="text" size="25" maxlength="15" value="',(isset($_POST['accountlast']) ? $_POST['accountlast'] : ''),'" /></div>';
		}
	}


	function displayRegions(){
		$DbLink = new DB;
		class RegionIterator extends Aurora\WebUI\RegionIteratorFromDB{
			const sql_get_uuids =
'SELECT
	RegionUUID
FROM
	gridregions
ORDER BY
	RegionName ASC';
		}

		$PDODB = Aurora\WebUI\DB::i();
		$regions = RegionIterator::r($PDODB['Aurora']);
		echo '<div class="roundedinput"><select require="true" label="startregion_label" id="register_input" wide="25" name="startregion">';
		foreach(RegionIterator::r($PDODB['Aurora']) as $region){
			echo '<option value="',$region->RegionUUID(),'">',htmlentities($region->RegionName()),'</option>';
		}
		echo "</select></div>";
	}


	function displayCountry(){
		$DbLink = new DB;
		echo '<div class="roundedinput"><select require="true" label="country_label" id="register_input" wide="25" name="country" value="',(isset($_POST['country']) ? $_POST['country'] : ''),'">';
		$DbLink->query("SELECT name FROM " . C_COUNTRY_TBL . " ORDER BY name ASC ");
		echo '<option></option>';
		while (list($COUNTRYDB) = $DbLink->next_record()) {
			echo '<option>',htmlentities($COUNTRYDB),'</option>';
		}
		echo '</select></div>';
	}


	function displayDOB(){	
		echo
			'<div id="birthday" class="roundedinput"><table><tr><td>',
			'<select label="dob_label" id="birthday_input" require="true" name="tag" class="',(($status == 1 and $tag == '') ? 'red' : 'black'),'>',
			'<option></option>'
		;
		for($i = 1; $i <= 31; $i++){
			echo '<option value="',$i,'" ',($tag == $i ? 'selected ' : ''),'>',htmlentities($i),'</option>';
		}
		echo '</select>';

		echo
			'<select label="dob_label" id="birthday_input" require="true" name="monat" class="', (($status == 1 and $monat == '') ? 'red' : 'black'), '">',
			'<option></option>'
		;
		for($i = 1; $i <= 12; $i++){
			echo '<option value="',$i,'" ',($monat == $i ? 'selected ' : ''),'>',htmlentities($i),'</option>';
		}
		echo '</select>';

		echo
			'<select label="dob_label" id="birthday_input" require="true" name="jahr" class="',(($status == 1 and $jahr == '') ? 'red' : 'black'),'">',
			'<option></option>'
		;

		$jetzt = getdate();
		$jahr1 = $jetzt["year"];

		for ($i = 1920; $i <= $jahr1; $i++) {
			echo '<option value="',$i,'" ',($jahr == $i ? 'selected ' : ''),'>',htmlentities($i),'</option>';
		}
		echo '</select></td></tr></table></div>';
	}


	function displayDefaultAvatars(){
		$found = array();
		$found[0] = json_encode(array('Method' => 'GetAvatarArchives', 'WebPassword' => md5(WIREDUX_PASSWORD)));
		$do_post_requested = do_post_request($found);
		$recieved = json_decode($do_post_requested);

		if(!$recieved || !isset($recieved->Verified)){
			return;
		}else if ($recieved->{'Verified'} == "true"){
			$names = explode(",", $recieved->{'names'});
			$snapshot = explode(",", $recieved->{'snapshot'});
			$count = count($names);
			echo '<tr><td colspan="2" valign="top">';
			for ($i = 0; $i < $count; $i++){
				echo
					'<div class="avatar_archive_screenshot"><label for="',$names[$i],'" >',htmlentities($names[$i]),'</label>',
					'<input type="radio" id="',$names[$i],'" name="AvatarArchive" value="',$names[$i],'"'
				;
				if((isset($_POST['AvatarArchive']) && $_POST['AvatarArchive'] == $names[$i]) || ($i == 0 && $_POST['AvatarArchive'] == "")){
					echo ' checked';
				}
				echo ' /><label for="',$names[$i],'" ><br><img src="',WIREDUX_TEXTURE_SERVICE,"/index.php?method=GridTexture&uuid=",$snapshot[$i],'" /></div>';
			}
			echo "</td></tr>";
		}else{
			echo '<tr><td colspan=2 valign=top><p>No avatars</p></td></tr>';
		}
	}
		
?>
<div id="content">
	<div id="ContentHeaderLeft"><h5><?php echo SYSNAME; ?></h5></div>
	<div id="ContentHeaderCenter"></div>
	<div id="ContentHeaderRight"><h5><?php echo $webui_register; ?></h5></div>
	<div id="register">
		<form action="index.php?page=register" method="POST" onsubmit="if (!validate(this)) return false;">
			<table>
<?php if(empty($error_400) === false){ ?>
				<tr><td class="error" colspan="2" align="center" id="error_message"><ul><?php foreach($error_400 as $error_msg){ echo '<li>',htmlentities($error_msg),'</li>'; } ?></ul></td></tr>
<?php } ?>
				<tr>
					<td class="even" width="52%"><span id="accountfirst_label"><?php echo $webui_avatar_first_name ?>*</span></td>
					<td class="even">
						<div class="roundedinput">
							<input minlength="3" id="register_input" require="true" label="accountfirst_label" name="accountfirst" type="text" size="25" maxlength="15" value="<?php echo isset($_POST['accountfirst']) ? $_POST['accountfirst'] : ''; ?>">
						</div>
					</td>
				</tr>
				<tr>
					<td class="odd"><span id="accountlast_label"><?php echo $webui_avatar_last_name; ?>*</span></td>
					<td class="odd"><?php echo printLastNames(); ?></td>
				</tr>
				<tr>
					<td class="even"><span id="wordpass_label"><?php echo $webui_password ?>*</span></td>
					<td class="even"><div class="roundedinput">
						<input minlength="6" compare="wordpass2" require="true" label="wordpass_label" id="register_input" name="wordpass" type="password" size="25" maxlength="15">
					</div></td>
				</tr>
				<tr>
					<td class="odd"><span id="wordpass2_label"><?php echo $webui_confirm ?> <?php echo $webui_password ?>*</span></td>
					<td class="odd"><div class="roundedinput">
						<input minlength="6" require="true" label="wordpass2_label" id="register_input" name="wordpass2" type="password" size="25" maxlength="15">
					</div></td>
				</tr>
<?php if ($REGIOCHECK == "0"){ ?>
                
                <tr>
                    <td class="even"><span id="startregion_label"><?php echo $webui_start_region ?>*</span></td>
                    <td class="even">
                        <?php displayRegions();	?>
                    </td>
                </tr>
                
<?php } if ($ADRESSCHECK == "1") { ?>
				<tr>
					<td class="odd"><span id="firstname_label"><?php echo $webui_first_name ?>*</span></td>
					<td class="odd"><div class="roundedinput">
						<input require="true" label="firstname_label" id="register_input" name="firstname" type="text" size="25" maxlength="15" value="<?php echo isset($_POST['firstname']) ? $_POST['firstname'] : ''; ?>">
					</div></td>
				</tr>
				<tr>
					<td class="even"><span id="lastname_label"><?php echo $webui_last_name ?>*</span></td>
					<td class="even"><div class="roundedinput">
						<input require="true" label="lastname_label" id="register_input" name="lastname" type="text" size="25" maxlength="15" value="<?php echo isset($_POST['lastname']) ? $_POST['lastname'] : ''; ?>">
					</div></td>
				</tr>
				<tr>
					<td class="odd"><span id="adress_label"><?php echo $webui_address ?>*</span></td>
					<td class="odd"><div class="roundedinput">
						<input require="true" label="adress_label" id="register_input" name="adress" type="text" size="50" maxlength="50" value="<?php echo isset($_POST['adress']) ? $_POST['adress'] : ''; ?>">
					</div></td>
				</tr>
				<tr>
					<td class="even"><span id="zip_label"><?php echo $webui_zip_code ?>*</span></td>
					<td class="even"><div class="roundedinput">
						<input require="true" label="zip_label" id="register_input" name="zip" type="text" size="25" maxlength="15" value="<?php echo isset($_POST['zip']) ? $_POST['zip'] : ''; ?>">
					</div></td>
				</tr>
				<tr>
					<td class="odd"><span id="city_label"><?php echo $webui_city ?>*</span></td>
					<td class="odd"><div class="roundedinput">
						<input require="true" label="city_label" id="register_input" name="city" type="text" size="25" maxlength="15" value="<?php echo isset($_POST['city']) ? $_POST['city'] : ''; ?>">
					</div></td>
				</tr>
				<tr>
					<td class="even"><span id="country_label"><?php echo $webui_country ?>*</span></td>
					<td class="even"><?php displayCountry(); ?></td>
				</tr>
                
                <?php } if ($ADRESSCHECK == "1" || $FORCEAGE == "1"){ ?>
                
                <tr>
                    <td class="odd"><span id="dob_label"><?php echo $webui_date_of_birth ?>*</span></td>
                    <td class="odd">
                        <?php displayDOB(); ?>
                    </td>
                </tr>
                
                <?php } ?>
                
				<tr>
					<td class="odd"><span id="email_label"><?php echo $webui_email ?>*</span></td>
					<td class="odd"><div class="roundedinput">
						<input compare="emaic" require="true" label="email_label" id="register_input" name="email" type="text" size="40" maxlength="40" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
					</div></td>
				</tr>
				<tr>
					<td class="even"><span id="emaic_label"><?php echo $webui_confirm ?> <?php echo $webui_email ?>*</span></td>
					<td class="even"><div class="roundedinput">
						<input require="true" label="emaic_label" id="register_input" name="emaic" type="text" size="40" maxlength="40" value="<?php echo (isset($_POST['email'], $_POST['emaic']) && $_POST['email'] === $_POST['emaic']) ? $_POST['emaic'] : ''; ?>" >
					</div></td>
				</tr>
				
<?php displayDefaultAvatars(); ?>
<?php if( file_exists( $_SERVER['DOCUMENT_ROOT'] . "/TOS.php "))  { ?>
				<tr>
					<td class="even" colspan="2"><div style="width:100%;height:300px;overflow:auto;">
						<?php include("tos.php "); ?>
					</div></td>
				</tr>
				<tr>
					<td colspan="2" valign="top" class="odd"><input label="agree_label" require="true" type="checkbox" name="Agree_with_TOS" id="agree" value="1" />
						<label for="agree"><span id="agree_label"><?echo $site_terms_of_service_agree; ?></span></label>
					</td>
				</tr>
<?php } ?>
				<tr>
					<td class="even"><div class="center">
<?php
	if(RECAPTCHA_PUBLIC_KEY !== false && RECAPTCHA_PRIVATE_KEY !== false){
		echo
			"<script type=\"text/javascript\">var RecaptchaOptions = {theme : '",$template_captcha_color,"'};</script>",
			recaptcha_get_html(RECAPTCHA_PUBLIC_KEY) // you got this from the signup page
		;
	}
?>
					</div></td>

					<td class="even"><div class="center">
						<input type="hidden" name="action" value="check">
						<button id="register_bouton" name="submit" type="submit"><?php echo $webui_create_new_account ?></button>
					</div></td>
				</tr>
			</table>
		</form>
	</div>
</div>
<?php }else{ ?>

<div id="content">
	<div id="ContentHeaderLeft"><h5><?php echo SYSNAME; ?></h5></div>
	<div id="ContentHeaderCenter"></div>
	<div id="ContentHeaderRight"><h5><?php echo $webui_register; ?></h5></div>
	<div id="alert">
		<p><?php echo $webui_registrations_disabled; ?></p>
	</div>
</div>

<?php } ?>
