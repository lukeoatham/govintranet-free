<?php
  /*
  Plugin Name: Ajax Pagination (Twitter Style)  
  Description: The "Ajax Pagination" plugin is used to enhance the pagination experience of Wordpress just like Twiiter's pagination style & functionality 
  Author: Nuwansh
  Version: 1.2
  Author URI: http://www.bezago.com
  Updated by Luke Oatham to only load on /news pages.
  */

  //adding a new menu item
  

  
  function twitpaginate_admin_actions() {
    add_options_page("Ajax Pagination Option", "Ajax Pagination", 10, "ajax-pagination-display", "ajaxpaginate_admin");
  }
	
  function twitpaginate_admin(){
    if (!current_user_can('manage_options'))  {
      wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  echo '<div class="wrap">';
  echo '<p>Here is where the form would go if I actually had options.</p>';
  echo '</div>';
  
	}
  
 
	/*******************************************************************************************************
	 * Following functions are use to front end interface
	 */

	/** This genarate_ajax_pagination function is used to genarate the pagination button
	 *  @param Array WordPress $wp_query variable
	 *  @param String Localize the Button name
	 *  @param String Button style
	 *  @return String HTML  pagination Button 
	 */
	
	function genarate_ajax_pagination($readMore='Read More', $buttonStyle = '', $loop = 'content', $query = array()){
		global $wp_query;

		if(!empty($query)){
      //if user give custom query
			$query = serialized($query,true);
      $post_query = new WP_Query($query);
      if ($_GET['s']){
      relevanssi_do_query($post_query);
      }
      $maxpages = $post_query->max_num_pages;
      wp_reset_query();
		}else{
			$query = '';
		  $maxpages = $wp_query->max_num_pages;
		}

		//if no 2 pages just return nothing
		if($maxpages < 2 )
			return; 
		//get the ajax loading animation gif
		$src_path = WP_PLUGIN_URL . '/ajax-pagination/img/ajax-loader.gif';
		
		$linkHTML ='<div id="ajax-post-container"></div>
                  <div class="more_post">
                      <a class="btn btn-primary" id="ajax_pagination_btn" href="#" ><span class="_ajax_link_text">'.$readMore.'</span></a> <span class="_ajaxpaging_loading" style="display: none;"><img src="'.$src_path.'" alt="Loading.." /></span>
                  </div>';
		$linkHTML .= '<script type="text/javascript">jQuery(function() {
  jQuery("#ajax_pagination_btn").ajaxpaging({
    loop: "'.$loop.'",
    maxpages: "'.$maxpages.'",
    query: "'.$query.'"
  });
}); 
</script>';

		echo $linkHTML;
	}



  function ajax_navigation_callback(){
    global $wp_query, $page_no, $loop, $query; // this is how you get access to the database

    $loop   = empty($_POST['loop']) ? 'loop' : $_POST['loop'];
    $posts_per_page  = get_option('posts_per_page');

    //merge the query array and paged
    $query = $_POST; 
    unset($query['action']);
    unset($query['loop']);
    
	  ob_start();
    # Load the posts
		if($loop){
      query_posts($query);
      
      while(have_posts()) : the_post();
         get_template_part($loop);
      endwhile;
    }

    $buffer = ob_get_contents();
    ob_end_clean();

    echo $buffer;
    exit;
    // this is required to return a proper result
  }
	    add_action('wp_ajax_ajax_navigation', 'ajax_navigation_callback');
	//serialized string creating
	function serialized($serialized = array(),$token){
		$serializeString = '';
		$sizeofArray = count($serialized);

		$temp = 0;		
		foreach($serialized as $key => $value){
				$temp++;
				if(is_array($value)){					
					$value = serialized($value,false);
				}
				//change the combain string
				if($token){
					$serializeString .=	$key . "=" . $value . (($temp < $sizeofArray)? "&": "");
				}else{
					$serializeString .=	$value . (($temp < $sizeofArray)? ",": "");
				}
		}
		return $serializeString;
  }
  
  
$urlstr = explode("/",$_SERVER['REQUEST_URI']);
if ( (($urlstr[1]=="newspage" || $urlstr[1]=="blog") && !$urlstr[2]) ):
	//define the js files
	function ajaxpaging_js(){
		wp_register_script('twitnavi', WP_PLUGIN_URL . '/ajax-pagination/js/jquery.ajaxpaging.js.php');
		wp_enqueue_script('twitnavi');
	}
		add_action('wp_print_scripts', 'ajaxpaging_js');

endif;  	
	//load the css file
	function ajaxpaging_css(){
		  wp_register_style('twitCSS', WP_PLUGIN_URL . '/ajax-pagination/css/ajaxpaging.css');
    wp_enqueue_style('twitCSS');
	}
		add_action('wp_print_styles','ajaxpaging_css');

  //load jquery file
  function ajax_init_js_scipts(){
     wp_enqueue_script('jquery');
  }
    add_action('init','ajax_init_js_scipts');
		
    //featch the posts from next page
    add_action('wp_ajax_nopriv_ajax_navigation', 'ajax_navigation_callback');


?>
