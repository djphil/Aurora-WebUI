<?php
include("../../settings/config.php");
include("../../settings/databaseinfo.php");
include("../../settings/mysql.php");
include('../../settings/entity-types.php');
include("../../settings/json.php");
include("../../languages/translator.php");
include("../../templates/templates.php");

$DbLink = new DB;

$PDODB = Aurora\WebUI\DB::i();
$region = Aurora\WebUI\RegionFromDB::get_by_LocX_LocY($PDODB['Aurora'], $_GET['x'], $_GET['y']);

$locX = $region->LocX() / 256;
$locY = $region->LocY() / 256;
$regionInfo     = json_decode($region->Info());
$regionType     = $regionInfo->regionType;
$regionType     = $regionType == '' ? 'Unknown' : $regionType;
$serverIP       = $regionInfo->{'serverIP'};
$serverHttpPort = $regionInfo->{'serverHttpPort'};


$regionOwner = Aurora\WebUI\UserFromDB::getRegionOwner($PDODB['AuroraUsers'], $region);

$SERVER = "http://$serverIP:$serverHttpPort";
$UUID = str_replace("-", "", $region->RegionUUID());
$source = $SERVER . "/index.php?method=regionImage" . $UUID . "";

/* +++ PRINT NEIGHBORS +++ */
// Array of 8 locations to search for
$locarr['RegionName1'] = "(LocX='" . ($locX - 1) * 256 . "' and LocY='" . ($locY - 1) * 256 . "')";
$locarr['RegionName2'] = "(LocX='" . $locX * 256 . "' and LocY='" . ($locY - 1) * 256 . "')";
$locarr['RegionName3'] = "(LocX='" . ($locX + 1) * 256 . "' and LocY='" . ($locY - 1) * 256 . "')";
$locarr['RegionName4'] = "(LocX='" . ($locX - 1) * 256 . "' and LocY='" . $locY * 256 . "')";
/* This region would go here */
$locarr['RegionName6'] = "(LocX='" . ($locX + 1) * 256 . "' and LocY='" . $locY * 256 . "')";
$locarr['RegionName7'] = "(LocX='" . ($locX - 1) * 256 . "' and LocY='" . ($locY + 1) * 256 . "')";
$locarr['RegionName8'] = "(LocX='" . $locX * 256 . "' and LocY='" . ($locY + 1) * 256 . "')";
$locarr['RegionName9'] = "(LocX='" . ($locX + 1) * 256 . "' and LocY='" . ($locY + 1) * 256 . "')";

/*
$DbLink->query("SELECT RegionName,LocX,LocY FROM " . C_REGIONS_TBL . " where " . implode(" or ", $locarr));
while (list($RegionNameX, $locX1, $locY1) = $DbLink->next_record()) {

    switch ($locX1 / 256) {
        case $locX: //same col
            $regN = 5;
            switch ($locY1 / 256) {
                case $locY - 1: //down one
                    $regN = 8;
                    break;
                case $locY + 1: //up one
                    $regN = 2;
                    break;
            }
            break;
        case $locX - 1: //one left
            $regN = 4;
            switch ($locY1 / 256) {
                case $locY - 1: //down one
                    $regN = 7;
                    break;
                case $locY + 1: //up one
                    $regN = 1;
                    break;
            }
            break;
        default: // one right
            $regN = 6;
            switch ($locY1 / 256) {
                case $locY - 1: //down one
                    $regN = 9;
                    break;
                case $locY + 1: //up one
                    $regN = 3;
                    break;
            }
            break;
    }
    ${"RegionName" . $regN} = "<a href='?x=" . $locX1 . "&y=" . $locY1 . "'>" . $RegionNameX . "</a>";
}
*/

  $DbLink->query("SELECT id,
                         displayTopPanelSlider, 
                         displayTemplateSelector,
                         displayStyleSwitcher,
                         displayStyleSizer,
                         displayFontSizer,
                         displayLanguageSelector,
                         displayScrollingText,
                         displayWelcomeMessage,
                         displayLogo,
                         displayLogoEffect,
                         displaySlideShow,
                         displayMegaMenu,
                         displayDate,
                         displayTime,
                         displayRoundedCorner,
                         displayBackgroundColorAnimation,
                         displayPageLoadTime,
                         displayW3c,
                         displayRss FROM ".C_ADMINMODULES_TBL." ");
                     
  list($id,
       $displayTopPanelSlider,
       $displayTemplateSelector, 
       $displayStyleSwitcher,
       $displayStyleSizer,
       $displayFontSizer,
       $displayLanguageSelector,
       $displayScrollingText,
       $displayWelcomeMessage,
       $displayLogo,
       $displayLogoEffect,
       $displaySlideShow,
       $displayMegaMenu,
       $displayDate,
       $displayTime,
       $displayRoundedCorner,
       $displayBackgroundColorAnimation,
       $displayPageLoadTime,
       $displayW3c,
       $displayRss) = $DbLink->next_record();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="<?php echo SYSURL; ?><?php echo $template_css ?>" type="text/css" />
  <link rel="icon" href="<?php echo SYSURL, $favicon_image; ?>" />
  <title><?php echo SYSNAME; ?>: <?php echo $webui_region_information; ?></title>

<?php if($displayRoundedCorner)  { ?>
<script src="<?php echo SYSURL; ?>javascripts/jquery/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo SYSURL; ?>javascripts/jquery/jquery.corner.js?v2.11"></script>
<script type="text/javascript">
	  $("#regionMap .nosim, #regionMap .thissim, #regionMap tr td").corner("10px");
	  $("#region_picture").corner("15px");
		$("#container_popup, #content_popup").corner();
</script>
<?php } ?>

</head>

<body class="webui">
<div id="container_popup">
<div id="content_popup">
  <h2><?php echo SYSNAME, ': ',$webui_region_information; ?></h2>
  
  <div id="regioninfo">

    <hr>
<!--
    <div id="regionMap">
      <table cellpadding="0" cellspacing="4">
        <tr>
          <td <?php # echo ($RegionName1 ? ">" . $RegionName1 : "class='nosim'>") ?></td>
          <td <?php # echo ($RegionName2 ? ">" . $RegionName2 : "class='nosim'>") ?></td>
          <td <?php # echo ($RegionName3 ? ">" . $RegionName3 : "class='nosim'>") ?></td>
        </tr>
      
        <tr>
          <td <?php # echo ($RegionName4 ? ">" . $RegionName4 : "class='nosim'>") ?></td>
          <td class='thissim'><?php # echo $region->RegionName(); ?></td>
          <td <?php # echo ($RegionName6 ? ">" . $RegionName6 : "class='nosim'>") ?></td>
        </tr>
      
        <tr>
          <td <?php # echo ($RegionName7 ? ">" . $RegionName7 : "class='nosim'>") ?></td>
          <td <?php # echo ($RegionName8 ? ">" . $RegionName8 : "class='nosim'>") ?></td>
          <td <?php # echo ($RegionName9 ? ">" . $RegionName9 : "class='nosim'>") ?></td>
        </tr>
      </table>
    </div>
-->

    <div id="region_picture">
      <img src="<?php echo $source; ?>" alt="<?php echo $region->RegionName(); ?>" title="<?php echo $region->RegionName(); ?>" />
    </div>

    <div id="regiondetails">
      <table>
        <tr>
          <td><?php echo $webui_region_name, ': ', $region->RegionName(); ?></td>
        </tr>
      
        <tr>
          <td><?php echo $webui_region_type, ': ', $regionType; ?></td>
        </tr>
      
        <tr>
          <td><?php echo $webui_location, ' X: ', $locX, ' Y: ', $locY; ?></td>
        </tr>
      
        <tr>
          <td><?php echo $webui_owner; ?>: <a href="<?php echo SYSURL; ?>app/agent/?name=<?php echo urlencode($regionOwner->FirstName() . ' ' . $regionOwner->LastName()); ?>"><?php echo htmlentities($regionOwner->FirstName() . ' ' . $regionOwner->LastName()); ?></a></td>
        </tr>
      </table>
    </div>
  </div>
</div>
</div>
