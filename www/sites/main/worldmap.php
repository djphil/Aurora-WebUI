<div id="content">
  <div id="ContentHeaderLeft"><h5><?php echo SYSNAME; ?>: <?php echo $webui_world_map ?></h5></div>
  <div id="ContentHeaderCenter"></div>
  <div id="ContentHeaderRight">
  <h5><a <?= "onclick=\"window.open('".SYSURL."app/map/index.php','mywindow')\"" ?> style="float:right; display:inline-block;"><?php echo $webui_fullscreen; ?></a></h5></div>

<br /><br /><br /><br /><br />

  <div id="region_map">

    <iframe src="<?php echo SYSURL; ?>app/map/index.php" frameborder="0" width="100%" height="100%">
    <p>Your browser does not support iframes.</p>
  </iframe>
  </div>
</div>
