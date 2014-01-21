<?php
/* Template name: News category page */

$catquery = @explode("=",$query_string);

if ($catquery[1] == "news") {
	wp_redirect('/newspage/');
}

get_header(); 

$cat_name = $_GET['cat'];
$catpod = new Pod ('category' , $cat_name);
$catname = $catpod->get_field('name');				
$catslug = $catpod->get_field('slug');				//echo $catslug;
$catid = $catpod->get_field('id');				//echo $catid;
?>



	<div class="col-lg-7 col-md-8 col-sm-12 white">
		<div class="row">
			<div class='breadcrumbs'>
				<a title="Go to Home." href="<?php echo site_url(); ?>/" class="site-home">Home</a> &raquo; 
				<a title="Go to News" href="<?php echo site_url(); ?>/newspage/">News</a> &raquo; <?php echo $catname; ?>
			</div>
		</div>
	
		<div class="row">
<?php
		echo "<h1 class='h1_" . $catid . "'>".$catname." news</h1>";
?>
					<form class="form-horizontal" role="form" method="get" name="task-category" id="sbc-search" action="<?php echo home_url( '/' ); ?>">
						<div class="col-lg-12">
							<div id="staff-search" class="well well-sm">
								<div class="input-group input-md">
									<input type="text" value="" class="form-control" name="s" id="sbc-s" placeholder="Search news..."  />
									 <span class="input-group-btn">
									<button type="submit" class="btn btn-primary input-md"><i class="glyphicon glyphicon-search"></i></button>
									 </span>
									<input type="hidden" value="<?php echo $catslug; ?> " name = "cat" />
									<input type="hidden" value="news" name = "post_type" />
								</div>
							</div>
						</div>
					</form>

<script>
jQuery(function(){
jQuery("#sbc-s").focus();
});
</script>
		</div>


<?php
/* Run the loop for the category page to output the posts.
 * If you want to overload this in a child theme then include a file
 * called loop-category.php and that will be used instead.
 */
	$cquery = array(
		'tax_query' => array(
	        array(
	            'taxonomy' => 'category',
	            'field' => 'slug',
	            'terms' => $catslug,
		       ),
		    ),	

		    'post_type' => 'news',
		    'orderby'=>'post_date',
		    'order'=>'DESC',
		    'posts_per_page' => 10,
		    'paged' => $paged												
		)
		;
	$newsstories = new WP_Query( $cquery );		
	if ($newsstories->post_count==0){
		echo "<p>Nothing to show.</p>";
	}
				$context = "news";
		$contexturl = "/news/";
			$icon = "star-empty";			

	while ($newsstories->have_posts()) {
		$newsstories->the_post();
//	  	$thisconf = new Pod('task', $id);
		$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
		$thistitle = get_the_title();


		echo "<hr>";
?>	<h3>				
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); echo $title_context; ?></a></h3>
<?php
	echo "<div class='media'>" ;

	echo "<a href='";
	$userurl = get_permalink();
	echo $userurl;
	echo "'><div class='hidden-xs'>".$image_url."</div></a>" ;

	echo "<div class='media-body'>";
	
		echo "<div><p>";
			   $thisdate= $post->post_date;
			   $thisdate=date("j M Y",strtotime($thisdate));
			   echo "<span class='listglyph'><i class='glyphicon glyphicon-calendar'></i> Updated ".$thisdate."</span> ";
		echo "</p></div>";

		the_excerpt(); 
?>
	</div></div>
<?php } // End the loop. Whew. ?>
<?php 
	if (  $newsstories->max_num_pages > 1 ) : ?>
<?php if (function_exists(wp_pagenavi)) : ?>
		<?php wp_pagenavi(array('query' => $newsstories)); ?>
		<?php else : ?>
		<?php next_posts_link('&larr; Older items', $newsstories->max_num_pages); ?>
		<?php previous_posts_link('Newer items &rarr;', $newsstories->max_num_pages); ?>						
<?php endif; 
	endif; 
	wp_reset_query();								
?>

</div>

<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-12">
	<div class='widget-box'>
		<h3 class='widget-title'>Search by tag</h3>
<?php 
		$slug = $_GET['cat'];
		$thisterm = get_term_by('slug', $slug, 'category');
		$varcat = $thisterm->term_id;
		echo "<div class='tagcloud'>";
		echo my_colorful_tag_cloud($varcat, 'category' , 'news'); 
		echo "</div>";
?>
	</div>
<?php
				$taxonomies=array();
				$post_type = array();
				$taxonomies[] = 'category';
				$post_type[] = 'news';
				$post_cat = get_terms_by_post_type( $taxonomies, $post_type);
				if ($post_cat){
					echo "<div class='widget-box'><h3 class='widget-title'>Other categories</h3>";
					echo "<p class='taglisting {$post->post_type}'>";
					foreach($post_cat as $cat){
						if ($cat->name!='Uncategorized' && $cat->name && $cat->term_id != $catid){
						$newname = str_replace(" ", "&nbsp;", $cat->name );
						echo "<span class='wptag t".$cat->term_id."'><a href='".site_url()."/news-by-category/?cat=".$cat->slug."'>".$newname."</a></span><br>";
					}
					}
					echo "</p></div>";
				}
				?>	

</div>
				
<?php get_footer(); ?>