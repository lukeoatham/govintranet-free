<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */

get_header();
$catid = get_queried_object()->term_id;	

	if ( have_posts() )
		the_post();
?>
		<div class="col-lg-8 col-md-8 white">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) bcn_display();?>
				</div>
			</div>
			<h1>
		<?php if ( is_day() ) : ?>
						<?php printf( __( 'Daily archives: %s', 'govintranet' ), get_the_date() ); ?>
		<?php elseif ( is_month() ) : ?>
						<?php printf( __( 'Monthly archives: %s', 'govintranet' ), get_the_date('F Y') ); ?>
		<?php elseif ( is_year() ) : ?>
						<?php printf( __( 'Yearly archives: %s', 'govintranet' ), get_the_date('Y') ); ?>
		<?php else : ?>
				<?php
				$archiveTitle = post_type_archive_title('',false);
				if ( $archiveTitle != "" ):
					echo "<h1>".$archiveTitle."</h1>";
				else:
					the_archive_title( '<h1">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				endif;
				?>
		<?php endif; ?>
			</h1>

		<?php
			/* Since we called the_post() above, we need to
			 * rewind the loop back to the beginning that way
			 * we can run the loop properly, in full.
			 */
			rewind_posts();
		
			/* Run the loop for the archives page to output the posts.
			 * If you want to overload this in a child theme then include a file
			 * called loop-archives.php and that will be used instead.
			 */
			 get_template_part( 'loop', 'archive' );
			 wp_reset_postdata();
		?>
		</div>

	<div class="col-lg-4 col-md-4" id="sidebar">
		<div id="related">
			<?php if (is_tax(array('news-update-type')) || is_post_type_archive('news-update')):
					$taxonomies=array();
					$post_type = array();
					$taxonomies[] = 'news-update-type';
					$post_type[] = 'news-update';
					$post_cat = get_terms_by_post_type( $taxonomies, $post_type);
					if ( $post_cat && count($post_cat) > 1 ){
						echo "<div class='widget-box'><h3 class='widget-title'>Update categories</h3>";
						echo "<p class='taglisting news'>";
						foreach($post_cat as $cat){
							if ( $cat->name && ( $cat->term_id != $catid ) ){
								$newname = str_replace(" ", "&nbsp;", $cat->name );
								echo "<span class='wptag t".$cat->term_id."'><a href='".get_term_link($cat->slug, 'news-update-type')."'>".$newname."</a></span> ";
							}
						}
						echo "</p></div>";
					}
				 dynamic_sidebar('news-widget-area');  
				 endif;
				 ?>
		</div>
	</div>

<?php 
get_footer(); 
?>