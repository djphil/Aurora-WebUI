<div id="content">
  <div id="ContentHeaderLeft"><h5><?php echo SYSNAME; ?></h5></div>
  <div id="ContentHeaderCenter"></div>
  <div id="ContentHeaderRight"><h5><?php echo $webui_news; ?></h5></div>
  
  	<div class="clear"></div>
        	
  <div id="news">
    <div id="info"><p><?php echo $webui_news; ?></p></div>
    
<?php

                $querypage = 0;
                if(isset($_GET['pagenum']) && $_GET['pagenum'] != "") {
                    $querypage = cleanQuery($_GET[pagenum]);
                }
                $showNext = true;
                $DbLink = new DB;
                $DbLink->query("SELECT COUNT(*) from " . C_NEWS_TBL);
while (list($count) = $DbLink->next_record()) {
if($querypage*5 + 5 > $count)
    $showNext = false;
}
                ?>
<!-- STYLE TO DO -->
        <div style="text-align: left; width: 50%; float: left;">
        <?php
        if($querypage > 0) { ?>
            <a href="<?php echo SYSURL; ?>index.php?page=news&pagenum=<?php echo $querypage-1; ?>">Previous Page</a>
            <?php } ?>&nbsp;
        </div>
        <div style="text-align: right; width: 50%; float: left;">
            <?php
            if($showNext) { ?>
            <a href="<?php echo SYSURL; ?>index.php?page=news&pagenum=<?php echo $querypage+1; ?>">Next Page</a>
            <?php } ?>&nbsp;
        </div>
<!-- STYLE TO DO -->        
        	
            <table>
                <?php
                $query = "";
                if(isset($_GET['scr']) && $_GET['scr'] != "") {
                    $query = " where id='".cleanQuery($_GET[scr])."'";
                }
                $querypage = $querypage * 5;
                $DbLink->query("SELECT id,title,message,time,user from " . C_NEWS_TBL . $query . " ORDER BY time DESC LIMIT $querypage,".($querypage+5));
                $count = 0;

                while (list($id, $title, $message, $time, $user) = $DbLink->next_record()) {
                    $count++;

                    if (strlen($title) > 92) {
                        $title = substr($title, 0, 92);
                        $title .= "...";
                    }
                    $TIMES = date("l M d Y", $time);
                ?>

                    <tr>
                        <td width="100"><div class="news_time"><b><?php echo $TIMES; ?></b>
                        <br><br>
                        <b>By <?php echo $user;?>
                        </div></td>
                        <td><div class="news_title"><h3> <a href="<?php echo SYSURL; ?>index.php?page=news&scr=<?php echo $id; ?>" ><?php echo $title; ?></a></h3></div></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><div class="news_content"><?php echo $message; ?></div></td>
                    </tr>

                    <tr>
                        <td colspan="2"><hr /></td>
                    </tr>
                <?php } $DbLink->clean_results();
				
                $DbLink->close(); ?>
            </table>
	  </div>
</div>
