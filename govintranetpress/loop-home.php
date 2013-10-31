<?php 

// change the query to retrieve posts (incl pages, via Page Tagger plugin) tagged with 'featured'

$query = new WP_Query( 'tag=featured' );

$firstflag = true; 

?>


<?php while ( $query->have_posts() ) : $query->the_post(); ?>

	<?php if ($firstflag) : ?>
	
		<div class='firstpost clearfix'>
	
			<?php the_post_thumbnail('firstlistingthumb',"class=firstlistingthumb"); ?>
	
			<h2><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

			<?php the_excerpt(); ?>
			
		</div>
		
		<?php $firstflag = false; ?>

	<?php else : ?>
	
		<div class='post clearfix'>
	
			<?php the_post_thumbnail('listingthumb',"class=listingthumb"); ?>
	
			<h2><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			<p class='postmeta'><?php echo get_the_date();?></p>
			<?php the_excerpt(); ?>
			
		</div>

	<?php endif; ?>
	

<?php endwhile; // End the loop. Whew. ?>

					
