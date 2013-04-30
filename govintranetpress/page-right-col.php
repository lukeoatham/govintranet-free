<?php
/**
/* Template name: Right column page */

					


get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>



	<div class="row">
		<div class="ninecol white" id='content'>
			<div class="content-wrapper">
				<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
			</div>
		</div> <!--end of first column-->
		
		<div class="threecol last" >	
		</div>				
	</div> <!--end of second column-->


<?php endwhile; ?>

<?php get_footer(); ?>