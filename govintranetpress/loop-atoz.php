<?php
/**
 * The loop that displays posts in the A-Z taxonomy.
 * Matches the A to Z letter in the post title and changes key words to bold in the listing.
 * If no key words found in the post title, revert to sorting through the post keywords field for matching text within square brackets.
 */
?>

<?php /* If there are no posts to display, such as an empty archive page */ ?>

<?php 
		$pageslug = pods_url_variable(-1);

		if ( ! have_posts() ) { 
		
				echo "<h1>";
				_e( 'Not found', 'twentyten' );
				echo "</h1>";
				echo "<p>";
				_e( 'There\'s nothing to show.', 'govintranetpress' );
				echo "</p>";
				get_search_form(); 
		};


	while ( have_posts() ) : the_post(); 

	//highlight words that begin with this letter in the standard post title
	$foundkey = false; //set a flag to see if we get a match
	$oldtitle = get_the_title();

	//we allow glyphicons in post titles, so to stop any code being highlighted we extract it here and add it back when we're finished
	$iconcode='';
	$findstartpos = strpos ($oldtitle,"<i",0);  
	if ($findstartpos > -1){ 
		$findendpos = strpos ($oldtitle,"</i>",$findstartpos+1); 	
		$iconcode = substr($oldtitle, $findstartpos-1, strlen($oldtitle));
		$oldtitle = substr($oldtitle,0, $findstartpos-1); 
	}

	$otwords = explode(" ",$oldtitle); 
	$newwords = array();
	foreach ($otwords as $ot){
		if (strtolower(substr($ot, 0, 1)) == strtolower($pageslug)  && (strlen($ot) > 2 || in_array(strtolower($ot), array("hr" , "it",  "is", "pq", "pqs", "wms" ) )) ) 
		{
			$newwords[] ="<strong>".$ot."</strong>"; 
			$foundkey = true;
		} else {
			$newwords[] = $ot;
		}
	}
	$newtitle = implode(" ", $newwords);//." ".$iconcode;
	if (!$foundkey) $newtitle='';
	$post_type = ucwords($post->post_type);
	$post_cat = get_the_category();
	$title_context='';		
	$context='';
	$icon='';
	$userurl = get_permalink();

	if (!$foundkey){ //if we didn't get a match via the standard post title we'll look in the keywords field for a shortcode [Extra A to Z entry]
		$syns=0; //position marker for finding the next [ in keywords
		$synpos=true;
		$synonyms = get_post_meta($post->ID, 'keywords', true); //load the keywords for this post
		$temptitle = '';
		while ($synpos){ //check iteratively for shortcodes
			//get any synonym words
			$findtxt = "["; 
			$findstartpos = strpos ($synonyms,$findtxt,$syns);  
			if ($findstartpos > -1){ 
				$syns = $findstartpos+1;
				$findendpos = strpos ($synonyms,"]",$syns); 				
				$synstr = substr($synonyms, $findstartpos+1, $findendpos-$findstartpos-1);
				$otwords = explode(" ",$synstr); //process the shortcode by highlight words
				$newwords = array();
				$foundletter = false; //flag to check if we found a match in the shortcode
				foreach ($otwords as $ot){ 
		if (strtolower(substr($ot, 0, 1)) == strtolower($pageslug)  && (strlen($ot) > 3 || in_array(strtolower($ot), array("hr" , "it",  "is", "pq", "pqs", "wms" ) )) ) 
 //don't include tiny words but allow common acronyms
					{
						$foundletter=true;
						$newwords[] ="<strong>".$ot."</strong>"; 
					} else {
						$newwords[] = $ot;
					}
				}
				if ($foundletter){
					$newsyn = implode(" ", $newwords);
					$temptitle .= $newsyn ."</li><li>" ; //regular single entries get added with <li> later, so we'll add our own for each new element, and then take off the excess when we're done.
				}
			} else {
				$synpos=false;
			}
		}

		$newtitle = $temptitle;//." ".$iconcode;
	}
	
	if (substr($newtitle,strlen($newtitle)-9,strlen($newtitle)) == '</li><li>') $newtitle = substr($newtitle,0,strlen($newtitle)-9); //if we have an open trailing li we remove it here.
	
	//final check to see if we actually found anything
	
	if ($foundkey || $foundletter){
		if ($iconcode)	$newtitle.="&nbsp;".$iconcode."&nbsp;"; // add glyphicon code back in
		$contexturl=$post->guid;
		$context='';
		if ($post_type=='Task'){
			$taskpod = new Pod ('task' , $post->ID); 
			if ( $taskpod->get_field('page_type') == 'Task'){		
				$context = "task";
				$icon = "question-sign";
			} else {
				$context = "guide";
				$icon = "book";
				$taskparent=$taskpod->get_field('parent_guide');
				if ($taskparent){
					$parent_guide_id = $taskparent[0]['ID']; 		
					$taskparent = get_post($parent_guide_id);
					$title_context=" <small>(".govintranetpress_custom_title($taskparent->post_title).")</small>"; 
				}
			}			
		}
		if ($post_type=='Projects'){
			$context = "project";
			$icon = "road";		
			$taskpod = new Pod ('projects' , $post->ID); 
			$projparent=$taskpod->get_field('parent_project');
			if ($projparent){
				$parent_guide_id = $projparent[0]['ID']; 		
				$projparent = get_post($parent_guide_id);
				$title_context=" <small>(".govintranetpress_custom_title($projparent->post_title).")</small>";
			}			
		}
		if ($post_type=='News'){
				$context = "news";
				$icon = "star-empty";				
		}
		if ($post_type=='Vacancies'){
				$context = "job vacancy";
				$icon = "random";						
		}
		if ($post_type=='Blog'){
				$context = "blog";
				$icon = "comment";				
		}
		if ($post_type=='Event'){
				$context = "event";
				$icon = "calendar";						
		}
		if ($post_type=='User'){
				$context = "staff";
				$icon = "user";			
		}
	
		if ($post_type=='Attachment'): 
			$context='document download';
			$icon = "download";			
		endif;	
		
		if ($context) $context=ucfirst($context);
	
		if ($post_type=='Attachment'): 
		
		?>
			<li>				
			<a href="<?php echo wp_get_attachment_url( $post->id ); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), $context ); ?>" rel="bookmark"><?php echo $newtitle  ?></a>
		
		
		<?php 
		elseif ($post_type=='User'): 
		
		?>
			<li>				
			<a href="<?php echo $userurl; ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), $context ); ?>" rel="bookmark"><?php  echo $newtitle;  ?></a>
		
		<?php 
		else:
		
		?>
			<li>				
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ),  $context ); ?>" rel="bookmark"><?php  echo $newtitle;  ?></a>
		
		<?php
	
		endif;
		
		//echo '<span class="listglyph"><i class="glyphicon glyphicon-'.$icon.'"></i>&nbsp;'.$context.'</span>';	
			
		echo "</li>";
	
	}
	?>


<?php endwhile; // End the loop. Whew. ?>
