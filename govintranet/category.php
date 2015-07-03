<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package WordPress
 */

get_header(); 
$catname = get_queried_object()->name;					
$catid = get_queried_object()->term_id;	
$catslug = get_queried_object()->slug;	
$catdesc = get_queried_object()->description;	
$catparentid = get_queried_object()->parent; 
$tasktagslug = '';
$tasktag = '';
if ($catparentid):
	$catparent = get_term($catparentid, 'category');
	$catparentlink = "<a href='".get_term_link($catparentid, 'category')."'>".$catparent->name."</a> &raquo; ";
endif;
if ( isset( $_GET['showtag'] ) ) $tasktagslug = $_GET['showtag'];
if ($tasktagslug):
	$tasktag = get_tags(array('slug'=>$tasktagslug));
	$tasktag = $tasktag[0]->name;
endif;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
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

		<h1 <?php echo "class='h1_" . $catid . "'>". single_tag_title( '', false ) ; ?></h1>
	
			<?php echo wpautop($catdesc); ?>
				<form class="form-horizontal" role="form" method="get" name="task-category" id="category-search" action="<?php echo site_url( '/' ); ?>">
					<div class="input-group input-md">
						<input type="text" value="" class="form-control" name="s" id="sbc-s" placeholder="How do I..." />
						 <span class="input-group-btn">
						<button class="btn t<?php echo $catid; ?> input-md" type="submit" ><span class="dashicons dashicons-search"></span></button>
						 </span>
						<input type="hidden" value="<?php echo $catid; ?>" name = "cat" />
						<input type="hidden" value="task" name = "post_type[]" />
					</div>
				</form>

				<script type='text/javascript'>
				    jQuery(document).ready(function(){
						jQuery('#category-search').submit(function(e) {
						    if (jQuery.trim(jQuery("#sbc-s").val()) === "") {
						        e.preventDefault();
						        jQuery('#sbc-s').focus();
						    }
						});	
					});	
				
				</script>
							
							
		<?php 
		$tagcloud = gi_tag_cloud('category',$catslug,'task'); 
		if ($tagcloud):
			?>
			<div class='cattagbutton' ><a class='btn t<?php echo $catid; ?>' data-toggle="collapse" href="#cattagcloud" aria-expanded="false" aria-controls="cattagcloud">Browse by tag <span class='caret'></span></a></div>
			<div class="collapse" id="cattagcloud">
			<?php echo $tagcloud; ?>
			</div>							
			<?php if ($tasktagslug) echo "<h3 class='h1_'".$catid."><span class='dashicons dashicons-tag'></span> ".$tasktag."</h3>";?>
			<?php
		endif;
							
		/* Run the loop for the category page to output the posts.
		 */
		if ($tasktagslug):
			$taskitems = new WP_Query(
					array (
			'post_type'=>'task',
			'cat'=>$catid,
			'tag'=>$tasktagslug,
			'posts_per_page' => 25,
			'paged' => $paged,												
			'orderby'=>'name',
			'order'=>'ASC',
			'post_parent'=>0,
			)
			);
		else: 
			$taskitems = new WP_Query(
					array (
			'post_type'=>'task',
			'cat'=>$catid,
			'posts_per_page' => 25,
			'paged' => $paged,												
			'orderby'=>'name',
			'order'=>'ASC',
			'post_parent'=>0,
			)
			);
		endif;
		if ($taskitems->post_count==0){
			echo "<p>Nothing to show.</p>";
		}
		while ($taskitems->have_posts()) {
			$taskitems->the_post();
			$ID = $post->ID;
			$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
			echo "<div class='newsitem'>".$image_url ;
			echo "<hr>";			
			if ( get_posts(array("post_type"=>"task",'post_parent'=>$post->ID,"post_status"=>"publish"))) { 
				$context = "guide";
				$icon = "book";
			} else {
				$context = "task";
				$icon = "hammer";
			}			
				?>
			<h3><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); ?></a>&nbsp;<small><span class="dashicons dashicons-<?php echo $icon; ?>"></span>&nbsp;<?php echo ucfirst($context); ?></small></h3>

			<?php
			the_excerpt(); 
			echo "</div>";
		 }
		 ?>
		 <?php 	if (  $taskitems->max_num_pages > 1 ) : ?>
		 		<?php if (function_exists(wp_pagenavi)) : ?>
					<?php wp_pagenavi(array('query' => $taskitems)); ?>
				<?php else : ?>
			<?php next_posts_link('&larr; Older items', $taskitems->max_num_pages); ?>
			<?php previous_posts_link('Newer items &rarr;', $taskitems->max_num_pages); ?>						
			<?php 
			endif; 
		endif; 
		wp_reset_query();								
		?>
	</div>

	<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-12">

		<?php
		$terms = get_terms('category',array("hide_empty"=>true,"parent"=>$catid));
		if ($terms) {
			?>
			<div class="widget-box">
				<h3 class='widget-title'>Sub-categories</h3>
				<div class='catlisting task'><ul class="nav nav-pills nav-stacked">
					<?php				
			  			foreach ((array)$terms as $taxonomy ) {
				  		    $themeid = $taxonomy->term_id;
				  			$themeURL= $taxonomy->slug;
				  			$desc='';
					  		if ($themeid == 0) {
				  		    	continue;
				  			}
							echo "
							<li class=''><a href='".site_url()."/category/{$themeURL}/'><span class='brd". $taxonomy->term_id ."'>&nbsp;</span>&nbsp;".$taxonomy->name."</a>".$desc."</li>";
						}
						?>
				</ul>
				</div>
			</div>
			<?php
		} 

		$taxonomies=array();
		$post_type = array();
		$taxonomies[] = 'category';
		$post_type[] = 'task';
		$post_cat = get_terms_by_post_type( $taxonomies, $post_type);
		if ($post_cat){
			echo "<div class='widget-box'><h3 class='widget-title'>Categories</h3>";
			echo "<div class='catlisting {$post->post_type}'><ul class='nav nav-pills nav-stacked'>";
			foreach($post_cat as $cat){
				if ( $cat->name!='Uncategorized' && $cat->name ){
					$newname = str_replace(" ", " ", $cat->name );
					echo "<li><a ";
					if ($cat->term_id == $catid) echo " class='active'";
					echo "href='".site_url()."/category/".$cat->slug."'><span class='brd".$cat->term_id."'></span>&nbsp;";
					if ($cat->term_id == $catid) echo "<strong>";
					echo $newname;
					if ($cat->term_id == $catid) echo "</strong>";
					echo "</a></li>";
				}
			}
			echo "</ul></div></div>";
		}
		?>	
	</div>
		
<?php get_footer(); ?>