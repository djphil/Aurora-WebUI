<?
////////////////////////////////// ADMIN ///////////////////////////////////////


if ($_SESSION[ADMINID]) {
$DbLink = new DB;

if ($_GET[delete] == 1) {
    $DbLink->query("DELETE from " . C_NEWS_TBL . " WHERE (id = '" . cleanQuery($_GET[id]) . "')");
}

if ($_POST[infobox] == "save") {
    $message = str_replace("'", "\'", "$_POST[infomessage]");
    $DbLink->query("UPDATE " . C_INFOWINDOW_TBL . " SET gridstatus='" . cleanQuery($_POST[gridstatus]) . "',active='" . cleanQuery($_POST[boxstatus]) . "',color='" . cleanQuery($_POST[boxcolor]) . "',title='" . cleanQuery($_POST[infotitle]) . "',message='" . cleanQuery($message) . "'");
}
} else {

    echo "<script language=\"javascript\">
<!--
window.location.href=\"index.php?page=home\";
// -->
</script>";
}

$DbLink->query("SELECT gridstatus,active,color,title,message  FROM " . C_INFOWINDOW_TBL . " ");
list($gridstatus, $boxstatus, $boxcolor, $infotitle, $infomessage) = $DbLink->next_record();
////////////////////////////////// ADMIN END ///////////////////////////////////
?>


<div id="content">
    <div id="ContentHeaderLeft"><h5><?= SYSNAME ?></h5></div>
    <div id="ContentHeaderCenter"></div>
    <div id="ContentHeaderRight"><h5><?php echo $webui_admin_edit_loginscreen; ?></h5></div>

    <div id="loginscreen_manager">

        <div id="info">
            <p><?php echo $webui_admin_loginscreen_info ?></p>
        </div>

        <table>
            <form action="index.php?page=adminloginscreen" method="post">
                <input type="hidden" name="infobox" value="save" />

                <tr>
                    <td>
                        <div align="right">
                            <?php echo $webui_admin_grid_status ?>
                        </div>
                    </td>

                    <td>
                        <font>
                            <select name="gridstatus" id="gridstatusselectbox">
                                <option value="1" style="background-color:#00FF00" <? if ($gridstatus == "1") {
                                echo"selected";
                            } ?>>Online</option>
                                <option value="0" style="background-color:#FF0000" <? if ($gridstatus == "0") {
                                echo"selected";
                            } ?>>Offline</option>
                            </select>
                        </font>
                    </td>

                    <td>
                        <div align="right">
<?php echo $webui_admin_windows_status ?>
                        </div>
                    </td>

                    <td>
                        <font>
                            <select name="boxstatus" id="boxstatus">
                                <option value="1" style="background-color:#00FF00" <? if ($boxstatus == "1") {
                                echo"selected";
                            } ?>>Active</option>
                                <option value="0" style="background-color:#FF0000" <? if ($boxstatus == "0") {
                                echo"selected";
                            } ?>>Inactive</option>
                            </select>
                        </font>
                    </td>

                    <td>
                        <div align="right">
<?php echo $webui_admin_windows_color ?>
                        </div>
                    </td>

                    <td>
                        <font>
                            <select name="boxcolor" id="boxcolor">
                                <option value="white" style=" background-color:#FFFFFF" <? if ($boxcolor == "white") {
                                echo"selected";
                            } ?>>white</option>
                                <option value="green" style="background-color:#00FF00"  <? if ($boxcolor == "green") {
                                echo"selected";
                            } ?>>green</option>
                                <option value="yellow" style="background-color:#FFFF00" <? if ($boxcolor == "yellow") {
                                echo"selected";
                            } ?>>yellow</option>
                                    <option value="red" style="background-color:#FF0000" <? if ($boxcolor == "red") {
                                echo"selected";
                            } ?>>red</option>
                                </select>
                            </font>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6"></td>
                    </tr>

                    <tr>
                        <td>
<?php echo $webui_admin_windows_title ?>
                        </td>

                        <td colspan="5">
                            <input name="infotitle" type="text" id="infotitle" size="119" value="<?= $infotitle ?>" />
                        </td>
                    </tr>

                    <tr>
                        <td>
<?php echo $webui_admin_windows_message ?>
                        </td>

                        <td colspan="5">
                            <font>
                                <textarea name="infomessage" cols="90" rows="10" id="infomessage"><?= $infomessage ?></textarea>
                            </font>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6">
                            <div align="center">
                                <input type="submit" name="Submit" value="<?php echo $webui_admin_windows_settings ?>" />
                        </div>
                    </td>
                </tr>
            </form>
        </table>
    </div>
</div>
