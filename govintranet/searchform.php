<?php

//display random search nudge
$randex = '';
$placeholder = get_option('options_search_placeholder'); //get search placeholder text and variations
if ($placeholder!=''){
	$placeholder = explode( ",", $placeholder );
	srand();
	$randdo = rand(1,5);//1 in 5 chance of showing a variation
	$randpl = rand(2,count($placeholder))-1;//choose a random variation
	if ($randdo==1 && $randpl > 1) {
		$randex=trim($placeholder[$randpl]);
	} else {
		$randex=trim($placeholder[0]);
	}
} else {
	$randex = __("Search","govintranet");
}	
?>
	<form class="form-horizontal" role="form" id="searchform" name="searchform" action="<?php echo site_url( '/' ); ?>">
	  <div class="row">
		  <div class="input-group">
			 <label for="s" class="sr-only"><?php _e('Search','govintranet'); ?></label>
	    	 <input type="text" class="form-control" placeholder="<?php echo $randex ;?>" name="s" id="s" value="<?php echo the_search_query();?>">
			 <span class="input-group-btn">
			<label for="searchbutton" class="sr-only"><?php _e('Search','govintranet'); ?></label>	 
	    	 <?php
		    	 $icon_override = get_option('options_search_button_override', false); 
		    	 if ( isset($icon_override) && $icon_override ):
			    	 $override_text = esc_attr(get_option('options_search_button_text', __('Search', 'govintranet') ));
					 ?>
			 		<button class="btn btn-primary" id="searchbutton" type="submit"><?php echo $override_text; ?></button>
				 	<?php 
		    	 else:
			    	 ?>
			 		<button class="btn btn-primary" id="searchbutton" type="submit"><span class="dashicons dashicons-search"></span><span class="sr-only"><?php _e('Search','govintranet'); ?></span></button>
				 	<?php 
				 endif;
				 ?>
		 	</span>
		</div><!-- /input-group -->
	  </div>
	</form>
	<script type='text/javascript'>
	    jQuery(document).ready(function(){
			jQuery('#searchform').submit(function(e) {
			    if (jQuery.trim(jQuery("#s").val()) === "") {
			        e.preventDefault();
			        jQuery('#s').focus();
			    }
			});	
		});	
	
	</script>
