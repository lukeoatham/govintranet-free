<?php
/* Template name: How do I? alt page */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<div class="col-lg-7 col-md-7 white">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>
				
			<!-- category search box -->
			<h1><?php the_title(); ?></h1>
			<?php  the_content(); ?>
			<div class="well well-sm">
				<form class="form-horizontal" role="form" method="get" id="task-alt-search" action="<?php echo site_url('/'); ?>">
					<label for="sbc-s" class="sr-only"><?php _e('Search' , 'govintranet'); ?></label>
					<div class="input-group">
						<input type="text" value="" name="s" id="sbc-s" class="multi-cat form-control input-md" placeholder="<?php echo get_the_title(); ?>" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
						 <span class="input-group-btn">
						 <input type="hidden" name="post_type[]" value="task" />
						 <label for="searchbutton2" class="sr-only"><?php _e('Search' , 'govintranet'); ?></label>
				    	 <?php
					    	 $icon_override = get_option('options_search_button_override', false); 
					    	 if ( isset($icon_override) && $icon_override ):
						    	 $override_text = esc_attr(get_option('options_search_button_text', __('Search','govintranet') ));
								 ?>
						 		<button class="btn btn-primary" id="searchbutton2" type="submit"><?php echo $override_text; ?></button>
							 	<?php 
					    	 else:
						    	 ?>
						 		<button class="btn btn-primary" id="searchbutton2" type="submit"><span class="dashicons dashicons-search"></span><span class="sr-only"><?php _e('Search','govintranet'); ?></span></button>
							 	<?php 
							 endif;
							 ?>
						 </span>
					</div><!-- /input-group -->
				</form>
			</div>
			<script type='text/javascript'>
			    jQuery(document).ready(function(){
					jQuery('#task-alt-search').submit(function(e) {
					    if (jQuery.trim(jQuery("#sbc-s").val()) === "") {
					        e.preventDefault();
					        jQuery('#sbc-s').focus();
					    }
					});	
				});	
			
			</script>

			<div class="widget-box browsecats">
				<h3 class="widget-title"><?php _e('Browse by category','govintranet'); ?></h3>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6">
							<ul class="howdoi">
								<?php 
								// Display category blocks
								$catcount = 0;
								$terms = get_terms('category',array("hide_empty"=>true,"parent"=>0,"orderby"=>"slug"));
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
											echo "</ul></div><div class='col-lg-6  col-md-6 col-sm-6'><ul class='howdoi'>";
											$catcount=0;
										}
										echo "
										<li class='howdoi'><span class='brd". $taxonomy->term_id ."'>&nbsp;</span>&nbsp;<a href='".site_url()."/category/{$themeURL}'>".$taxonomy->name."</a>".$desc."</li>";
									}
								} 
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-5 col-md-5">
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
					if ($taghtml): ?>
					<div style="text-align:middle;"  class="widget-box browsetags">
					<h3 class="widget-title"><?php _e('Browse by tag','govintranet'); ?></h3>
					<div class="tagcloud">
					 <?php	echo $taghtml; ?>
					</div>
					</div>
					<?php
					endif;
					?>
			</div>

		</div>

<?php endwhile; ?>

<?php get_footer(); ?>