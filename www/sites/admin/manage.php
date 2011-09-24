<?php
if($_SESSION['ADMINID']) {
$Link1 = $Link2 = '';
        $GoPage= "page=adminmanage";

        $AnzeigeStart 		= 0;
        $AnzeigeLimit		= 25;

// LINK SELECTOR

        if(isset($_POST['query'])) {
            $Link2.='query=' . $_POST['query'] . '&';
        }

        $AStart = isset($AStart) ? $AStart : $AnzeigeStart;
        $ALimit = isset($ALimit) ? $ALimit : $AnzeigeLimit;

        $Limit = sprintf('LIMIT %1$u, %2%u',$AStart, $ALimit);

	if(isset($_GET['action2'], $_GET['quest'])){
//DELETE USER START
        if(($_GET['action2'] == '$webui_admin_manage_userdelete') and ($_GET['quest'] == 'yes')) {
            $found = array();
            $found[0] = json_encode(array('Method' => 'DeleteUser', 'WebPassword' => md5(WIREDUX_PASSWORD),
                    'UserID' => cleanQuery($_GET['user_id'])));
            $do_post_request = do_post_request($found);
        }
//DELETE USER END

//BAN USER START
        if(($_GET['action2'] == '$webui_admin_manage_userban') and ($_GET['quest'] == 'yes')) {
            $found = array();
            $found[0] = json_encode(array('Method' => 'BanUser', 'WebPassword' => md5(WIREDUX_PASSWORD),
                    'UserID' => cleanQuery($_GET['user_id'])));
            $do_post_request = do_post_request($found);
        }
//BAN USER END

//UNBAN USER START
        if(($_GET['action2'] == '$webui_admin_manage_userunban') and ($_GET['quest'] == 'yes')) {
            $found = array();
            $found[0] = json_encode(array('Method' => 'UnBanUser', 'WebPassword' => md5(WIREDUX_PASSWORD),
                    'UserID' => cleanQuery($_GET['user_id'])));
            $do_post_request = do_post_request($found);
        }
//UNBAN USER END
	}
        $DbLink = new DB;
        $DbLink->query("SELECT COUNT(*) FROM ".C_USERS_TBL." ");
        list($count) = $DbLink->next_record();
?>

 <!-- <br><center> -->
        <?php
// DELETE QUESTION
        if(isset($_GET['action']) && $_GET['action'] == '$webui_admin_manage_userdelete') {

            echo "<TABLE border=1 WIDTH=95% BGCOLOR=#FF0000><TR><TD><center>";
            echo '<FONT COLOR=#FFFFFF><B>Do you want to delete the User ',$_GET['delusr'],'?</B>&nbsp;&nbsp;&nbsp;&nbsp; <a href="index.php?page=manage&action2=delete&quest=yes&uname=',$_GET['delusr'],'&user_id=',$_GET['user_id'],"><FONT COLOR=#FFFFFF><b>YES</b></font></a><FONT COLOR=#FFFFFF><b> / </b></font><a href='index.php?page=manage'><FONT COLOR=#FFFFFF><b>NO</b></font></a>";
            echo "<br></center></TD></TR></TABLE><br>";
        }
//DELETE ANSWER
        if(isset($_GET['action2'], $_GET['quest']) && ($_GET['action2'] == '$webui_admin_manage_userdelete') and ($_GET['quest'] == 'yes')) {
            echo "<TABLE WIDTH=95% BGCOLOR=#FF0000><TR><TD>";
            echo '<FONT COLOR=#FFFFFF><B>',$_GET['uname'],' successfully deleted</B>';
            echo "</TD></TR></TABLE>";
        }

// BAN QUESTION
        if(isset($_GET['action']) && $_GET['action'] == '$webui_admin_manage_userban') {

            echo "<TABLE border=1 WIDTH=95% BGCOLOR=#FF0000><TR><TD><center>";
            echo '<FONT COLOR=#FFFFFF><B>Do you want to Ban ',$_GET['banusr'],'?</B>&nbsp;&nbsp;&nbsp;&nbsp; <a href="index.php?page=manage&action2=ban&quest=yes&uname=',$_GET['banusr'],'&user_id=',$_GET['user_id'],"><FONT COLOR=#FFFFFF><b>YES</b></font></a><FONT COLOR=#FFFFFF><b> / </b></font><a href='index.php?page=manage'><FONT COLOR=#FFFFFF><b>NO</b></font></a>";
            echo "<br></center></TD></TR></TABLE><br>";
        }
//BAN ANSWER
        if(isset($_GET['action2'], $_GET['quest']) && ($_GET['action2'] == '$webui_admin_manage_userban') and ($_GET['quest'] == 'yes')) {
            echo "<TABLE WIDTH=95% BGCOLOR=#FF0000><TR><TD>";
            echo '<FONT COLOR=#FFFFFF><B>',$_GET['uname'],' successfully banned</B>';
            echo "</TD></TR></TABLE>";
        }

// UNBAN QUESTION
        if(isset($_GET['action']) && $_GET['action'] == '$webui_admin_manage_userunban') {

            echo "<TABLE border=1 WIDTH=95% BGCOLOR=#FF0000><TR><TD><center>";
            echo '<FONT COLOR=#FFFFFF><B>Do you want to remove ',$_GET['unbanusr'],' from Ban List?</B>&nbsp;&nbsp;&nbsp;&nbsp; <a href="index.php?page=manage&action2=unban&quest=yes&uname=',$_GET['unbanusr'],'&user_id=',$_GET['user_id'],"><FONT COLOR=#FFFFFF><b>YES</b></font></a><FONT COLOR=#FFFFFF><b> / </b></font><a href='index.php?page=manage'><FONT COLOR=#FFFFFF><b>NO</b></font></a>";
            echo "<br></center></TD></TR></TABLE><br>";
        }
//UNBAN ANSWER
        if(isset($_GET['action2'], $_GET['quest']) && ($_GET['action2'] == '$webui_admin_manage_userunban') and ($_GET['quest'] == 'yes')) {
            echo "<TABLE WIDTH=95% BGCOLOR=#FF0000><TR><TD>";
            echo '<FONT COLOR=#FFFFFF><B>',$_GET['uname'],' successfully removed from Ban List</B>';
            echo "</TD></TR></TABLE>";
        }


        ?>

<div id="content">
    <div id="ContentHeaderLeft"><h5><p><?php echo SYSNAME; ?></p></h5></div>
    <div id="ContentHeaderCenter"></div>
    <div id="ContentHeaderRight"><h5><p><?php echo $webui_admin_manage; ?></p></h5></div>
      
    <div id="managepanel">

        <div id="info">
            <p><?php echo $webui_admin_manage_info; ?></p>
        </div>

        <table>
        <tr>
            <td colspan="2">
            <!--//START LIMIT AND SEARCH ROW -->
            <table>
                <tr>
                    <td>

                    <table>
                        <tr>
                            <td>
                        <font><p><?php echo $count; ?> <?php echo $webui_users_found ?></p></font>
                    </td>

                  <td>                   
        
        <div id="region_navigation">
        <table>
            <tr>
                <td>
                    <a href="index.php?<?php echo $GoPage; ?>&<?php echo $Link1; ?><?php echo $Link2; ?>AStart=0&amp;ALimit=<?php echo $ALimit; ?>" target="_self">
                        <img SRC=images/icons/icon_back_more_<?php echo (0 > ($AStart - $ALimit)) ? 'off' : 'on'; ?>.gif WIDTH=15 HEIGHT=15 border="0">
                    </a>
                </td>
                        
                <td>
                    <a href="index.php?<?php echo $GoPage; ?>&<?php echo $Link1; ?><?php echo $Link2; ?>AStart=<?php if(0 > ($AStart - $ALimit)) echo 0; else echo $AStart - $ALimit; ?>&amp;ALimit=<?php echo $ALimit; ?>" target="_self">
                        <img SRC=images/icons/icon_back_one_<?php echo (0 > ($AStart - $ALimit)) ? 'off' : 'on'; ?>.gif WIDTH=15 HEIGHT=15 border="0">
                    </a>
                </td>
                        
                <td>
                    <p><?php echo $webui_navigation_page; ?> <?php /* echo $LANG_ADMPAYMENT8; /* does not seem to be defined anywhere, but leaving it in rather than deleting it */ ?> <?php echo  round($AStart / $ALimit ,0)+1; ?> <?php echo $webui_navigation_of; ?> <?php echo  @round($count / $ALimit,0); ?></p>
                </td>
                        
                <td>
                    <a href="index.php?<?php echo $GoPage; ?>&<?php echo $Link1; ?><?php echo $Link2; ?>AStart=<?php if($count <= ($AStart + $ALimit)) echo 0; else echo $AStart + $ALimit; ?>&amp;ALimit=<?php echo $ALimit; ?>" target="_self">
                        <img SRC=images/icons/icon_forward_one_<?php echo ($count <= ($AStart + $ALimit)) ? 'off' : 'on'; ?>.gif WIDTH=15 HEIGHT=15 border="0">
                    </a>
                </td>
                        
                <td>
                    <a href="index.php?<?php echo $GoPage; ?>&<?php echo $Link1; ?><?php echo $Link2; ?>AStart=<?php if(0 > ($count - $ALimit)) echo 0; else echo $count - $ALimit; ?>&amp;ALimit=<?php echo $ALimit; ?>" target="_self">
                        <img SRC=images/icons/icon_forward_more_<?php echo (0 > ($count - $ALimit)) ? 'off' : 'on';?>.gif WIDTH=15 HEIGHT=15 border="0">
                    </a>
                </td>
                        
                <td WIDTH="10"></td>
                        
                <td>
                    <a href="index.php?<?php echo $GoPage; ?>&<?php echo $Link1; ?><?php echo $Link2; ?>AStart=0&ALimit=10" target="_self">
                        <img SRC=images/icons/<?php echo ($ALimit != 10) ? 'icon_limit_10_on' : 'icon_limit_off'; ?>.gif WIDTH=15 HEIGHT=15 border="0" ALT="Limit 10">
                    </a>
                </td>
                        
                <td>
                    <a href="index.php?<?php echo $GoPage; ?>&<?php echo $Link1?><?php echo $Link2; ?>AStart=0&ALimit=25" target="_self">
                        <img SRC=images/icons/<?php echo ($ALimit != 25) ? 'icon_limit_25_on' : 'icon_limit_off'; ?>.gif WIDTH=15 HEIGHT=15 border="0" ALT="Limit 25">
                    </a>
                </td>
                
                <td>
                    <a href="index.php?<?php echo $GoPage; ?>&<?php echo $Link1; ?><?php echo $Link2; ?>AStart=0&ALimit=50" target="_self">
                        <img SRC=images/icons/<?php echo ($ALimit != 50) ? 'icon_limit_50_on' : 'icon_limit_off'; ?>.gif WIDTH=15 HEIGHT=15 border="0" ALT="Limit 50">
                    </a>
                </td>
                    
                <td>
                    <a href="index.php?<?php echo $GoPage; ?>&<?php echo $Link1; ?><?php echo $Link2; ?>AStart=0&ALimit=100" target="_self">
                        <img SRC=images/icons/<?php echo ($ALimit != 100) ? 'icon_limit_100_on' : 'icon_limit_off'; ?>.gif WIDTH=15 HEIGHT=15 border="0" ALT="Limit 100">
                    </a>
                </td>
                <td></td>
            </tr>
        </table>
      </div>
    </td>
  </tr>
</table> 
</td></tr>
</table>
</td></tr>
</table>
    

<table>
    <form ACTION="index.php?<?php echo $GoPage; ?>" METHOD="POST">       
        <tr>
            <td>
                <div id="message">
                    <?php echo $webui_admin_manage_username; ?>:
                    <input TYPE="TEXT" NAME="query" SIZE="50" value="<?php echo isset($_POST['query']) ? $_POST['query'] : ''; ?>">
                    <button id="search_bouton" TYPE="Submit" value="<?php echo $webui_people_search_bouton ?>"><?php echo $webui_people_search_bouton ?></button>
                </div>       
            </td>
        </tr>
    </form>
</table>

<table>
    <tr>
    <td>
        <div>
              <table>
                  <tr>
                  <td width=36></td>
                  <td width=113 align="center"><p><?php echo $webui_admin_manage_edit; ?></p></td>
                  <td width=312 align="center"><p><?php echo $webui_admin_manage_username; ?></p></td>
                  <td width=220 align="center"><p><?php echo $webui_admin_manage_created; ?></p></td>
                  <td width=167 align="center"><p><?php echo $webui_admin_manage_active; ?></p></td>
                  <td width=47></td>
                  </tr>
              </table>
                    
            <?php
    						$DbLink3 = new DB; 
                    $found = array();
                		$found[0] = json_encode(array('Method' => 'FindUsers', 'WebPassword' => md5(WIREDUX_PASSWORD),
                      		'UserID' => cleanQuery(isset($_GET['user_id']) ? $_GET['user_id'] : ''), 'Start' => cleanQuery($AStart), 'End' => cleanQuery($ALimit), 'Query' => cleanQuery(isset($_POST['query']) ? $_POST['query'] : '')));
            		    $do_post_request = do_post_request($found);
                    $recieved = json_decode($do_post_request, true);
					$fullUserInfo = (array)$recieved['Users'];
		    foreach($fullUserInfo as $userInfo)
			{
					$user_id = $userInfo['PrincipalID'];
                    $username = $userInfo['UserName'];
                    $created = $userInfo['Created'];
                    $flags = $userInfo['UserFlags'];

                    $create = date("d.m.Y", $created);
            ?>

            <table>
                <tr class="<?php echo ($odd = $w%2 )? "even":"odd" ?>" >
                    <td width=21 align=center>
                        <img src="images/icons/icon_user.png" alt="<?php echo $webui_admin_manage_user; ?>" title="<?php echo $webui_admin_manage_user; ?>">
                    </td>

                    <td width=91 align="center">
                        <a href="index.php?page=adminedit&userid=<?php echo $user_id; ?>">
                            <b><?php echo $webui_admin_manage_edit; ?></b>
                        </a>
                    </td>

                    <td width=243>
                        <b><?php echo $username; ?></b>
                    </td>

                    <td width=173>
                        <b><?php echo $create; ?></b>
                    </td>

                    <td width=100>
                    <b>
                      <?php
                          if(($flags & 7) == 7) {
                              echo"<FONT COLOR=#00FF00><?php echo $webui_admin_manage_active; ?></FONT>";
                              }
                          elseif(($flags & 3) == 3) {
                              echo"<FONT COLOR=#FF0000><?php echo $webui_admin_manage_notconf; ?></FONT>";
                              }
                         elseif(($flags & 5) == 5) {
                              echo"<FONT COLOR=#FF0000><?php echo $webui_admin_manage_banned; ?></FONT>";
                              }
                          else {
                              echo"<FONT COLOR=#FF0000><?php echo $webui_admin_manage_inactive; ?></FONT>";
                              }
                      ?>
                    </b>
                    </td>

                    <td width=21 align=center>
                        <?php if($active ==5) {?>
                        <a href="index.php?<?php echo $GoPage; ?>&action=unban&unbanusr=<?php echo $username; ?>&user_id=<?php echo $user_id; ?>">
                            <img src="images/icons/unban.png" alt="<?php echo $webui_admin_manage_userunban; ?>" title="<?php echo $webui_admin_manage_userunban; ?>">
                        </a>

                        <?php } else { ?>

                        <a href="index.php?<?php echo $GoPage; ?>&action=ban&banusr=<?php echo $username; ?>&user_id=<?php echo $user_id; ?>">
                            <img src="images/icons/ban.png" alt="<?php echo $webui_admin_manage_userban; ?>" title="<?php echo $webui_admin_manage_userban; ?>">
                        </a>
                        <?php } ?>
                    </td>

                    <td width=21 align=center>
                        <a HREF="index.php?<?php echo $GoPage; ?>&action=delete&delusr=<?php echo $username; ?>&user_id=<?php echo $user_id; ?>">
                            <img src="images/icons/btn_del.png" alt="<?php echo $webui_admin_manage_userdelete; ?>" title="<?php echo $webui_admin_manage_userdelete; ?>">
                        </a>
                    </td>
                </tr>
            </table>
            <?php } ?>
        </div>
    </td>
    </tr>
</table>

</div>
</div>



<!-- </center> -->

    <?php }
else {
    echo "<script language=\"javascript\">
<!--
window.location.href=\"index.php?page=home\";
// -->
</script>";
}?>
