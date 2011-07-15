<div id="content">
  <div id="ContentHeaderLeft"><h5><?php echo SYSNAME; ?></h5></div>
  <div id="ContentHeaderCenter"></div>
  <div id="ContentHeaderRight"><h5><?php echo $webui_help; ?></h5></div>
  <div id="help">
  <div id="info"><p><?php echo $webui_help_info; ?></p></div>
  
<div id="info1"><h3><?php echo $webui_help_title_comment01; ?></h3> 
  <p><?php echo $webui_help_comment01; ?></p>
</div>
  
<div id="info2"><h3><?php echo $webui_help_title_comment02; ?></h3>
  <p><?php echo $webui_help_comment02; ?></p>
</div>

<div id="info3"><h3><?php echo $webui_help_title_comment03; ?></h3>
  <p><?php echo $webui_help_comment03; ?></p>
</div>


<p>
<?php
  $DbLink2 = new DB;
  $DbLink = new DB;

  $Display = (integer)(isset($_SESSION['USERID']) && $_SESSION['USERID']);

  $AdminDisplay = (isset($_SESSION['ADMINID']) && $_SESSION[ADMINID]) ? " or (display='3')" : "";
  $DbLink2->query("SELECT id,url,target FROM " . C_PAGE_TBL . " Where parent = '".cleanQuery(isset($_GET['btn']) ? $_GET['btn'] : '')."' and active='1' and ((display='$Display') or (display='2') " . $AdminDisplay . ") ORDER BY rank ASC ");
  $a = get_defined_vars();
    
  while (list($siteid, $siteurl, $sitetarget) = $DbLink2->next_record()) 
  {
	  echo "<a href=\"$siteurl&btn=$siteid\"><span>$a[$siteid]</span></a><br/>";
  }
?>
</p></div></div>
