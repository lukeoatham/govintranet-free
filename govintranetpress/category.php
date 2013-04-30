<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

$catquery = @explode("=",$query_string);

if ($catquery[1] == "news") {
	wp_redirect('/news/');
}



get_header(); 

				$caturl = pods_url_variable(1);
				$cat_type = $_GET['type'];
				?>

				<div class="row">
					<div class="ninecol white last" id="content">
					<div class="content-wrapper">
					<?php 


				$catpod = new Pod ('category' , $caturl);
				$catid = $catpod->get_field('term_id');				
				echo "<h1 class='h1_" . $catid . "'>" ;

					$tasktag=$_GET['tag'];
					printf( __( '%s', 'twentyten' ), '' . single_cat_title( '', false ) . '' );
					if ($tasktag!=""){
						echo " - " . $tasktag; 
					}
				?></h1>

				<?php
					$category_description = category_description();
					if ( ! empty( $category_description ) )
						echo '' . $category_description . '';
						?>
					</div>

<!-- category search box -->


				
				<!-- category search box -->
	<div class="category-search t<?php echo $catid; ?>">
		<div id="sbc">
		<form method="get" id="sbc-search" action="http://dcmsintranet.helpfulclients.com">
			<input type="hidden" value="<?php the_category_ID(); ?> " name = "cat" />
			<input type="hidden" value="<?php echo $cat_type; ?> " name = "type" />
			<input type="text" value="" name="s" id="s" class="multi-cat" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
			<input type="submit" id="sbc-submit" value="Search" />
		</form>
	</div>
	


</div>

<div class="content-wrapper">
<?php
				/* Run the loop for the category page to output the posts.
				 * If you want to overload this in a child theme then include a file
				 * called loop-category.php and that will be used instead.
				 */
				query_posts($query_string. '&orderby=title&order=asc&post_type=any');
				get_template_part( 'loop', 'category' );
				?>
				
				
</div>
</div>
					<div class="threecol last" id='sidebar'>
					<div id="related">
					
					<h3 class='widget-title'>Tags</h3>
						<?php 
					$slug = pods_url_variable(1);
					$thisterm = get_term_by('slug', $slug, 'category');
					$varcat = $thisterm->term_id;
					echo my_colorful_tag_cloud($varcat, 'category' , 'task'); 
					?>
					</div>
					

					<?php get_sidebar('inside'); ?>
					</div>

				</div>
				
<?php get_footer(); ?>