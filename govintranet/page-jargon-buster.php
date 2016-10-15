<?php
/* Template name: Jargon buster */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<div class="col-lg-12 white ">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>

		<?php
		$thistitle = get_the_title();
		echo "<h1>".$thistitle."</h1>";
		the_content();
		// default to show 'A' for speed, user can still set to show= for full list
		$requestshow = "A";
		if ( isset( $_REQUEST['show'] ) ) $requestshow = $_REQUEST['show'];

		$letters = range('A','Z');
		
		foreach($letters as $l) {
			$letterlink[$l] = "<li class='disabled {$l}'><a>".$l."</a></li>";
		}				
		?>				
		
		<ul id='atozlist' class="pagination">
		<?php
		$gterms = new WP_Query('post_type=jargon-buster&posts_per_page=-1&orderby=name&order=ASC');
		$counter = 0;
		$html = '';
				
		if ( $gterms->have_posts() ) while ( $gterms->have_posts() ) : $gterms->the_post(); ?>
														
			<?php 
			
			$title = get_the_title();
			$thisletter = strtoupper(substr($title,0,1));	
			
			if ( isset( $hasentries[$thisletter] )):							
				$hasentries[$thisletter] = $hasentries[$thisletter] + 1;
			else:
				$hasentries[$thisletter] = 1;
			endif;

			if ( strtoupper($thisletter) == strtoupper($requestshow) ) {
				
				$html .= "\r\r<div class='letterinfo'>\r<h3><a class='accordion-toggle' data-toggle='collapse' data-parent='#atozlist'  href='#atozgloss-".esc_attr($post->post_name)."''>".get_the_title()."</a></h3><div class='collapse out' id='atozgloss-".esc_attr($post->post_name)."'>" . wpautop(get_the_content()) . "</div></div>";
				$counter++;
																											
			}
			
			$activeletter = ( $requestshow == strtoupper($thisletter)) ? "active" : null;

			$letterlink[$thisletter] = ($hasentries[$thisletter] > 0) ? "<li  class='{$thisletter} {$activeletter}'><a href='?show=".$thisletter."'>".$thisletter."</a></li>" : "<li class='{$thisletter} {$activeletter}'><a>".$thisletter."</a></li>";
			
			?>
			
		<?php endwhile; ?>
		
		<?php echo @implode("",$letterlink); ?>
		
		</ul>
		
		<?php 
		
		echo wpautop( sprintf( _n('1 term' , '%d terms' , $counter , 'govintranet' ) , $counter ) );
		
		echo $html;
		?>
		
	</div>

<?php get_footer(); ?>
	
<?php endwhile; ?>

