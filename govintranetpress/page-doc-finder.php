<?php
/* Template name: Document finder*/

get_header(); ?>


					<div class="col-lg-12 white ">
						<div class="row">
							<div class='breadcrumbs'>
								<?php if(function_exists('bcn_display') && !is_front_page()) {
									bcn_display();
									}?>
							</div>
						</div>

			<h1><?php the_title(); ?> </h1>
<?php
						$doctyp = $_GET['doctyp'] ? $_GET['doctyp'] : 'any' ;
						$cat_id = $_GET['cat'] ? $_GET['cat'] : 'any' ;
						$catterm = get_category_by_slug($cat_id);
						$catname = $catterm->name;
						$catid = $catterm->term_id;
						
						// get all document types for the left hand menu
						$args = array(
						    'orderby'       => 'name', 
						    'order'         => 'ASC',
						    'hide_empty'    => false 
						    );
														
	?>		
			<?php

			$subcat = get_terms( 'document_type', $args );
			$cathead = '';
			
			?>
					</div>

		<div class="col-lg-3 col-md-3 col-sm-6">
<div class="widget-box">
    <h3 class="widget-title">Categories</h3>
<?php
				$taxonomies[]='category';
				$post_type[]='attachment';
				 	$post_cat = get_terms_by_media_type( $taxonomies, $post_type);
				if ($post_cat){
					echo "<p class='taglisting {$post->post_type}'><ul class='menu'>";
					if ($doctyp) {
						if ($cat_id =='any' ) {
							echo "<li class='howdoi'><span class='brd'>&nbsp;";
							echo "All categories</span></li>";
						} else {
							echo "<li class='howdoi'><span class='brd'>&nbsp;<a href='/document-finder/?doctyp=".$doctyp."'>";
							echo "All categories</a></span></li>";
							
						}
					} else {
						echo "<li class='howdoi'><span class='brd'>&nbsp;<a href='/document-finder/'>";
						echo "All categories</a></span></li>";
					}
					foreach($post_cat as $cat){
						if ($cat->name!='Uncategorized' && $cat->name){
						$newname = str_replace(" ", "&nbsp;", $cat->name );
						if ($doctyp) {
							if ($cat->slug == $cat_id) {
								echo "<li class='howdoi'><span class='brd".$cat->term_id."'>&nbsp;";
								echo $newname."</span></li>";
							} else {
								echo "<li class='howdoi'><span class='brd".$cat->term_id."'>&nbsp;<a href='/document-finder/?cat=".$cat->slug."&doctyp=".$doctyp."'>";
								echo $newname."</a></span></li>";
								
							}
						} else {
						echo "<li class='howdoi'><span class='brd".$cat->term_id."'>&nbsp;<a href='/document-finder/?cat=".$cat->slug."'>";
						echo $newname."</a></span></li>";
						}
					}
					}
					echo "</ul></p>";//echo $cat_id;
				}
								
				 ?> 
</div>
		</div>
				<div class="col-lg-3 col-md-3 col-sm-6">


		<div class="widget-box">
				<h3 class="widget-title">Document types</h3>
				<div class="chapters">
						<ul class='doctypemenu'>
						<?php
						if ($doctyp == 'any') {
						    echo "<li class='active'>All types</li>";
						    } else {
							if ($cat_id){    
								echo "<li><a href='".home_url( '/' )."document-finder/?doctyp=any&cat={$cat_id}'>";								
								echo "All types</a></li>";
							} else {
						       echo "<li><a href='".home_url( '/' )."document-finder/?doctyp=any'>All documents</a></li>";
							}
						}
						foreach ($subcat as $sc) { 
							if ($doctyp == $sc->slug) {
							    echo "<li class='active'>{$sc->name}</li>";
						    } else {
						    if ($cat_id){   
						       echo "<li><a href='".home_url( '/' )."document-finder/?doctyp={$sc->slug}&cat={$cat_id}'>";
						       
						       echo "{$sc->name}</a></li>";
						       } else {
							       echo "<li><a href='".home_url( '/' )."document-finder/?doctyp={$sc->slug}'>{$sc->name}</a></li>";
						
							}
						}
							
					}
						?>
		
						</ul>
			</div>
		</div>
		


				</div>		

		
<?php
		echo "<div class='col-lg-6 col-md-6 col-sm-6'>";
		echo "<div class='widget-box'>";
			$serphead ='';
//			$serphead="<small>";

			
			if (($cat_id=='any' || !$cat_id) && ($doctyp=='any' || !$doctyp)){ //echo "opt1";
				$serphead = "All documents";
			}

			if (($cat_id!='any' && $cat_id) && ($doctyp=='any' || !$doctyp)){//echo "opt2";
					foreach($post_cat as $cat){
						if ($cat->name!='Uncategorized' && $cat->name && $cat->slug == $cat_id){
							$newname = str_replace(" ", "&nbsp;", $cat->name );
							$serphead= "<span class='wptag t".$cat->term_id."'>".$newname."</span> documents";						
						}
					}

			}

			if (($cat_id=='any' || !$cat_id) && ($doctyp!='any' && $doctyp)){//echo "opt3";
				foreach ($subcat as $sc) {
					if ($doctyp == $sc->slug) { $serphead .= "All ".$sc->name ;} 
					}
			}

			if (($cat_id!='any' && $cat_id) && ($doctyp!='any' && $doctyp)){//echo "opt4";

					foreach($post_cat as $cat){
						if ($cat->name!='Uncategorized' && $cat->name && $cat->slug == $cat_id){
							$newname = str_replace(" ", "&nbsp;", $cat->name );
							$serphead= "<span class='wptag t".$cat->term_id."'>".$newname."</span>";						
						}
					}
				foreach ($subcat as $sc) {
					if ($doctyp == $sc->slug) { $serphead .= " ".$sc->name ;} 
				}
						
				
			}
//			$serphead.="</small>";			

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;


if ($cat_id!='any' && $doctyp!='any'){			
			$docs = get_posts(Array(
			'post_type'=>'attachment',
			'orderby'=>'title',
			'order'=>'ASC',
            'posts_per_page' => -1,
            'tax_query'=>array(
            'relation' => 'AND',
            array(  
            'taxonomy' => 'document_type',
			'field' => 'slug',
			'terms' => $doctyp),
            array(  
            'taxonomy' => 'category',
			'field' => 'slug',
			'terms' => $cat_id)))
			);
} 
if ($cat_id=='any' && $doctyp!='any'){			
			$docs = get_posts(Array(
			'post_type'=>'attachment',
			'orderby'=>'title',
			'order'=>'ASC',
            'posts_per_page' => -1,
            'tax_query'=>array(array(  
            'taxonomy' => 'document_type',
			'field' => 'slug',
			'terms' => $doctyp,)),
			
				)
			);	
	
}
if ($cat_id=='any' && $doctyp=='any' ){
 $inlist=array();
     foreach ( $subcat as $term ) {
       $inlist[] = $term->slug . ", "; 
      }
			$docs = get_posts(Array(
			'post_type'=>'attachment',
			'orderby'=>'title',
			'order'=>'ASC',
            'posts_per_page' => -1,
            'tax_query'=>array(array(  
            'taxonomy' => 'document_type',
			'field' => 'slug',
			'terms' => $inlist,
			)),
			
				)
			);	

}
if ($cat_id!='any' && $doctyp=='any' ){
 $inlist=array();
     foreach ( $subcat as $term ) {
       $inlist[] = $term->slug . ", "; 
      }
			$docs = get_posts(Array(
			'post_type'=>'attachment',
			'orderby'=>'title',
			'order'=>'ASC',
            'posts_per_page' => -1,
            'tax_query'=>array(
              'relation' => 'AND',
            array(  
            'taxonomy' => 'document_type',
			'field' => 'slug',
			'terms' => $inlist,
			),
			array(  
            'taxonomy' => 'category',
			'field' => 'slug',
			'terms' => $cat_id)))
			);	

}

$postsarray=array();
foreach($docs as $doc){
$postsarray[].=$doc->ID. ",";
};//print_r( $postsarray);

echo "<h3 class='widget-title'>".$serphead."</h3>";


			if (count($docs) == 0 ) {
				echo "Nothing to show.";
				$postsarray[]='';
			}
							$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
							$counter = 0;	

$docs = new wp_query(array('orderby'=>'title','order'=>'ASC','post_status'=>'inherit','posts_per_page'=>25,'paged'=>$paged,'post_type'=>'attachment','post__in'=>$postsarray));


			if (count($docs) == 0 ) {
				echo "Nothing to show.";
			}
	echo '<ul class="docmenu">';

if ( $docs->have_posts() ) while ( $docs->have_posts() ) : $docs->the_post(); ?>

<?php
  	echo '<li><a href="'.$post->guid.'" class="">';
    	echo ''.$post->post_title;
    	echo '</a><br class="">'.$post->post_content.'</li>';
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
			
		</div> <!--end of first column-->
		

				



<?php get_footer(); ?>