<?php
/**
 * The template for displaying A-Z pages.
 *
 * Uses atoz taxonomy to display lists of pages, teams and tasks
 * 
 *
 */

get_header(); ?>


<?php
	if ( have_posts() )
		the_post();
		
		$thistax = $wp_query->get_queried_object();
		$slug = $thistax->slug; 
		$stopwords = array('the','for','and','out','much','many','how','what','why','when','about','arrange','find','check','get','you','i\'m','set'); // words greater than 2 letters to ignore
		$gowords = array("hr","it","is","pq","pqs","wms" ) // words less than 3 letters to include
		?>

		<div class="col-lg-12 col-md-12 white">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>
			<h1>A to Z</h1>
			<ul class="pagination">

			<?php 

			//fill the default a to z array
			$letters = range('a','z');
			$letterlink=array();
			$hasentries = array();
			
			foreach($letters as $l) { 
				$letterlink[$l] = "<li class='disabled'><a href='#'>".strtoupper($l)."</a></li>";
			}				

			$terms = get_terms('a-to-z'); 
			if ($terms) {
				foreach ((array)$terms as $taxonomy ) {
	
				$letterlink[$taxonomy->slug] = "<li";
				if (strtolower($slug)==strtolower($taxonomy->slug)) $letterlink[$taxonomy->slug] .=  " class='active'";
					$letterlink[$taxonomy->slug] .=  "><a href='".get_term_link($taxonomy->slug,'a-to-z')."'>".strtoupper($taxonomy->name)."</a></li>";
				}
			}

			echo @implode("",$letterlink); 
			?>
			</ul>
			<h2><?php echo single_cat_title(); ?></h2>
			<?php

						 
			$args = array(
				'posts_per_page' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'a-to-z',
						'field' => 'slug',
						'terms' => $slug,
					)
				)
			);
			$postslist = new WP_Query( $args ); 
			$sortedlist = array();
			
			
			if ( ! $postslist->have_posts() ) { 
			
					echo "<h1>";
					_e( 'Not found', 'govintranet' );
					echo "</h1>";
					echo "<p>";
					_e( 'There\'s nothing to show.', 'govintranet' );
					echo "</p>";
					get_search_form(); 
			};
			
			
			while ( $postslist->have_posts() ) : $postslist->the_post(); 

				//highlight words that begin with this letter in the standard post title
				$foundkey = false; //set a flag to see if we get a match
				$oldtitle = govintranetpress_custom_title($post->post_title); 
				$otwords = explode(" ",$oldtitle); 
				$newwords = array();
				$newtitle = '';
				$tempot = '';

				foreach ($otwords as $ot){
					if (strtolower(substr($ot, 0, 1)) == strtolower($slug)  && (strlen($ot) > 2 || in_array(strtolower($ot), $gowords )) && !in_array(strtolower($ot),$stopwords)) 		{
						$newwords[] ="<strong>".$ot."</strong>"; 
						$foundkey = true;
						if (!isset($sortedlist[$post->ID]['ID'])):
							$sortedlist[$post->ID]['listword'] = strtolower($ot);
							if ( $ot == strtoupper($ot) ):
								$sortedlist[$post->ID]['keyword'] = strtoupper($ot);
							else:
								$sortedlist[$post->ID]['keyword'] = strtolower($ot);
							endif;
							$sortedlist[$post->ID]['listword'] = strtolower($ot);
							$sortedlist[$post->ID]['ID'] = $post->ID;
						endif;
						if (!$tempot) $tempot = $ot;
					} else {
						$newwords[] = $ot;
					}
				}
				
				if ( count($newwords) ) $newtitle = implode(" ", $newwords);
				if (!$foundkey) $newtitle=''; 
				$post_type = ucwords($post->post_type);
				$userurl = get_permalink();

				if (!$foundkey){ //if we didn't get a match via the standard post title we'll look in the keywords field for a shortcode [Extra A to Z entry]
					$syns=0; //position marker for finding the next [ in keywords
					$synpos=true;
					$synonyms = get_post_meta($post->ID, 'keywords', true); //load the keywords for this post
					while ($synpos && $synonyms){ //check iteratively for shortcodes
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
								if (strtolower(substr($ot, 0, 1)) == strtolower($slug)  && (strlen($ot) > 2 || in_array(strtolower($ot), $gowords )) && !in_array(strtolower($ot),$stopwords)) 
								//don't include tiny words but allow common acronyms
								{
									$foundletter=true;
									$newwords[] ="<strong>".$ot."</strong>"; 
									if (!isset($sortedlist[$post->ID]['ID'])): 
										$sortedlist[$post->ID]['listword'] = strtolower($ot);
										if ( $ot == strtoupper($ot) ):
											$sortedlist[$post->ID]['keyword'] = strtoupper($ot);
										else:
											$sortedlist[$post->ID]['keyword'] = strtolower($ot);
										endif;
										$sortedlist[$post->ID]['ID'] = $post->ID;									
									endif;
									if (!$tempot) $tempot = $ot;
								} else {
									$newwords[] = $ot;
								}
							}
							if ($foundletter){ 
								$newsyn = implode(" ", $newwords);
								$newtitle.= $newsyn ;
							}
						} else {
							$synpos=false;
						}
					}
			
					$newtitle = ucfirst($newtitle);//." ".$iconcode;
				}
				if (substr($newtitle,strlen($newtitle)-9,strlen($newtitle)) == '</li><li>') $newtitle = substr($newtitle,0,strlen($newtitle)-9); //if we have an open trailing li we remove it here.

				if ( isset($tempot) && $tempot && isset($newtitle) && $newtitle && ((isset($foundletter) && $foundletter ) || (isset($foundkey) && $foundkey)) ) :
					$sortedlist[$post->ID]['newtitle'] = $newtitle; 
				else:
					$sortedlist[$post->ID]['newtitle'] = $tempot; 
				endif;

			endwhile;

			asort($sortedlist);

			echo "<dl class='dl-atoz row'>";

			//final check to see if we actually found anything
			$lastword = '';
			$stripe = 'even';
			foreach ( $sortedlist as $key => $val ){ 
				global $post; 
				$post = get_post($key);
				setup_postdata($post); 
				$post_type = ucfirst($post->post_type);
				if ($post_type=='Attachment'): 
					if ( ucfirst($val['keyword']) != $lastword):
						echo "<dt class='col-sm-2 ".$stripe."'>".str_replace(",", "",  ucfirst($val['keyword']))."</dt>"; 
						$lastword = ucfirst($val['keyword']);
					else:
						echo "<dt>&nbsp;</dt>"; 
					endif;
					?>
					<dd><a href="<?php echo wp_get_attachment_url( $key ); ?>" rel="bookmark"><?php echo $val['newtitle'];  ?></a></dd>
					<?php  
				elseif ($post_type=='User'): 
					if ( ucfirst($val['keyword']) != $lastword):
						echo "<dt class='col-sm-2 ".$stripe."'>".str_replace(",", "",  ucfirst($val['keyword']))."</dt>"; 
						$lastword = ucfirst($val['keyword']);
					else:
						echo "<dt>&nbsp;</dt>"; 
					endif;
					?>
					<dd><a href="<?php echo $userurl; ?>" rel="bookmark"><?php echo $val['newtitle'];  ?></a></dd>
					<?php 
				else: 
					if ( isset( $val['keyword'] ) && ucfirst($val['keyword']) != $lastword):
						if ( 'even' == $stripe ) { $stripe = 'odd'; } else { $stripe = 'even'; }
						echo "<dt class='col-sm-2 ".$stripe."'>".str_replace(",", "",  ucfirst($val['keyword']))."</dt>"; 
						$lastword = ucfirst($val['keyword']);
					else:
						echo "<dt class='col-sm-2 ".$stripe."'></dt>"; 
					endif;

						?>
					<dd class='col-sm-10 <?php echo $stripe; ?>'><a href="<?php echo get_the_permalink($key); ?>" rel="bookmark"><?php echo $val['newtitle']; ?></a></dd>
					<?php
				endif;
			}

			 echo "</dl>";
			?>
		</div>
<?php wp_reset_postdata(); ?>
<?php get_footer(); ?>