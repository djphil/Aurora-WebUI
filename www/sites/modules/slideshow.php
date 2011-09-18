<script type="text/javascript" src="<?php echo SYSURL; ?>javascripts/jquery/jquery.cycle.all.2.74.js"></script>
<script type="text/javascript" src="<?php echo SYSURL; ?>javascripts/jquery/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="<?php echo SYSURL; ?>javascripts/jquery/jquery.easing.compatibility.js"></script>
<!--  initialize the slideshow when the DOM is ready -->
<script type="text/javascript">
var
	slideshowConfig = <?php echo json_encode(array(
		'fx'      => $SlideShowStyle,
		'timeout' => $SlideShowTimeout,
		'speed'   => $SlideShowSpeed,
		'easing'  => $SlideShowEaseing,
		'delay'   => $SlideShowDelay,
		'sync'    => $SlideShowSync,
//		'next'    => $SlideShowNext,
//		'prev'    => $SlideShowPrev,
		'pause'   => $SlideShowPause,
		'random'  => $SlideShowRandom,
		'pager'   => $SlideShowPager
	)); ?>
;
/*	slideshowConfig.before = onBefore; */
/*	slideshowConfig.after  = onAfter;  */
/*	slideshowConfig.speedIn = 2500;    */
/*	slideshowConfig.speedOut = 500;    */ 
$(document).ready(function() {
    $('.slideshow')
    .before('<div id="nav">') 
    .cycle(slideshowConfig);

});
</script>


<?php if($displaySlideShow) { ?>
  <div class="slideshow">
    <img src="<?php echo SYSURL; ?>images/gallery/image1thumbnail.jpg" title="<?php echo $webui_slideshow_comment01; ?>" alt="" />
  	<img src="<?php echo SYSURL; ?>images/gallery/image2thumbnail.jpg" title="<?php echo $webui_slideshow_comment02; ?>" alt="" />
	 <img src="<?php echo SYSURL; ?>images/gallery/image3thumbnail.jpg" title="<?php echo $webui_slideshow_comment03; ?>" alt="" />
  	<img src="<?php echo SYSURL; ?>images/gallery/image4thumbnail.jpg" title="<?php echo $webui_slideshow_comment04; ?>" alt="" />
  	<img src="<?php echo SYSURL; ?>images/gallery/image5thumbnail.jpg" title="<?php echo $webui_slideshow_comment05; ?>" alt="" />
  </div>
<?php } ?>
