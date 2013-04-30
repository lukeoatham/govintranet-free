<?php
$q = $_GET['s'];
?>

<form role="search" method="get" id="searchform" action="/">
    <div><label class="screen-reader-text hiddentext" for="s">Search for:</label>
        <input type="text" value="<?php echo $q; ?>" name="s" id="s" accesskey='4' />
        <input type="submit" id="searchsubmit" value="Search" />
        <?php if ( pods_url_variable(1)=='forums' || $_GET['pt']=='forums'){
	     echo "<input type='hidden' name='pt' value='forums' />";   
        }
        ?>
    </div>
</form>