<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 */

get_header(); 

$notfound = get_option('options_page_not_found', __('Not found','govintranet'));
if ( !$notfound ) $notfound = "Not found";
?>
	<div class='col-lg-12 white'>
		<h1 class="entry-title"><?php  echo $notfound;  ?></h1>
		<p><?php _e( 'The page that you are trying to reach doesn\'t exist. <br><br>Please go back or try searching.', 'govintranet' ); ?></p><br>
		<div class='col-lg-6'>
			<form class="form" role="form" action="<?php echo site_url( '/' ); ?>">
			  	<div class="col-lg-12">
			   		<div class="input-group">
			    		<input type="text" class="form-control" placeholder="<?php _e('Search','govintranet');?>..." name="s" id="snf" value="<?php echo the_search_query();?>">
						<div class="input-group-btn">
							<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php get_footer(); ?>