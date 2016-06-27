<?php
/*
Plugin Name: HT Need to know AJAX
Plugin URI: http://www.helpfultechnology.com
Description: Display need to know news stories using AJAX
Author: Luke Oatham
Version: 1.1
Author URI: http://www.helpfultechnology.com
*/

class htNeedToknowAJAX extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htNeedToknowAJAX',
			__( 'HT Need to know AJAX' , 'govintranet'),
			array( 'description' => __( 'Need to know AJAX news widget' , 'govintranet') )
		);   
    }
    
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $items = intval($instance['items']);
        $hide = $instance['hide'];

        $path = plugin_dir_url( __FILE__ );

        wp_enqueue_script( 'ht_need_to_know', $path.'js/ht_need_to_know_ajax.js' );
        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $params = array(
          'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		'items' => $items,
		'before_widget' => stripcslashes($before_widget),
		'after_widget' => stripcslashes($after_widget),
		'before_title' => stripcslashes($before_title),
		'after_title' => stripcslashes($after_title),
		'title' => $title,
		'hide' => $hide,
          
        );
        wp_localize_script( 'ht_need_to_know', 'ht_need_to_know', $params );

		echo "<div id='need-to-know' class='ht_need_to_know_ajax'></div>";
    }


		
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['hide'] = strip_tags($new_instance['hide']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        $hide = esc_attr($instance['hide']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>
          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          <input id="<?php echo $this->get_field_id('hide'); ?>" name="<?php echo $this->get_field_name('hide'); ?>" type="checkbox" <?php checked((bool) $instance['hide'], true ); ?> />
          <label for="<?php echo $this->get_field_id('hide'); ?>"><?php _e('Hide if already read','govintranet'); ?></label> <br>
        </p>

        <?php 
    }

}

add_action( 'wp_ajax_ht_need_to_know_ajax_show', 'ht_need_to_know_ajax_show' );
add_action( 'wp_ajax_nopriv_ht_need_to_know_ajax_show', 'ht_need_to_know_ajax_show' );
function ht_need_to_know_ajax_show() {

	$items = absint( $_POST['items'] );
	$title = esc_attr($_POST['title']);
	$before_widget = stripcslashes($_POST['before_widget']);
	$after_widget = stripcslashes($_POST['after_widget']);
	$before_title = stripcslashes($_POST['before_title']);
	$after_title = stripcslashes($_POST['after_title']);
	$hide = $_POST['hide'];
	
    $response = new WP_Ajax_Response;
	global $post;
			
	$html = load_news($items, $title, $before_widget, $after_widget, stripcslashes($before_title), $after_title, $hide);

    if(
        $html
    ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => $html
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => 'an error occured'
            ),
        ) );
    }

    $response->send();
    
    exit();
}


 function load_news( $items, $title, $before_widget, $after_widget, $before_title, $after_title, $hide ) {
 		global $post;
 		$html = "";
	
		$cquery = array(
			'orderby' => 'post_date',
		    'order' => 'DESC',
		    'post_type' => 'news',
		    'posts_per_page' => -1,
		    'tax_query' => array(array(
		    'taxonomy'=>'post_format',
		    'field'=>'slug',
		    'terms'=>array('post-format-status'),
		    ))
			);
	
		$news = new WP_Query($cquery); 
		$read = 0;
		$show = 0;
		$alreadydone = array();
		if ($hide):
			while ($news->have_posts()):
				$news->the_post();  
				if (isset($_COOKIE['ht_need_to_know_'.get_the_id()])):
					$read++; 
					$alreadydone[]=get_the_id();
				else:
					$show++; 
				endif;
			endwhile;
		else:
			$show=1; 
		endif;
		$k=0;
		while ($news->have_posts()) {
			$news->the_post();
			if (in_array(get_the_id(), $alreadydone ) || $k > $items) continue;  //don't show if already read
			if ( get_post_status(get_the_id()) != "publish" ) continue;  //don't show if already read
			$k++;
			if ($k > $items) break;
			$thistitle = get_the_title();
			$thisURL=get_permalink();
			$icon = get_option('options_need_to_know_icon');
			if ($icon=='') $icon = "flag";
			$html.= "<li><a href='{$thisURL}' onclick='Javascript:pauseNeedToKnowAJAX(\"ht_need_to_know_".get_the_id()."\");'><span class='glyphicon glyphicon-".$icon."'></span> ".$thistitle."</a>";
			if ( get_comments_number() ){
				$html.=  " <a href='".$thisURL."#comments'>";
				$html.= '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number(), 'govintranet' ), get_comments_number() ) . '</span>';
				 $html.=  "</a>";
			}
			$html.= "</li>";
		}
		if ($k){
			if ( $title ) $html =  $before_title . $title . $after_title . $html;
			$html= "<div class='need-to-know'><ul class='need'>" . $html; 
			$html= "<div class='zign2n category-block'>" . $html; 
			$html.= "</ul></div>";
			$html.= $after_widget;
		}
		return $html;	 
 }
 
add_action('widgets_init', create_function('', 'return register_widget("htNeedToknowAJAX");'));
		
?>