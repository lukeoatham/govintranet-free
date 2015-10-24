<?php

/* Template name: How do I? page */

get_header();


?>


<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<div class="col-lg-5 col-md-5 col-sm-12 white">

			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>

			<!-- category search box -->
			<div class="well">
				<h1><?php  the_title(); ?></h1>
				<?php  the_content(); ?>
				<form class="form-horizontal" role="form" method="get" id="task-search" action="<?php echo site_url('/'); ?>">
					<label for="cat">Search in: </label>
					<div class="form-group input-md">
						<select name='cat' id='cat' class='form-control input-md'>
							<option value='0' selected='selected'>All tasks and guides</option>
								<?php
								$terms = get_terms('category',array("hide_empty"=>true,"parent"=>0,"orderby"=>"slug"));
								if ($terms) {
							  		foreach ((array)$terms as $taxonomy ) {
							  			if ($taxonomy->term_id == 1) continue;
								  		echo "<option class='level-0' value='".$taxonomy->term_id."'>".$taxonomy->name."</option>";
								  	}
								}
								?>
						</select>
					</div>
					<label for="sbc-s" class="sr-only">Search for</label>
					<div class="form-group input-md">
						<input type="text" value="" name="s" id="sbc-s" class="multi-cat form-control input-md" placeholder="<?php echo get_the_title(); ?>" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
					</div>
					<label for="searchbutton2" class="sr-only">Search</label>
					<div class="form-group input-md">
			    	 <?php
				    	 $icon_override = get_option('options_search_button_override', false); 
				    	 if ( isset($icon_override) && $icon_override ):
					    	 $override_text = get_option('options_search_button_text', 'Search');
							 ?>
					 		<button class="btn btn-primary" id="searchbutton2" type="submit"><?php echo esc_attr($override_text); ?></button>
						 	<?php 
				    	 else:
					    	 ?>
					 		<button class="btn btn-primary" id="searchbutton2" type="submit"><span class="dashicons dashicons-search"></span><span class="sr-only">Search</span></button>
						 	<?php 
						 endif;
						 ?>
					</div>
					<input type="hidden" name="post_type[]" value="task" />
				</form>
			</div>
		</div>
		<script type='text/javascript'>
		    jQuery(document).ready(function(){
				jQuery('#task-search').submit(function(e) {
				    if (jQuery.trim(jQuery("#sbc-s").val()) === "") {
				        e.preventDefault();
				        jQuery('#sbc-s').focus();
				    }
				});	
			});	
		
		</script>

		<div class="col-lg-7 col-md-7 col-sm-12">
			<div class="widget-box">
				<h3 class="widget-title">Browse by category</h3>
				<div class="col-lg-6 col-md-6 col-sm-6">
					<div class="row">
						<ul class="howdoi">
						<?php
						// Display category blocks
						$catcount = 0;
						if ($terms) {
				  			foreach ((array)$terms as $taxonomy ) {
					  		    $themeid = $taxonomy->term_id;
					  			$themeURL= $taxonomy->slug;
					  			$desc='';
					  			if ($taxonomy->description){
						  		    $desc = "<p class='howdesc'>".$taxonomy->description."</p>";
						  		}
						  		if ($themeid == 1) {
					  		    	continue;
					  			}
					  			$catcount++;
								if ($catcount > round(count($terms)/2,0,PHP_ROUND_HALF_DOWN) && count($terms) > 3 ) {
									echo "</ul></div></div><div class='row'><div class='col-lg-6  col-md-6 col-sm-6'><ul class='howdoi'>";
									$catcount=0;
								}
								echo "
								<li class='howdoi'><span class='brd". $taxonomy->term_id ."'>&nbsp;</span>&nbsp;<a href='".site_url()."/category/{$themeURL}/'>".$taxonomy->name."</a>".$desc."</li>";
							}
						} 
						?>
						</ul>
					</div>
				</div>
			</div>
					<?php 
					$taghtml = "";
					$taghtml = get_transient("ht_how_do_i_tags");
					if ( !$taghtml ):
						$taskcloud = get_option('options_module_tasks_showtags');
						if ( $taskcloud ):
							$taghtml = gi_howto_tag_cloud('task');
						else:
							$taghtml =  my_colorful_tag_cloud('','category','task'); 
						endif;
						set_transient("ht_how_do_i_tags", $taghtml, 60*60);
					endif;
					if ($taghtml):?>
					<div style="text-align:middle; clear:both;"  class="widget-box">
					<h3 class="widget-title">Browse by tag</h3>
					<div class="tagcloud">
						<?php echo $taghtml; ?>
					</div>
					</div>
					<?php endif;
					?>
		</div>
	</div>
<?php endwhile; ?>

<?php get_footer(); ?>