<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>

				<div class="row">
					
					<div class="eightcol white last" id='content'>
											<div class="row">
							<div class='breadcrumbs'>
							<?php if(function_exists('bcn_display') && !is_front_page()) {
								bcn_display();
							}?>
							</div>
							
				</div>
				<div class="content-wrapper">

<?php 

$s=$_GET['s'];
if ( have_posts() ) : 

				if ($_GET['pt']=='forums'){
					$query->query_vars['s'] = $s;
					$query->query_vars['posts_per_page'] = 10;
					$query->query_vars['post_type'] = array('forum','topic','reply');
					if (function_exists('relevanssi_do_query')){
						relevanssi_do_query($query);
					}
				}
?>
				<h1><?php printf( __( 'Search results for: %s', 'twentyten' ), '' . $s . '' ); ?></h1>
				<?php
				if ($_GET['pt']=='forums'){
					echo "<p class='news_date'>Showing results from forums. <a href='/?s=".$_GET['s']."'>Search the intranet</a></p>";
				}
				if ($wp_query->found_posts>1){
				echo "<p class='news_date'>Found ".$wp_query->found_posts." results</p>";
				}
				/* Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called loop-search.php and that will be used instead.
				 */
				 get_template_part( 'loop', 'search' );
				?>
<?php else : ?>
					<h1><?php _e( 'Nope', 'twentyten' ); ?></h1>
					<script type="text/javascript">
					_gaq.push(['_trackEvent', 'Search', 'Empty results', '<?php echo $_GET['s'];?>']);
					</script>
					<?php 
					$pt = $_GET['post_type'];
					$ct = $_GET['cat'];
					$ct = get_category($ct);
					$ct = $ct->name;
					if ($ct){
						$searchcon=$ct;
					}
					if ($pt=='task'){
						$searchcon.=' tasks';
					}
					if ($pt=='projects'){
						$searchcon.=' projects';
					}
					if ($pt=='vacancies'){
						$searchcon.=' vacancies';
					}
					if ($_GET['pt']=='forums'){
						$searchcon.=' the forums';
						$pt=$_GET['pt'];
					}					 ?>
					<p><?php 
					if ($pt){
					_e( 'Sorry, there\'s nothing in ' . $searchcon . ' like that. We\'ve made a note. Please try searching the whole intranet.', 'twentyten' ); 
					}
					else
					{
					_e( 'Sorry, there\'s nothing on the intranet like that. We\'ve made a note. Please try some other words.', 'twentyten' ); 				
					}
					?>
					
					</p>
					<p>
												<?php
$q = $_GET['s'];
 if (function_exists('relevanssi_didyoumean')) { relevanssi_didyoumean(get_search_query(), "<p>Did you mean: ", "?</p>", 5);
}
?>

<form role="search" method="get" id="searchformnf" action="/">
    <div><label class="screen-reader-text hiddentext" for="snf">Search for:</label>
        <input type="text" value="<?php echo $q; ?>" name="s" id="snf" accesskey='4' />
        <input type="submit" id="searchsubmitnf" value="Search" />
    </div>
</form>

						
					</p>
					

<?php endif; ?>
							<div class="wp_pagenavi">
					<?php if (  $items->max_num_pages > 1 ) : ?>
						<?php if (function_exists(wp_pagenavi)) : ?>
							<?php wp_pagenavi(array('query' => $items)); ?>
 						<?php else : ?>

							<?php next_posts_link('&larr; Older items', $items->max_num_pages); ?>
							<?php previous_posts_link('Newer items &rarr;', $items->max_num_pages); ?>						
						
						<?php endif; ?>
					<?php endif; ?>
<?php
				if ($_GET['pt']!='forums'){
					echo "<br><p class='news_date'><a href='/?s={$s}&pt=forums'>Search in forums</a></p>";
				}
?>
					</div>
						</div></div>
					<div class="fourcol last" id='sidebar'>
						<?php 
						//wp_reset_postdata(); 
						    wp_reset_query();

?>
		<?php 	dynamic_sidebar('serp-widget-area'); ?>	
						
					</div>
				</div>


<?php get_footer(); ?>
