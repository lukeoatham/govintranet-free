<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); 

$notfound=get_option('general_intranet_page_not_found');
if (!$notfound) $notfound = "That's an error";

?>


				<div class="row">
					
					<div class='col-lg-12 white'>
						<div class="content-wrapper">

							<h1 class="entry-title"><?php echo $notfound; ?></h1>

							<p><?php _e( 'The page that you are trying to reach doesn\'t exist. <br><br>Please go back or try searching.', 'twentyten' ); ?></p><br>
							<div class='col-lg-6'>
							
								<form class="form-horizontal" role="form" action="<?php echo home_url( '/' ); ?>">
							
								  <div class="col-lg-12">
								    <div class="input-group">
										    	 <input type="text" class="form-control" placeholder="Search again" name="s" id="snf" value="<?php echo $_GET['s'];?>">
								      <div class="input-group-btn">
												<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button>
								      </div><!-- /btn-group -->
								    </div><!-- /input-group -->
								  </div><!-- /.col-lg-6 -->
							
								</form>
		
							</div>
					</div>
				</div>
				
				</div>

<?php get_footer(); ?>