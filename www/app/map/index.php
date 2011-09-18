<?php
require_once("../../settings/config.php");
require_once("../../settings/databaseinfo.php");
require_once("../../settings/mysql.php");
require_once('../../settings/entity-types.php');
require_once("../../settings/json.php");

$maxZoom = 8;
$sizes=array(1 => 4,
    2 => 8,
    3 => 16,
    4 => 32,
    5 => 64,
    6 => 128,
    7 => 256,
    8 => 512);

if($ALLOW_ZOOM == TRUE && isset($_GET['zoom']))
{
	foreach ($sizes as $zoomUntested => $sizeUntested) 
	{
		if($zoomUntested == $_GET['zoom'])
		{
		    $zoomSize = $sizeUntested;
		    $zoomLevel = $zoomUntested;
		}
		if($zoomUntested == 9-$_GET['zoom'])
		{
		    $antiZoomSize = $sizeUntested;
		}
	}
}

if ($zoomLevel == 1) {
    $infosize = 4;
} else if ($zoomLevel == 2) {
    $infosize = 5;
} else if ($zoomLevel == 3) {
    $infosize = 7;
} else if ($zoomLevel == 4) {
    $infosize = 10;
} else if ($zoomLevel == 5) {
    $infosize = 10;
} else if ($zoomLevel == 6) {
    $infosize = 20;
} else if ($zoomLevel == 7) {
    $infosize = 30;
} else if ($zoomLevel == 8) {
    $infosize = 40;
}

$mapX = isset($_GET['startx']) ? $_GET['startx'] : $mapstartX;
$mapY = isset($_GET['starty']) ? $_GET['starty'] : $mapstartY;
?>

<head>
  <title><?php echo SYSNAME; ?> World Map</title>
  <style type="text/css" media=all>@import url(map.css);</style>
  <script src="prototype.js" type="text/javascript"></script>
  <script src="effects.js" type="text/javascript"></script>
  <script src="mapapi.js" type="text/javascript"></script>

  <script type="text/javascript">
  function loadmap() 
  {
    <?php if ($ALLOW_ZOOM == TRUE) { ?>    
        if (window.addEventListener)
        /** DOMMouseScroll is for mozilla. */
        window.addEventListener('DOMMouseScroll', wheel, false);
        /* IE/Opera. */
        window.onmousewheel = document.onmousewheel = wheel;
    <?php } ?>

    mapInstance = new ZoomSize(<?php echo $zoomSize ?>);
    mapInstance = new WORLDMap(document.getElementById('map-container'), {hasZoomControls: true, hasPanningControls: true});
    mapInstance.centerAndZoomAtWORLDCoord(new XYPoint(<?php echo $mapX,',',$mapY; ?>),1);
<?php
	class RegionIterator extends Aurora\WebUI\RegionIteratorFromDB{
		const sql_get_uuids =
'SELECT
	RegionUUID
FROM
	gridregions
ORDER BY
	LocX';
	}

	$PDODB = Aurora\WebUI\DB::i();
	$regions = RegionIterator::r($PDODB['Aurora']);

	$regionJSON = array();
	foreach($regions as $regionUUID => $region){
		$regionOwner = Aurora\WebUI\UserFromDB::getRegionOwner($PDODB['AuroraUsers'], $region);

		$MarkerCoordX = $region->LocX();
		$MarkerCoordY = $region->LocY();

		$regionSizeOffset = ($region->SizeX() / 256) * 0.40;

		switch($display_marker){
			case 'tl':
				$MarkerCoordX = ($MarkerCoordX / 256) - $regionSizeOffset;
				$MarkerCoordY = ($MarkerCoordY / 256) + $regionSizeOffset;
			break;
			case 'tr':
				$MarkerCoordX = ($MarkerCoordX / 256) + $regionSizeOffset;
				$MarkerCoordY = ($MarkerCoordY / 256) + $regionSizeOffset;
			break;
			case 'dl':
				$MarkerCoordX = ($MarkerCoordX / 256) - $regionSizeOffset;
				$MarkerCoordY = ($MarkerCoordY / 256) - $regionSizeOffset;
			break;
			case 'dr':
				$MarkerCoordX = ($MarkerCoordX / 256) + $regionSizeOffset;
				$MarkerCoordY = ($MarkerCoordY / 256) - $regionSizeOffset;
			break;
		}

		$info = json_decode($region->Info());

		$filename = $info->serverURI . '/index.php?method=regionImage' . str_replace('-','',$region->RegionUUID());

		$regionJSON[$regionUUID] = array(
			'name'             => $region->RegionName(),
			'serverURI'        => $info->serverURI,
			'LocX'             => $region->LocX(),
			'LocY'             => $region->LocY(),
			'SizeX'            => $region->SizeX(),
			'SizeY'            => $region->SizeY(),
			'Owner'            => $regionOwner->FirstName() . ' ' . $regionOwner->LastName(),
			'MarkerCoordX'     => $MarkerCoordX,
			'MarkerCoordY'     => $MarkerCoordY,
		);
	}
?>
	var
		zoomSize   = <?php echo json_encode($zoomSize); ?>,
		infosize   = <?php echo json_encode($infosize); ?>,
		regionJSON = <?php echo json_encode($regionJSON); ?>
	;
	for(var regionUUID in regionJSON){
		var
			regionData      = regionJSON[regionUUID],
			region_loc      = new Icon(new Img(regionData.serverURI + '/index.php?method=regionImage' + regionUUID.replace(/\-/g,''), zoomSize * (regionData.SizeX / 256), zoomSize * (regionData.SizeY / 256))),
			marker1         = new Marker([region_loc, region_loc, region_loc, region_loc, region_loc, region_loc], new XYPoint(regionData.LocX / 256, regionData.LocY / 256)),
			map_marker_img  = new Img('images/info.gif', infosize, infosize),
			map_marker_icon = new Icon(map_marker_img),
			mapWindow       = new MapWindow(['Region Name: ' + regionData.name, 'Coordinates: ' + (regionData.LocX / 256) + ',' + (regionData.LocY / 256),'Owner: ' + regionData.Owner, '<a href="' + [escape(regionData.name), escape(regionData.SizeX / 256), escape(regionData.SizeY / 256)].join('/') + '">Teleport</a>'].join('<br><br>'),{closeOnMove:true}),
			marker2         = new Marker([map_marker_icon, map_marker_icon, map_marker_icon, map_marker_icon, map_marker_icon, map_marker_icon], new XYPoint(regionData.MarkerCoordX, regionData.MarkerCoordY))
		;

		mapInstance.addMarker(marker1);
		mapInstance.addMarker(marker2, mapWindow);
	}
}

function setZoom(size) {
  var a = mapInstance.getViewportBounds();
  var x = (a.xMin + a.xMax) / 2;
  var y = (a.yMin + a.yMax) / 2;
  window.location.href="<?php echo SYSURL; ?>app/map/?zoom="+size+"&startx="+x+"&starty="+y;
}

function wheel(event){
  var delta = 0;
  if (!event) /* For IE. */
    event = window.event;
  if (event.wheelDelta) { /* IE/Opera. */
    delta = event.wheelDelta/120;
    /** In Opera 9, delta differs in sign as compared to IE.
    */
    if (window.opera)
      delta = -delta;
  }
  
  else if (event.detail) { /** Mozilla case. */
    /** In Mozilla, sign of delta is different than in IE.
    * Also, delta is multiple of 3.
    */
    delta = -event.detail/3;
  }
  
  /** If delta is nonzero, handle it.
  * Basically, delta is now positive if wheel was scrolled up,
  * and negative, if wheel was scrolled down.
  */
  if (delta)
    handle(delta);
        
  /** Prevent default actions caused by mouse wheel.
  * That might be ugly, but we handle scrolls somehow
  * anyway, so don't bother here..
  */
  if (event.preventDefault)
    event.preventDefault();
    event.returnValue = false;
}

function handle(delta) {
  if (delta == 1) { 
    <?php if (($zoomLevel) < $maxZoom) { ?> setZoom(<?php echo ($zoomLevel + 1); ?>);<?php } ?>
  }
  
  else {
    <?php if (($zoomLevel - 1) != 0) { ?>setZoom(<?php echo ($zoomLevel - 1); ?>);<?php } ?>
  }
}
</script>

</head>

<body class="webui" onload=loadmap()>

<div id=map-container style="z-index: 0;"></div>

<div id=map-nav>
  <div id=map-nav-up style="z-index: 1;"><a href="javascript: mapInstance.panUp();">
    <img alt=Up src="images/pan_up.png"></a>
  </div>
  
  <div id=map-nav-down style="z-index: 1;"><a href="javascript: mapInstance.panDown();">
    <img alt=Down src="images/pan_down.png"></a>
  </div>
  
  <div id=map-nav-left style="z-index: 1;"><a href="javascript: mapInstance.panLeft();">
    <img alt=Left src="images/pan_left.png"></a>
  </div>
  
  <div id=map-nav-right style="z-index: 1;"><a href="javascript: mapInstance.panRight();">
    <img alt=Right src="images/pan_right.png"></a>
  </div>
  
  <div id=map-nav-center style="z-index: 1;"><a href="javascript: mapInstance.panOrRecenterToWORLDCoord(new XYPoint(<?php echo $mapstartX,',',$mapstartY; ?>), true);">
    <img alt=Center src="images/center.png"></a>
  </div>
  
  <!-- START ZOOM PANEL-->
  <?php if ($ALLOW_ZOOM == TRUE) { ?>
    <div id=map-zoom-plus>
      <?php if (($zoomLevel + 1) > $maxZoom) { ?>
        <img alt="Zoom In" src="images/zoom_in_grey.png">
        <?php } else { ?>
          <a href="javascript: setZoom(<?php echo ($zoomLevel + 1); ?>);">
            <img alt="Zoom In" src="images/zoom_in.png">
          </a>
        <?php } ?>
    </div>
    
    <div id=map-zoom-minus>
      <?php if (($zoomLevel - 1) == 0) { ?>
        <img alt="Zoom In" src="images/zoom_out_grey.png">
        <?php } else { ?>
          <a href="javascript: setZoom(<?php echo ($zoomLevel - 1); ?>);">
            <img alt="Zoom Out" src="images/zoom_out.png">
          </a>
        <?php } ?>
    </div>
    <?php } ?>
  <!-- END ZOOM PANEL-->
</div>
</body>
