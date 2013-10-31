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
$catid = $catpod->get_field('term_id');				
?>



	<div class="col-lg-8 col-md-8 white">
		<div class="row">
			<div class='breadcrumbs'>
				<a title="Go to Home." href="/" class="site-home">Home</a> > 
				<a title="Go to How do I?" href="/news/">News</a> > <?php echo $catname; ?>
			</div>
		</div>
	
	<div>
<?php
		echo "<h1 class='h1_" . $catid . "'>".$catname." news</h1>";
?>
	<div class="well">
									<form class="form-horizontal" role="form" method="get" id="sbc-search" action="/">
										<label for="sbc-s">In <?php echo strtolower($catname);?> news </label>
										<div class="form-group input-md">
											<input type="text" value="" name="s" id="sbc-s" class="multi-cat form-control input-md" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
										</div>
										<div class="form-group input-md">
											<button type="submit" class="btn btn-primary input-md">Search</button>
										</div>
										<input type="hidden" value="<?php echo $catid; ?> " name = "cat" />
										<input type="hidden" value="news" name = "post_type" />
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
	            'field' => 'id',
	            'terms' => $catid,
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
	  	$thisconf = new Pod('task', $id);
		$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
		$thistitle = get_the_title();


		echo "<hr class='hr".$catid."'/>";
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
</div>
<div class="col-lg-4 col-md-4" id='sidebar'>
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
</div>
				
<?php get_footer(); ?>