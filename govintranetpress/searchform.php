<?php
$q = $_GET['s'];

//display random search nudge
$randex = '';
$placeholder = get_option('general_intranet_search_placeholder');
if ($placeholder){
	$placeholder = explode( ",", $placeholder );
	$randpl = rand(2,count($placeholder))-1;
	$randdo = rand(1,5);
	if ($randdo==1 && $randpl > 1) {
		$randex=trim($placeholder[$randpl]);
	} else {
		$randex=trim($placeholder[0]);
	}
} else {
	$randex = "Search";
}	
?>
<div class="row">
	<form class="form-horizontal" role="form" id="searchform" name="searchform" action="<?php echo home_url( '/' ); ?>">

	  <div class="col-lg-12">
		  <div class="input-group">
			    	 <input type="text" class="form-control typeahead" placeholder="<?php echo $randex ;?>" name="s" id="s" value="<?php echo $_GET['s'];?>">
					 <span class="input-group-btn">
					 <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
					 </span>
		</div><!-- /input-group -->
	  </div>

	</form>
</div>

