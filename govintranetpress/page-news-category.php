<?php
/* Template name: News category page */

$catquery = @explode("=",$query_string);

if ($catquery[1] == "news") {
	wp_redirect('/news/');
}

get_header(); 

$cat_name = $_GET['cat'];
$catpod = new Pod ('category' , $cat_name);
$catname = $catpod->get_field('name');				
$catid = $catpod->get_field('term_id');				
?>

<div class="row">
	<div class="eightcol white last" id="content">
		<div class="row">
			<div class='breadcrumbs'>
				<a title="Go to Home." href="/" class="site-home">Home</a> > 
				<a title="Go to How do I?" href="/News/">News</a> > <?php echo $catname; ?>
			</div>
		</div>
	
	<div class="content-wrapper">
<?php
		echo "<h1 class='h1_" . $catid . "'>".$catname."</h1>";
?>
		<div class="category-search t<?php echo $catid; ?>">
			<div id="sbc">
				<form method="get" id="sbc-search" action="<?php echo home_url( '/' ); ?>">
					<input type="hidden" value="<?php echo $catid; ?> " name = "cat" />
					<input type="hidden" value="news" name = "post_type" />
					<input type="text" value="" name="s" id="s2" class="multi-cat" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
					<input type="submit" id="sbc-submitx" class='small awesome blue' value="Search" />
				</form>
			</div>
		</div>
	</div>
	<div class="content-wrapper">
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
	while ($newsstories->have_posts()) {
		$newsstories->the_post();
	  	$thisconf = new Pod('task', $id);
		$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
		echo "<hr class='hr".$catid."'/>";
		echo "<div class='newsitem'>".$image_url ;
		?>
		<h3><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
<?php
		the_excerpt(); 
		echo "</div><div class='clearfix'></div>";
	 }
?>
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
<div class="fourcol last" id='sidebar'>
	<div class='widget-box'>
		<h3 class='widget-title'>Search by tag</h3>
<?php 
		$slug = $_GET['cat'];
		$thisterm = get_term_by('slug', $slug, 'category');
		$varcat = $thisterm->term_id;
		echo my_colorful_tag_cloud($varcat, 'category' , 'news'); 
?>
	</div>
</div>
				
<?php get_footer(); ?>