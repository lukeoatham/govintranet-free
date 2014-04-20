
<?php
/**
 * The template for displaying Archive pages.
 *
 * @package WordPress
 */
 
get_header(); ?>

<?php
	/* Queue the first post, that way we know
	 * what date we're dealing with (if that is the case).
	 *
	 * We reset this later so we can run the loop
	 * properly with a call to rewind_posts().
	 */
	 
	 $curauth = (isset($_GET['author_name'])) ? get_user_by('slug',$author_name): get_userdata(intval($author));
	 
	 $sfilter = $_GET['show'];
	 
	 if ($sfilter == 'forum'){
		 query_posts( array('post_type'=>array('reply','forum','topic'),'author'=>$author ) );
	 } else {
	 	 query_posts( array('post_type'=>'blog','author'=>$author ) );
	 }

	if ( have_posts() )
		the_post();
?>		
			<div class="col-lg-8 col-md-8 col-sm-8 white">
				<div class="row">
					<div class='breadcrumbs'>
						<?php echo "<a href='".site_url()."/'>Home</a> &raquo; ";
						 	if ($sfilter == 'forum'){
							 	 echo "<a href='".site_url()."/forums/'>Forums</a> &raquo; ";
						 	} else {
							 	 echo "<a href='".site_url()."/blog/'>Blog</a> &raquo; ";
							}
							echo $curauth->display_name ;
							?>
					</div>
				</div>
				<h1><?php if ($sfilter=='forum'){ echo "Forum"; }else{echo "Blog"; }?> posts by <?php echo $curauth->display_name   ?></h1>
				<p><a href='/staff/<?php echo $curauth->user_login ;?>'>Staff profile</a> | <a href="mailto:<?php echo $curauth->user_email ; ?>"><?php echo $curauth->display_name ; ?></a></p>
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
				?>
			</div>
		<div class="col-lg-4 col-md-4 col-sm-4">

		</div>	
<?php
wp_reset_query();
get_footer(); ?>
