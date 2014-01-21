<?php
/* Template name: How do I? alt page */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>

<div>

		<div class="col-lg-8 col-md-8 white">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>
				<!-- category search box -->
				<h1><?php echo the_title(); ?></h1>
				<?php //echo the_content(); ?>
			<div class="well well-sm">
					<form class="form-horizontal" role="form" method="get" id="sbc-search" action="/">
					<div class="input-group">
						<input type="text" value="" name="s" id="sbc-s" class="multi-cat form-control input-md" placeholder="How do I..." onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
						 <span class="input-group-btn">
						 <input type="hidden" name="post_type" value="task" />
						 <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
						 </span>
					</div><!-- /input-group -->

				</form>
		
			</div>


		<script>
		jQuery(function(){
		jQuery("#sbc-s").focus();
		});
		</script>


				<div class="widget-box">
					<h3 class="widget-title">Browse by category</h3>
						<div class="row">

					<div class="col-lg-6 col-md-6 col-sm-6">

							<ul class="howdoi">
							<?php
							// Display category blocks
							$catcount = 0;
							$terms = get_terms('category');
							if ($terms) {
					  			foreach ((array)$terms as $taxonomy ) {
						  		    $themeid = $taxonomy->term_id;
						  			$themeURL= $taxonomy->slug;
						  			$desc='';
						  			if ($taxonomy->description){
							  		    $desc = "<p class='howdesc'>".$taxonomy->description."</p>";
							  		}
							  		if ($themeURL == 'uncategorized') {
						  		    	continue;
						  			}
						  			$catcount++;
									if ($catcount > round(count($terms)/2,0,PHP_ROUND_HALF_DOWN) && count($terms) > 3 ) {
										echo "</ul></div><div class='col-lg-6  col-md-6 col-sm-6'><ul class='howdoi'>";
										$catcount=0;
									}
									echo "
									<li class='howdoi'><span class='brd". $taxonomy->term_id ."'>&nbsp;</span>&nbsp;<a href='".site_url()."/task-by-category/?cat={$themeURL}'>".$taxonomy->name."</a>".$desc."</li>";
								}
							} 
					?>
						</ul>
						</div>
						
					</div>
				</div>
		</div>
	<div class="col-lg-4 col-md-4">
			<div>
				<div style="text-align:middle;"  class="widget-box">
					<h3 class="widget-title">Browse by tag</h3>
					<div class="tagcloud">
						<?php echo my_colorful_tag_cloud('','category','task'); ?>
					</div>
				</div>
			</div>

		</div>

	</div>

</div>
<?php endwhile; ?>

<?php get_footer(); ?>