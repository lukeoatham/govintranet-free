<?php
/* Template name: Timeline page */

get_header(); 

$styleurl = plugin_dir_url("/") . 'ht-timelines/css/ht_timelines.css';
wp_enqueue_style( 'ht_timeline', $styleurl );

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
			<section>
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
					<li<?php if ( $tcount == 1 ) echo ' class="timeline-inverted"'; ?>>
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

<?php get_footer(); ?>