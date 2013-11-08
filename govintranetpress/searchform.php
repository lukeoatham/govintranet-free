<?php
$q = $_GET['s'];
$randex = rand(1,15);
if ($randex==1) $randex=" e.g. book a meeting room";
if ($randex==2) $randex=" e.g. get an eye test";
if ($randex==3) $randex=" e.g. return to work form";
if ($randex > 3) $randex="";
?>
<div class="row">
	<form class="form-horizontal" role="form" id="searchform" name="searchform" action="<?php echo home_url( '/' ); ?>">

	  <div class="col-lg-12">
		  <div class="input-group">
			    	 <input type="text" class="form-control typeahead" placeholder="Search<?php echo $randex ;?>" name="s" id="s" value="<?php echo $_GET['s'];?>">
					 <span class="input-group-btn">
					 <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
					 </span>
		</div><!-- /input-group -->
	  </div>
    


	</form>
</div>

