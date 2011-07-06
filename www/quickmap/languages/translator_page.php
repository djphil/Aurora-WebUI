<!-- Test Translation  -->
<div>
    <?php echo $webui_actual_language; ?>
    <?php
      foreach ($languages as $langCode => $langName) {
        if ($langCode != $webui_language_code) {
        echo ' <a href="?page=',(isset($_GET['page']) ? $_GET['page'] : null),'&btn=',$_GET['btn'],'&lang=',$langCode,'"><img src="images/flags/flag-',$langCode,'.png" alt=" ',$langName,'" title=" ',$langName,'" /></a>';
        }
      } 
    ?>
</div>

