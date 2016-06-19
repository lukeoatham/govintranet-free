<?php
/**
 * The template for displaying news taxonomy pages.
 */


get_header(); 
$catname = get_queried_object()->name;					
$catid = get_queried_object()->term_id;	
$catslug = get_queried_object()->slug;	
$catdesc = get_queried_object()->description;	
$catparentid = get_queried_object()->parent; 
$catparentlink = '';
$tasktagslug = '';
$tasktag = '';

if ($catparentid):
	$catparent = get_term($catparentid, 'category');
	$catparentlink = "<a href='".get_term_link($catparentid, 'news-type')."'>".$catparent->name."</a> &raquo; ";
endif;
if ( isset( $_GET['showtag'] ) ) $tasktagslug = $_GET['showtag'];
if ($tasktagslug):
	$tasktag = get_tags(array('slug'=>$tasktagslug));
	$tasktag = $tasktag[0]->name;
endif;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$landingpage = get_option('options_module_news_page'); 
if ( !$landingpage ):
	$landingpage_link_text = 'news';
	$landingpage = site_url().'/newspage/';
else:
	$landingpage_link_text = get_the_title( $landingpage[0] );
	$landingpage = get_permalink( $landingpage[0] );
endif;

if ( have_posts() )
	the_post();
	?>
	<div class="col-lg-7 col-md-8 col-sm-12 white">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>

		<h1 <?php echo "class='h1_" . $catid . "'>". single_tag_title( '', false ) ; if ($tasktag) echo " <small><span class='glyphicon glyphicon-tag'></span> ".$tasktag."</small>";?></h1>
	
		<?php echo $catdesc; ?>
		<?php 
		$tagcloud = gi_tag_cloud('news-type',$catslug,'news');
		if ($tagcloud):
			?>					
			<h3 class='widget-title h1_<?php echo $catid; ?>'><?php _e('Browse by tag','govintranet'); ?></h3>
			<?php 
			/* Run the loop for the category page to output the posts.
			 */
			echo $tagcloud;	
		endif;
		
		if ($tasktagslug):
			$taskitems = new WP_Query(
				array (
				'post_type'=>'news',
				'news-type'=>$catslug,
				'tag'=>$tasktagslug,
				'posts_per_page' => 25,
				'paged' => $paged,												
				'orderby'=>'date',
				'order'=>'DESC',
				'post_parent'=>0
				)
			);
		else:
			$taskitems = new WP_Query(
				array (
				'post_type'=>'news',
				'news-type'=>$catslug,
				'posts_per_page' => 25,
				'paged' => $paged,												
				'orderby'=>'date',
				'order'=>'DESC',
				'post_parent'=>0
				)
			);
		endif;
		if ($taskitems->post_count==0){
			echo "<p>" . __('Nothing to show','govintranet') . ".</p>";
		}
		while ($taskitems->have_posts()) {
			$taskitems->the_post();
			$image_url = get_the_post_thumbnail($id, 'thumbnail', array('class' => 'alignright'));
			echo "<hr>";			
			echo "<div class='newsitem'><a href='".get_the_permalink()."' title='". the_title_attribute( 'echo=0' ) . "' rel='bookmark'>".$image_url."</a>";
			?>
			<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute( 'echo=1' ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
			<?php
			echo '<span class="listglyph">'.get_the_date(); 
			echo '</span> ';				
			if ( get_comments_number() ){
						echo "<a href='".get_permalink($id)."#comments'>";
						printf( _n( '<span class="badge">1 comment</span>', '<span class="badge">%d comments</span>', get_comments_number(), 'govintranet' ), get_comments_number() );
						echo "</a>";
					}
			the_excerpt(); 
			echo "</div>";
			echo '<div class="clearfix"></div>';
		 }
		 ?>
		 <?php if (  $taskitems->max_num_pages > 1 ) : ?>
		 <?php if (function_exists(wp_pagenavi)) : ?>
			<?php wp_pagenavi(array('query' => $taskitems)); ?>
			<?php else : ?>
			<?php next_posts_link(__('&larr; Older items','govintranet'), $taskitems->max_num_pages); ?>
			<?php previous_posts_link(__('Newer items &rarr;','govintranet'), $taskitems->max_num_pages); ?>						
			<?php 
			endif; 
		endif; 
		wp_reset_query();								
		?>
	</div>

	<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-12">

		<?php
		$terms = get_terms('news-type',array("hide_empty"=>true,"parent"=>$catid));
		if ($terms) {
		?>
			<div class="widget-box">
				<h3 class='widget-title'><?php _e('Sub-categories' , 'govintranet'); ?></h3>
				<ul class="howdoi">
					<?php				
			  			foreach ((array)$terms as $taxonomy ) {
							echo "
							<li class='howdoi'><span class='brd". $taxonomy->term_id ."'>&nbsp;</span>&nbsp;<a href='".get_term_link($taxonomy->slug , 'news-type')."'>".$taxonomy->name."</a>".$desc."</li>";
						}
						?>
				</ul>
			</div>
			<?php
		} 

		$taxonomies=array();
		$post_type = array();
		$taxonomies[] = 'news-type';
		$post_type[] = 'news';
		$post_cat = get_terms_by_post_type( $taxonomies, $post_type); 
		if ( count($post_cat) > 1 ){
			echo "<div class='widget-box'><h3 class='widget-title'>" . __('Other categories' , 'govintranet') . "</h3>";
			echo "<p class='taglisting " . $post->post_type . "'>";
			foreach($post_cat as $cat){ 
				if ( $cat->term_id != $catid ){
					$newname = str_replace(" ", "&nbsp;", $cat->name );
					echo "<span class='wptag t".$cat->term_id."'><a href='" . get_term_link($cat->slug, 'news-type') . "'>" . $newname . "</a></span> ";
				}
			}
			echo "</p></div>";
		}
		?>	
	</div>
<?php 
wp_reset_query();
get_footer(); 
?>
