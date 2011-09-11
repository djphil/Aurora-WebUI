<?php
/*
 * Copyright (c) 2007 - 2011 Contributors, http://opensimulator.org/, http://aurora-sim.org/
 * See CONTRIBUTORS for a full list of copyright holders.
 *
 * See LICENSE for the full licensing terms of this file.
 *
*/
 
function WriteMenu($siteid, $siteurl, $sitetarget, $a, $Display, $AdminDisplay)
{
	$DbLink2 = new DB;
	$DbLink2->query("SELECT id,url,target FROM " . C_PAGE_TBL . " Where parent = '".cleanQuery($siteid)."' and active='1' and ((display='$Display') or (display='2') " . $AdminDisplay . ") ORDER BY rank ASC ");
	if ($siteurl != "") {
		if ($sitetarget == '_self') {
			if (isset($_GET['btn']) && $_GET['btn'] == $siteid) 
			{
				echo "<li><a href=\"#\"><span>$a[$siteid]</span></a>";
				if ($DbLink2->num_rows() > 0)
				{
					echo "<ul>";
					while (list($siteid2, $siteurl2, $sitetarget2) = $DbLink2->next_record()) 
					{
						WriteMenu($siteid2, $siteurl2, $sitetarget2, $a, $Display, $AdminDisplay);
					}
					echo "</ul>";
				}
				echo "</li>";
			} 
			else 
			{
				echo "<li><a href=\"$siteurl\"><span>$a[$siteid]</span></a>";
				if ($DbLink2->num_rows() > 0)
				{
					echo "<ul>";
					while (list($siteid2, $siteurl2, $sitetarget2) = $DbLink2->next_record()) 
					{
						WriteMenu($siteid2, $siteurl2, $sitetarget2, $a, $Display, $AdminDisplay);
					}
					echo "</ul>";
				}
				echo "</li>";
			}
		}
                else if ($sitetarget == '_external') {
                    echo "<li><a href=\"$siteurl\"><span>$a[$siteid]</span></a>";
                            if ($DbLink2->num_rows() > 0)
                            {
                                    echo "<ul>";
                                    while (list($siteid2, $siteurl2, $sitetarget2) = $DbLink2->next_record())
                                    {
                                            WriteMenu($siteid2, $siteurl2, $sitetarget2, $a, $Display, $AdminDisplay);
                                    }
                                    echo "</ul>";
                            }
                            echo "</li>";
                }
                else if ($sitetarget == '_none') {
                    echo "<li><a><span>$a[$siteid]</span></a>";
                            if ($DbLink2->num_rows() > 0)
                            {
                                    echo "<ul>";
                                    while (list($siteid2, $siteurl2, $sitetarget2) = $DbLink2->next_record())
                                    {
                                            WriteMenu($siteid2, $siteurl2, $sitetarget2, $a, $Display, $AdminDisplay);
                                    }
                                    echo "</ul>";
                            }
                            echo "</li>";
                }
		else 
		{
			echo "<li><a href=\"#\" onclick=\"window.open('$siteurl','mywindow','')\"><span>$a[$siteid]</span></a>";
			if ($DbLink2->num_rows() > 0)
			{
				echo "<ul>";
				while (list($siteid2, $siteurl2, $sitetarget2) = $DbLink2->next_record()) 
				{
					WriteMenu($siteid2, $siteurl2, $sitetarget2, $a, $Display, $AdminDisplay);
				}
				echo "</ul>";
			}
			echo "</li>";
		}
	} 
	else 
	{
		echo "<li><a href=\index.php?&page=smodul&id=$siteid\"><span>$a[$siteid]</span></a></li>";
	}
 }
?>

<div id="megamenu" class="<?php echo $MegaMenuSkin; ?>">
    <ul id="mega-menu-<?php echo $MegaMenuPreset; ?>" class="mega-menu">
    <?php
		$DbLink = new DB;
		$Display      = (integer)(isset($_SESSION['USERID']) && $_SESSION['USERID']);
		$AdminDisplay = (isset($_SESSION['ADMINID']) && $_SESSION['ADMINID']) ? " or (display='3')" : '';

		$DbLink->query("SELECT id,url,target FROM " . C_PAGE_TBL . " Where parent is null and active='1' and ((display='$Display') or (display='2') " . $AdminDisplay . ") ORDER BY rank ASC ");
		// $tableWidth = 1000 / $DbLink->num_rows();
		$a = get_defined_vars();
		while (list($siteid, $siteurl, $sitetarget) = $DbLink->next_record()) 
		{
			WriteMenu($siteid, $siteurl, $sitetarget, $a, $Display, $AdminDisplay);
		}
    ?>
    </ul>
</div>
