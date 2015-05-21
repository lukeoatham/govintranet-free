<?php
	global $post;
	global $cat_id;
	global $doctyp;
	global $title;

	// get all document types for the left hand menu
	$args = array(
	    'orderby'       => 'name', 
	    'order'         => 'ASC',
	    'hide_empty'    => false,
	    );

	$subcat = get_terms( 'document-type', $args ); 
	$cathead = '';


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
		'field' => 'id',
		'terms' => $cat_id)),
		'meta_query'=>array(
	    array(  
	    'key' => 'document_type',
	    'compare'=>"LIKE",
		'value' => '"' .$doctyp.'"' )),
		
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
		'value' => '"' .$doctyp.'"' ))
		));	
	}

	if ($cat_id!='any' && $doctyp=='any' ){ // single cat
		$docs = get_posts(array(
			'post_type'=>'attachment',
			'orderby'=>'title',
			'order'=>'ASC',
			'posts_per_page' => -1,
			'post_status'=>'inherit',
			'tax_query'=>array(
			array(  
			'taxonomy' => 'category',
			'field' => 'id',
			'terms' => $cat_id,
			'compare' => 'IN',
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
	$docs = new wp_query(array('orderby'=>'title','order'=>'ASC','post_status'=>'inherit','posts_per_page'=>-1,'paged'=>$paged,'post_type'=>'attachment','post__in'=>$postsarray));

	if ( $docs->have_posts() ):
		echo "<div class='widget-box'>";
		if ( $title ) echo "<h3>".esc_attr($title)."</h3>";
		echo '<ul id="docresults" class="docmenu">';
		while ( $docs->have_posts() ) : $docs->the_post(); 
			echo '<li><a href="'.wp_get_attachment_url().'" class="">';
			echo ''.$post->post_title;
			echo '</a></li>';
		endwhile;
		echo '</ul>';						
		echo "</div>";
	endif;

	wp_reset_postdata();	
?>			