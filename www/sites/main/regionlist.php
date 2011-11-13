<?php
$GoPage= "index.php?page=regionlist";
$Link1 = '';

$AnzeigeStart = 0;
$AStart = isset($_GET['AStart']) ? $_GET['AStart'] : $AnzeigeStart;

$ALimit = 10;
// LINK SELECTOR
$LinkAusgabe="page=index.php?page=regionlist&";

$PDODB = Aurora\WebUI\DB::i();
$count = (integer)$PDODB['Aurora']->query('SELECT COUNT(*) FROM ' . C_REGIONS_TBL . ' WHERE !(Flags & 512) && !(Flags & 1024)')->fetchColumn();

$sitemax=ceil($count / 10);
$sitestart=ceil($AStart / 10)+1;
if($sitemax == 0){$sitemax=1;}
?>

<div id="content">
  <div id="ContentHeaderLeft"><h5><p><?php echo SYSNAME; ?></p></h5></div>
  <div id="ContentHeaderCenter"></div>
  <div id="ContentHeaderRight"><h5><p><?php echo $webui_region_list; ?></p></h5></div>
  <div id="regionlist">
	<div id="info"><p><?php echo $webui_region_list_page_info ?></p></div>
	<table>
		<tr>
			<td>
				<p><?php echo $count; ?> <?php echo $webui_regions_found; ?><p>
			</td>
			<td>
			<div id="region_navigation">
				<table>
					<tr>
						<td>
							<a href="<?php echo $GoPage,'&',$Link1; ?>AStart=0&amp;ALimit=<?php echo $ALimit; ?>" target="_self" title="<?php echo $webui_pagination_tooltips_back_begin; ?>">
								<img SRC=images/icons/icon_back_more_<?php echo (0 > ($AStart - $ALimit)) ? 'off' : 'on' ?>.gif WIDTH=15 HEIGHT=15 border="0" />
							</a>
						</td>
						<td>
							<a href="<?php echo $GoPage,'&',$Link1; ?>AStart=<?php  echo (0 > ($AStart - $ALimit)) ? 0 : $AStart - $ALimit; ?>&amp;ALimit=<?php echo $ALimit; ?>" target="_self"  title="<?php echo $webui_pagination_tooltips_back_page; ?>">
								<img SRC=images/icons/icon_back_one_<?php echo (0 > ($AStart - $ALimit)) ? 'off' : 'on' ?>.gif WIDTH=15 HEIGHT=15 border="0" />
							</a>
						</td>
						<td>
						  	<p><?php echo $webui_navigation_page, ' ', $sitestart, ' ', $webui_navigation_of, ' ', $sitemax ?></p>
						</td>
						<td>
							<a href="<?php echo $GoPage,'&',$Link1; ?>AStart=<?php echo ($count <= ($AStart + $ALimit)) ? 0 : ($AStart + $ALimit); ?>&amp;ALimit=<?php echo $ALimit; ?>" target="_self" title="<?php echo $webui_pagination_tooltips_forward_page; ?>">
								<img SRC=images/icons/icon_forward_one_<?php echo ($count <= ($AStart + $ALimit)) ? 'off' : 'on' ?>.gif WIDTH=15 HEIGHT=15 border="0" />
							</a>
						</td>
						<td>
							<a href="<?php echo $GoPage,'&',$Link1; ?>AStart=<?php echo (0 > ($count <= ($AStart + $ALimit))) ? 0 : (($sitemax - 1) * $ALimit); ?>&amp;ALimit=<?php echo $ALimit; ?>" target="_self"  title="<?php echo $webui_pagination_tooltips_last_page; ?>">
								<img SRC=images/icons/icon_forward_more_<?php echo ($count <= ($AStart + $ALimit)) ? 'off' : 'on' ?>.gif WIDTH=15 HEIGHT=15 border="0" />
							</a>
						</td>
						<td></td>
<?php
	$_webui_pagination_tooltips_tds = array(
		10  => array( $webui_pagination_tooltips_show10 , $webui_pagination_tooltips_limit10) ,
		25  => array( $webui_pagination_tooltips_show25 , $webui_pagination_tooltips_limit25) ,
		50  => array( $webui_pagination_tooltips_show50 , $webui_pagination_tooltips_limit50) ,
		100 => array( $webui_pagination_tooltips_show100, $webui_pagination_tooltips_limit100)
	);
	foreach($_webui_pagination_tooltips_tds as $k=>$v){
?>
						<td>
							<a href="<?php echo $GoPage,'&',$Link1; ?>AStart=0&amp;ALimit=<?php echo $k; ?>&amp;" target="_self" title="<?php echo $v[0]; ?>">
								<img SRC=images/icons/<?php echo ($ALimit != $k) ? 'icon_limit_' . $k . '_on' : 'icon_limit_off'; ?>.gif WIDTH=15 HEIGHT=15 border="0" ALT="<?php echo $v[1]; ?>" />
							</a>
						</td>
<?php } ?>
					</tr>
				</table>
				</div>
			</td>
		</tr>
	</table>
	<table>
		<thead>
			<tr>
				<td width="55%">
					<p><?php echo $webui_region_name; ?></p>
				</td>
				<td width="15%">
					<p><?php echo $webui_location; ?>: X</p>
				</td>
				<td width="15%">
					<p><?php echo $webui_location; ?>: Y</p>
				</td>
				<td width="15%">
					<p><?php echo $webui_info ?></p>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="4">
					<table>
						<tbody>
						<?php
							$regionIterator = Aurora\WebUI\RegionIteratorByNameFromDB::r($PDODB['Aurora']);
							$regionIterator->seek(isset($_GET['AStart']) ? $_GET['AStart'] : 0);
							$w=0;
							while($regionIterator->valid()){
							$w++;
								$region = $regionIterator->current();
						?>
							<tr class="<?php echo ($odd = $w%2 )? "even":"odd"; ?>" >
								<td width="55%">
									<div><p><?php echo $region->RegionName(); ?></p></div>
								</td>
								<td width="15%">
									<div><p><?php echo $region->LocX()/256; ?></p></div>
								</td>
								<td width="15%">
									<div><p><?php echo $region->LocY()/256; ?></p></div>
								</td>
								<td width="15%">
									<div>
										<a onClick="window.open('<?php echo SYSURL,'app/region/?x=',$region->LocX(),'&y=',$region->locY();?>','mywindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=400')">
											<p><?php echo $webui_more_info ?></p>
										</a>
									</div>
								</td>
							</tr>
						<?php
								$regionIterator->next();
							}
						?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
</div>
