<?php
/**
 * The Template for displaying all single task posts.
 *
 * @package WordPress
 */

if ( get_post_format($post->ID) == 'link' ){
	$external_link = get_post_meta($post->ID,'external_link',true);
	if ($external_link){
		wp_redirect($external_link); 
		exit;
	}	
}

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 

	$taskicon = get_option("options_module_tasks_icon_tasks", "glyphicon glyphicon-file");
	$guideicon = get_option("options_module_tasks_icon_guides", "glyphicon glyphicon-duplicate");
		
	$alreadydone = array();	
	$chapter_header = false;
	$singletask = false;
	$pagetype = "";
	$parent_guide = '';
	$children_chapters = '';
	$current_task = $id;
	$current_chapter = '';
	$alreadydone[]=$id;
	$parent_guide_id = 0;
	if ($post->post_parent != 0){
		$parent_guide = get_post($post->post_parent); 
		if ($parent_guide){
			$parent_guide_id = $parent_guide->ID; 
			if (!$parent_guide_id){
				$parent_guide_id = $post->ID;
			}
		}
	} else {
		$parent_guide_id = $post->ID;
	} 
	$children_chapters = get_posts ("post_type=task&posts_per_page=-1&post_status=publish&post_parent=".$parent_guide_id."&orderby=menu_order&order=ASC");
	
	if (!$parent_guide && !$children_chapters){
		$singletask=true; 
		$pagetype = __("task","govintranet");
		$pagetypeorig = "task";
		$icon = $taskicon;
	} else {
		$pagetype = __("guide","govintranet");
		$pagetypeorig = "guide";
		$icon = $guideicon;
	};

	if ($children_chapters && !$parent_guide){
		$chapter_header=true;
	}

	if ($parent_guide){
		$parent_slug=$parent_guide->post_name;
		$parent_name=govintranetpress_custom_title($parent_guide->post_title); 
		$guidetitle =$parent_name;	
	}

	if (!$parent_guide){
		$guidetitle = govintranetpress_custom_title($post->post_title);
	}	
	?>
	<div class="col-lg-7 col-md-8 col-sm-8 white">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>						
		<?php 

		if ($pagetypeorig=="guide"):

		?>
		<div>
			<h1><?php echo $guidetitle; ?> <small class="task-context"><span class="<?php echo $icon; ?>"></span> <?php echo ucwords($pagetype); ?></small></h1>
			<?php 
			$podchap = get_post($parent_guide_id); 
			$alreadydone[]=$parent_guide_id;
			$children_chapters = get_posts("post_type=task&posts_per_page=-1&post_status=publish&post_parent=".$parent_guide_id."&orderby=menu_order&order=ASC");
			$totalchapters = count($children_chapters) + 1;
			$halfchapters = round($totalchapters/2,0,PHP_ROUND_HALF_UP); 
			?>
			<div class="chapters-container">
				<div class="col-lg-6 col-md-6">
					<div class="chapters">
						<nav role="navigation" class="page-navigation">
							<ol>
								<?php
									if ($chapter_header){
										echo "<li class='active'>";
										echo "<span class='part-label part-title'>".$guidetitle."</span>";
									} else {
										$chapname = $parent_name;
										$chapslug = $parent_slug;
										echo "<li><a href='".get_permalink($parent_guide_id)."'><span class='part-title'>{$chapname}</span></a>";
									}
									echo "</li>";
									$carray = array();
									$k=1; 
									foreach ($children_chapters as $chapt){
										if ($chapt->post_status=='publish'){
										$k++;
									if (($k == $halfchapters + 1) && ($totalchapters > 3) ):?>
							</ol>
						</nav>
					</div>
				</div>
				<div class="col-lg-6 col-md-6">
					<div class="chapters">
						<nav role="navigation" class="page-navigation">
							<ol start='<?php echo $k;?>'>
									
								<?php
								endif;
				
								echo "<li ";
								if ($id == $chapt->ID){
									 echo "class='active'";
									 $current_chapter=$k;
								}
								echo ">";
								$chapname = govintranetpress_custom_title($chapt->post_title);
								$chapslug = $chapt->post_name; 
								$carray[$k]['chapter_number']=$k;
								$carray[$k]['slug']=$chapslug;
								$carray[$k]['name']=$chapname;
								$carray[$k]['id']=$chapt->ID;
								$alreadydone[]=$chapt->ID;
								if ($chapt->ID==$current_task){
									echo "<span class='part-label part-title'>{$chapname}</span>";
								} else {
									echo "<a href='".get_permalink($chapt->ID)."'><span class='part-label part-title'>{$chapname}</span></a>";
								}
								echo "</li>";
								}
							}
							?>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	
	<?php
				
	endif;
	
	if ($pagetypeorig=="guide"){
		echo "<div>";

		echo "<div class='content-wrapper-notop'>";
			if ($current_chapter>1){
				echo "<h2>".$current_chapter.". ".get_the_title()."</h2>";
			} else {
				echo "<h2>" . __('Overview' , 'govintranet') . "</h2>";
			}

			the_content(); 		

			if ( get_post_meta($post->ID, 'treat_as_a_manual', true) ):

			show_manual();

			endif;

			get_template_part("part", "downloads");	

			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}
			echo "</div>";
			
	        echo '<div class="row">';
	
	        if ($chapter_header){ // if on chapter 1
				
				echo '<div class="col-lg-12 chapterr"><a href="'.get_permalink($carray[2]["id"]).'">'.$carray[2]["name"].'&nbsp;<span class="dashicons dashicons-arrow-right-alt2"></span></a>';
				echo "</div>";
	        } elseif ($current_chapter==2) { // if on chapter 2
				echo '<div class="col-lg-6 col-md-6 chapterl"><a href="'.get_permalink($parent_guide_id).'" title="' . esc_attr__("Navigate to previous part" , "govintranet") . '"><span class="dashicons dashicons-arrow-left-alt2"></span>&nbsp;' . __("Overview" ,"govintranet") .'</a></div>';
	            if (isset($carray[3]['slug'])){
					echo '<div class="col-lg-6 col-md-6 chapterr"><a href="'.get_permalink($carray[3]["id"]).'">'.$carray[3]["name"].'&nbsp;<span class="dashicons dashicons-arrow-right-alt2"></span></a></div>';
		        }
	        }   else { // we're deep in the middle somewhere
	        	$previous_chapter = $current_chapter-1; 
				$next_chapter = $current_chapter+1;

				echo '<div class="col-lg-6 col-md-6 chapterl"><a href="'.get_permalink($carray[$previous_chapter]["id"]).'" title="' . esc_attr__("Navigate to previous part" , "govintranet") . '"><span class="dashicons dashicons-arrow-left-alt2"></span>&nbsp;'.govintranetpress_custom_title($carray[$previous_chapter]["name"]).'</a></div>';
	            if (isset($carray[$next_chapter]['slug'])){
					echo '<div class="col-lg-6 col-md-6 chapterr"><a href="'.get_permalink($carray[$next_chapter]["id"]).'">'.govintranetpress_custom_title($carray[$next_chapter]["name"]).'&nbsp;<span class="dashicons dashicons-arrow-right-alt2"></span></a></div>';
				}
			}
			echo "</div>";
			echo "</div>";

		} else { ?>
			<h1><?php echo $guidetitle; ?> <small class="task-context"><span class="<?php echo $icon; ?>"></span> <?php echo ucwords($pagetype); ?></small></h1>
			<?php
			the_content(); 
			
			if ( get_post_meta($post->ID, 'treat_as_a_manual', true) ):

				show_manual();

			endif;

			get_template_part("part", "downloads");	

			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}
		}
		?>

		</div>

		<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-4">	

		<?php 
		get_template_part("part", "sidebar");
		
		dynamic_sidebar('task-widget-area'); 
		
		get_template_part("part", "related");

		$post_categories = wp_get_post_categories( $post->ID ); 
		$cats = array();
		$catsfound = false;	
		$catshtml='';
		if ($post_categories){
			foreach($post_categories as $c){
				$cat = get_category( $c );
				if ( $c < 2 ) continue;
				$catsfound = true;
				$catshtml.= "<span><a class='wptag t".$cat->term_id."' href='".get_term_link($cat->slug, 'category')."'>".str_replace(" ","&nbsp;",$cat->name)."</a></span> ";
			}
		}
			
		if ($catsfound){
			echo "<div class='widget-box'><h3>" . __('Categories' , 'govintranet') . "</h3><p class='taglisting page'>".$catshtml."</p></div>";
		}
		
		$posttags = get_the_tags();
		if ($posttags) {
			$foundtags=false;	
			$tagstr="";
		  	foreach($posttags as $tag) {
	  			$foundtags=true;
	  			$tagurl = $tag->term_id;
		    	$tagstr=$tagstr."<span><a class='label label-default' href='".get_tag_link($tagurl)."?type=task'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
		  	}
		  	if ($foundtags){
			  	echo "<div class='widget-box'><h3>" . __('Tags' , 'govintranet') . "</h3><p> "; 
			  	echo $tagstr;
			  	echo "</p></div>";
		  	}
		}


		 	?>			
		</div> 
			
<?php endwhile; // end of the loop. ?>
<?php
	function show_manual(){
		if( have_rows('manual_chapters') ):
			$i = 0; 
			$output = '<div class="panel-group" id="manualaccordion" role="tablist" aria-multiselectable="true">';

			while ( have_rows('manual_chapters') ) : the_row(); 
				$i++;
				$title = get_sub_field('manual_chapter_title'); 
				$content = get_sub_field('manual_chapter_content'); 
				$output.='
			  <div class="panel panel-default">
			    <div class="panel-heading" role="tab" id="chapter'.$i.'">
			      <h4 class="panel-title">
			        <a role="button" data-toggle="collapse" data-parent="#manualaccordion" href="'.get_permalink($post->ID).'#chaptercollapse'.$i.'" aria-expanded="true" aria-controls="chaptercollapse'.$i.'">'.$title.'</a>
			      </h4>
			    </div>
			    <div id="chaptercollapse'.$i.'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="chapter'.$i.'">
			      <div class="panel-body">
			        '.$content.'
			      </div>
			    </div>
			  </div>
				';
			endwhile;
			echo $output;
			echo "</div>";
		endif;
	}
	?>
<?php if ( get_post_meta($post->ID, 'treat_as_a_manual', true) ): ?>
<script>	
jQuery(document).ready(function() {
	    // on load of the page: switch to the currently selected tab
    var hash = window.location.hash;
    jQuery( hash ).addClass('in');
    hash = hash.replace('collapse', '');
    // Append URL with tab's ID #
	jQuery('#manualaccordion .panel .panel-title a').click(function (e) {
		var scrollmem = jQuery(hash).scrollTop();
		window.location.hash = this.hash;
		jQuery(hash).scrollTop(scrollmem);
	});
});		
</script>
<?php endif; ?>
<?php get_footer(); ?>