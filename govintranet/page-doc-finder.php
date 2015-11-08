<?php
/* Template name: Document finder */

get_header();
	 if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<div class="col-lg-12 white ">
	<div class="row">
		<div class='breadcrumbs'>
			<?php if(function_exists('bcn_display') && !is_front_page()) {
				bcn_display();
				}?>
		</div>
	</div>

	<h1><?php the_title(); ?> </h1>
	<?php the_content(); ?>

	
	<?php
	//get filter options
	$doctyp = 'any';
	$cat_id = 'any';
	if ( isset( $_GET['doctyp'] ) ) $doctyp = $_GET['doctyp'] ? $_GET['doctyp'] : 'any' ;
	if ( isset( $_GET['cat'] ) ) $cat_id = $_GET['cat'] ? $_GET['cat'] : 'any' ;

	if ($cat_id != "any") {
		$catterm = get_category_by_slug($cat_id);
		$catname = $catterm->name;
		$catid = $catterm->term_id;
	} else {
		$catname = __("All categories","govintranet") . " <span class='caret'></span>";
	}	
	
	if ($doctyp != "any") {
		$dtterm = get_term_by('slug', $doctyp, 'document-type'); 
		$dtname = $dtterm->name;
		$dtid = $dtterm->term_id;
	} else {
		$dtname = __("All document types","govintranet") . " <span class='caret'></span>";
	}	
	
	// get all document types for the left hand menu
	$args = array(
	    'orderby'       => 'name', 
	    'order'         => 'ASC',
	    'hide_empty'    => false,
	    );

	$subcat = get_terms( 'document-type', $args );
	$cathead = '';
			
	?>
</div>

<div class="col-lg-5 col-md-6 col-sm-12">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-6">
	<div id="document_category_dropdown" class="widget-box">
		<h3 class="widget-title"><?php _e('Category','govintranet'); ?></h3>
		<?php
		$taxonomies[]='category';
		$post_type[]='attachment';
		$post_cat = get_terms_by_media_type( $taxonomies, $post_type);
		if ($post_cat){ 
			
			?>
			<div class="btn-group">
			<img id="doccatspinner" class="hidden" src="<?php echo get_stylesheet_directory_uri() . '/images/small-squares.gif'; ?>" />
			<button id = "doccatbutton" type="button" class="btn btn-primary dropdown-toggle2" data-toggle="dropdown">
			<?php echo $catname; ?></button>
			<ul class='dropdown-menu docspinner' role="menu">
			<?php
			if ($doctyp) {
				if ($cat_id !='any' ) {
					echo "<li><a class='doccatspinner' href='".get_permalink(get_the_id())."?doctyp=".$doctyp."'>" . __('All categories','govintranet') . "</a></li>";
				}
			} else {
				echo "<li><a class='doccatspinner' href='".get_permalink(get_the_id())."'>" . __('All categories','govintranet') . "</a></li>";
			}
			foreach($post_cat as $cat){ 
				if ($cat->term_id > 1 && $cat->name){
					$newname = str_replace(" ", "&nbsp;", $cat->name );
					if ($doctyp) { // show chosen doc type as selected
						if ($cat->slug == $cat_id) {
							echo "<li class='disabled'><a class='doccatspinner' href='".get_permalink(get_the_id())."?cat=".$cat->slug."&doctyp=".$doctyp."'>";
							echo $newname."</a></li>";
						} else {
							echo "<li><a class='doccatspinner' href='".get_permalink(get_the_id())."?cat=".$cat->slug."&doctyp=".$doctyp."'>";
							echo $newname."</a></li>";
						}
					} else {
					echo "<li><a class='doccatspinner' href='".get_permalink(get_the_id())."?cat=".$cat->slug."'>";
					echo $newname."</a></li>";
					}
				}
			}
			echo "</ul></div>";
		}
	 ?> 
		</div>
</div>
<div class="col-lg-12 col-md-12 col-sm-6">
	<div id="document_type_dropdown" class="widget-box">
		<h3 class="widget-title"><?php _e('Document type' , 'govintranet' ); ?></h3>
		<div class="btn-group">
			<img id="doctypespinner" class="hidden" src="<?php echo get_stylesheet_directory_uri() . '/images/small-squares.gif'; ?>" />
			<button  id="doctypebutton" type="button" class="btn btn-primary dropdown-toggle2" data-toggle="dropdown">
				<?php echo $dtname; ?></button>
			<ul class='dropdown-menu docspinner' role="menu">
			<?php
			if ($doctyp != 'any') {
				if ($cat_id){    
					echo "<li><a class='doctypespinner' href='".get_permalink(get_the_id())."?doctyp=any&cat={$cat_id}'>" . __('All document types' , 'govintranet') . "</a></li>";
				} else {
			       echo "<li><a  class='doctypespinner' href='".get_permalink(get_the_id())."?doctyp=any'>" . __('All document types' , 'govintranet') . "</a></li>";
				}
			}
			foreach ($subcat as $sc) { 
				if ($doctyp == $sc->slug) {
				       echo "<li class='disabled'><a  class='doctypespinner' href='".get_permalink(get_the_id())."?doctyp={$sc->slug}&cat={$cat_id}'>";
				       echo "{$sc->name}</a></li>";
			    } else {
				    if ($cat_id){   
				       echo "<li><a  class='doctypespinner' href='".get_permalink(get_the_id())."?doctyp={$sc->slug}&cat={$cat_id}'>";
				       echo "{$sc->name}</a></li>";
				    } else {
				       echo "<li><a  class='doctypespinner' href='".get_permalink(get_the_id())."?doctyp={$sc->slug}'>{$sc->name}</a></li>";
					}
				}
			}
			?>
			</ul>
		</div>
	</div>
	</div>
</div>		
</div>

<div class='col-lg-7 col-md-6 col-sm-12'>
	<div class='widget-box'>
		<?php
		
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		if ($cat_id!='any' && $doctyp!='any'){	// cat and doc type
		
		//tax queries need to be meta queries for document_type
			
			$docs = get_posts(array(
			'post_type'=>'attachment',
			'orderby'=>'title',
			'order'=>'ASC',
		    'posts_per_page' => -1,
			'post_status'=>'inherit',
		    'tax_query'=>array(
		    array(  
		    'taxonomy' => 'category',
			'field' => 'slug',
			'terms' => $cat_id)),
			'meta_query'=>array(
		    array(  
		    'key' => 'document_type',
		    'compare'=>"LIKE",
			'value' => '"' .$dtid.'"' )),
			
			));
		} 

		if ($cat_id=='any' && $doctyp!='any'){	// single doc type
			$docs = get_posts(Array(
			'post_type'=>'attachment',
			'orderby'=>'title',
			'order'=>'ASC',
		    'posts_per_page' => -1,
			'post_status'=>'inherit',
			'post_mime_type' => 'application',
			'meta_query'=>array(
		    array(  
		    'key' => 'document_type',
		    'compare'=>"LIKE",
			'value' => '"' .$dtid.'"' ))
			));	
		}

		if ($cat_id!='any' && $doctyp=='any' ){ // single cat
			$inlist=array();
			foreach ( $subcat as $term ) {
				$inlist[] = $term->term_id; 
			}
			$docs = get_posts(array(
				'post_type'=>'attachment',
				'orderby'=>'title',
				'order'=>'ASC',
				'posts_per_page' => -1,
				'post_status'=>'inherit',
				'tax_query'=>array(
				array(  
				'taxonomy' => 'category',
				'field' => 'slug',
				'terms' => $cat_id,
				)),
				'meta_query'=>array(
			    array(  
			    'key' => 'document_type',
				'value' => '',
				'compare' => '!=',
				)),
				));
		}

		if ($cat_id=='any' && $doctyp=='any' ){ // no filter 
			$inlist=array(); 
		    foreach ( $subcat as $term ) {
		       $inlist[] = $term->term_id; 
		     }
			$docs = get_posts(array(
				'post_type'=>'attachment',
				'orderby'=>'title',
				'order'=>'ASC',
		        'posts_per_page' => -1,
				'post_status'=>'inherit',
				'meta_query'=>array(
			    array(  
			    'key' => 'document_type',
				'value' => '',
				'compare' => '!=',
				)),
			));	
		}

		$postsarray=array();
		foreach($docs as $doc){ 
			if (!is_object($doc)):
				$postsarray[].=$doc['ID'];
			else:
				$postsarray[].=$doc->ID;
			endif;
		};

		if (count($docs) == 0 ) {
			$postsarray[]='';
		}
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$counter = 0;	
		$docs = new wp_query(array('orderby'=>'title','order'=>'ASC','post_status'=>'inherit','posts_per_page'=>25,'paged'=>$paged,'post_type'=>'attachment','post__in'=>$postsarray));
		if ($docs->found_posts == 0 ) {
			?>
			<h3 class="widget-title"><?php _e('No results' , 'govintranet'); ?></h3>
			<?php
			_e('Nothing to show' , 'govintranet'); echo ".";
		} else {
			?>
			<h3 class="widget-title"><?php _e('Results', 'govintranet'); ?> <small>(
			<?php 
				echo $docs->found_posts;
				echo _n( 'item' , 'items' , $docs->found_posts );
			?>)</small></h3>
			<?php
		}
		?>
		<div id="docspinner" class="col-sm-12 hidden">
			<img src="<?php echo includes_url('/images/spinner-2x.gif'); ?>" alt="<?php _e('Please wait' , 'govintranet') ;?>" />
		</div>
		<?php
		echo '<ul id="docresults" class="docmenu">';
	
		if ( $docs->have_posts() ) while ( $docs->have_posts() ) : $docs->the_post(); 
			echo '<li><a href="'.wp_get_attachment_url().'">';
			echo ''.$post->post_title;
			echo '</a></li>';
		endwhile;
	
		echo '</ul>';
	
		wp_reset_query();

		?>
		<?php if ( $docs->max_num_pages > 1 ) : ?>
			<?php if (function_exists(wp_pagenavi)) : ?>
				<?php wp_pagenavi(array('query' => $docs)); ?>
			<?php else : ?>
				<?php next_posts_link('&larr; Older items', $docs->max_num_pages); ?>
				<?php previous_posts_link('Newer items &rarr;', $docs->max_num_pages); ?>						
			<?php endif; ?>
		<?php endif; ?>

	</div>
</div> 
<script>
jQuery(document).ready(function($) {
	jQuery('.docspinner').click(function(){
		jQuery('#docresults').slideUp();
		jQuery('.wp-pagenavi').addClass('hidden');
		return;
	});
	jQuery('a.doctypespinner').click(function(){
		jQuery('#doctypespinner').removeClass('hidden');
		jQuery('#doctypebutton').addClass('hidden');
		jQuery('#doccatbutton').addClass('hidden');
		return;
	});
	jQuery('a.doccatspinner').click(function(){
		jQuery('#doccatspinner').removeClass('hidden');
		jQuery('#doccatbutton').addClass('hidden');
		jQuery('#doctypebutton').addClass('hidden');
		return;
	});
});
</script>
<?php endwhile; 
get_footer(); ?>