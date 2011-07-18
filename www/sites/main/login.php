<script language="javascript" type="text/javascript">
	addEvent(window, "load", function() { document.getElementById("login_input").focus(); } );
</script>
<div id="content">
    <div id="ContentHeaderLeft"><h5><?php echo SYSNAME; ?></h5></div>
    <div id="ContentHeaderCenter"></div>
    <div id="ContentHeaderRight"><h5><?php echo $webui_login; ?></h5></div>
    <div id="login">

<?php
	$query_args = array();
	if(isset($_GET['next_page']) && empty($_GET['next_page']) === false){
		$query_args['next_page'] = $_GET['next_page'];
	}
	if(isset($_GET['purchase_id']) && empty($_GET['purchase_id']) === false){
		$query_args['purchase_id'] = $_GET['purchase_id'];
	}
?>
        <form action="index.php?<?php echo http_build_query($query_args); ?>" method="POST" onsubmit="if (!validate(this)) return false;">
            <table>
<?php if(isset($_GET['ERROR'])){ ?>
                <tr>
                    <td class="error" colspan="2" align="center" id="error_message"><?php echo $_SESSION['ERROR']; unset($_SESSION['ERROR']);?><?php echo $_GET['ERROR']; ?></td>
                </tr>
<?php } ?>
                <tr>
                    <td class="odd" width="51%"><span id="logname_label"><?php echo $webui_user_name ?>*</span></td>
                    <td class="odd"><div class="roundedinput"><input require="true" label="logname_label" id="login_input" name="logname" type="text" value="<?php echo isset($_POST['logname']) ? $_POST['logname'] : ""; ?>" /></div></td>
                </tr>
                <tr>
                    <td class="even"><span id="password_label"><?php echo $webui_password ?>*</span></td>
                    <td class="even"><div class="roundedinput"><input require="true" label="password_label" id="login_input" type="password" name="logpassword" /></div></td>
                </tr>
                <tr>
                    <td class="odd"><a href="index.php?page=forgotpass"><?php echo $webui_forgot_password ?></a></td>                
                    <td class="odd">
                      <div class="center">
                        <button id="login_bouton" type="Submit" name="Submit" value="<?php echo $webui_login ?>"><?php echo $webui_login; ?></button>
                      </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>