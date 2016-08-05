<?php
/* Template name: Timeline filter page */

get_header(); 

$styleurl = plugin_dir_url("/") . 'ht-timelines/css/ht_timelines_filter.css';
wp_enqueue_style( 'ht_timeline_filter', $styleurl );

?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<div class="col-sm-12 white">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) bcn_display(); ?>
				</div>
			</div>

			<div class="page-header">
				<h1 id="timeline"><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</div>
			
			
			
				<div class="legend">
					<ul  class="timeline2" id="timeline_filter">
					<?php
				$legend = array();
				while ( have_rows('ht_timeline_items') ) : the_row();
					$icon = get_sub_field('ht_timeline_icon');
					$colour = get_sub_field('ht_timeline_colour');
					if ( !$icon ) $icon = "flag";
					if ( !$colour ) $colour = "default";
				
					if ( !isset( $legend[$colour] ) ) $legend[$colour] = $icon;
				endwhile;
				foreach ( $legend as $colour=>$icon ){
					echo '<li><a href="#timeline_filter" onclick="showhide_timeline(\''.$colour.'\')"><div class="timeline2-badge ' . $colour . '"><i class="glyphicon glyphicon-' . $icon . '"></i></div></a></li>';
				}
				?>
						<li class='filter'><h3><a href="#timeline_filter" onclick="show_timeline()">Show all</a></h3></li>
						<li class='filter'><h3>Filter</h3> </li>
					</ul>
				</div>
			<div class="clearfix"></div>
			
			<section id="timeline_section">
				
			<ul class="timeline">


				<?php
				$tcount = 0;
				$dateformat = get_option('date_format');
				
				while ( have_rows('ht_timeline_items') ) : the_row();
					
					$title = get_sub_field('ht_timeline_title');
					$icon = get_sub_field('ht_timeline_icon');
					$colour = get_sub_field('ht_timeline_colour');
					if ( !$icon ) $icon = "flag";
					if ( !$colour ) $colour = "default";
					$content = get_sub_field('ht_timeline_content');
					
					
					
					?>
					<li<?php if ( $tcount == 1 ):
								echo ' class="timeline '.$colour.' timeline-inverted"'; 
								else:
								echo ' class="timeline '.$colour.'"'; 
								endif;
									?>>
				      <div class="timeline-badge <?php echo $colour; ?>"><i class="glyphicon glyphicon-<?php echo $icon; ?>"></i></div>
				      <div class="timeline-panel">
				        <div class="timeline-heading">
				          <h4 class="timeline-title"><?php echo $title; ?></h4>
				        </div>
				        <div class="timeline-body">
				          <?php echo apply_filters('the_content', $content); ?>
				        </div>
				      </div>
				    </li>					
					<?php
					$tcount++;
					if ( $tcount == 2 ) $tcount = 0;
					
				endwhile;
				?>
			
		  	</ul>
			</section>
		</div>
<?php endwhile; ?>
<script>
	function showhide_timeline(colour){
		var links = jQuery('li.timeline');

		for(var i = 0; i < links.length; i++) {
				jQuery(links[i]).addClass('hidden');
		}

		var links = jQuery('li.timeline.' + colour);

		for(var i = 0; i < links.length; i++) {
				jQuery(links[i]).removeClass('hidden');
		}

	}
	function show_timeline(){
		var links = jQuery('li.timeline');

		for(var i = 0; i < links.length; i++) {
				jQuery(links[i]).removeClass('hidden');
		}
	}
</script>
<?php get_footer(); ?>