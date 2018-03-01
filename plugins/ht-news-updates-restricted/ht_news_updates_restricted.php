<?php
/*
Plugin Name: HT News updates - restricted
Plugin URI: https://help.govintra.net
Description: Hide news updates from users
Author: Luke Oatham
Version: 1.3.1
Author URI: https://www.agentodigital.com
*/

class htnewsupdatesrestricted extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htnewsupdatesrestricted',
			__( 'HT News updates restricted' , 'govintranet'),
			array( 'description' => __( 'Hides news updates from users' , 'govintranet') )
		);   

		if( function_exists('acf_add_local_field_group') ):
			acf_add_local_field_group(array (
				'key' => 'group_nu558c858e438b9',
				'title' => _x('Update types','Categories of news updates','govintranet'),
				'fields' => array (
					array (
						'key' => 'field_nu558c85a4c1c4b',
						'label' => _x('News update type','Categories of news updates','govintranet'),
						'name' => 'news_update_widget_include_type',
						'type' => 'taxonomy',
						'instructions' => __('Choose "None" to include all alerts.','govintranet'),
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'taxonomy' => 'news-update-type',
						'field_type' => 'checkbox',
						'allow_null' => 1,
						'add_term' => 0,
						'save_terms' => 0,
						'load_terms' => 0,
						'return_format' => 'id',
						'multiple' => 0,
					),
					array (
						'key' => 'field_nu558c96d235d45',
						'label' => _x('Update background colour','The background colour of the news update','govintranet'),
						'name' => 'news_update_background_colour',
						'type' => 'color_picker',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
					),
					array (
						'key' => 'field_nu558c96e035d46',
						'label' => _x('Update text colour','The colour of the news update text','govintranet'),
						'name' => 'news_update_text_colour',
						'type' => 'color_picker',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
					),
					array (
						'key' => 'field_nu558c9cb48c113',
						'label' => __('Border colour','govintranet'),
						'name' => 'news_update_border_colour',
						'type' => 'color_picker',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '#000000',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'widget',
							'operator' => '==',
							'value' => 'htnewsupdatesrestricted',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
			));


			acf_add_local_field_group(array (
				'key' => 'group_56ad7b9c1828f',
				'title' => __('News updates','govintranet'),
				'fields' => array (
					array (
						'key' => 'field_56ad7bbd100c9',
						'label' => __('Hide news updates','govintranet'),
						'name' => 'user_hide_news_updates',
						'type' => 'taxonomy',
						'instructions' => __('Hide news for selected types.','govintranet'),
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'taxonomy' => 'news-update-type',
						'field_type' => 'checkbox',
						'allow_null' => 1,
						'add_term' => 0,
						'save_terms' => 0,
						'load_terms' => 0,
						'return_format' => 'id',
						'multiple' => 0,
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'user_form',
							'operator' => '==',
							'value' => 'all',
						),
					),
				),
				'menu_order' => 200,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => 1,
				'description' => '',
			));
		
		endif;		
    }
    
    function widget($args, $instance) {
        extract( $args );

	    $userid = get_current_user_id();
        if ( $userid ):
        	$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_user_" . $userid; 
        else:
	        $acf_key = "widget_" . $this->id_base . "-" . $this->number . "_user_all"; 
        endif;
        $trans = get_transient($acf_key);
        if ( $trans ):
			 echo "<div data-id='".$this->number."' id='news-updates-restricted-".$this->number."' class='ht_news_updates_restricted_".$this->number."'>".$trans."</div>";
        else:
        
        $title = apply_filters('widget_title', $instance['title']);
        if ( !$title ) $title = "no_title_" . $id;
        $moretitle = $instance['moretitle'];
        $items = intval($instance['items']);
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_news_update_widget_include_type" ;  
		$news_update_types = get_option($acf_key); 
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_news_update_background_colour" ;  
		$background_colour = get_option($acf_key); 
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_news_update_text_colour" ;  
		$text_colour = get_option($acf_key); 
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_news_update_border_colour" ;  
		$border_colour = get_option($acf_key); 
		$border_height = get_option('options_widget_border_height','5');
        $path = plugin_dir_url( __FILE__ );
        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $ajaxurl = admin_url( 'admin-ajax.php', $protocol );
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
		    
			jQuery('.ht_news_updates_restricted_<?php echo $this->number; ?>').slideUp();
			
			var jqXHR = $.ajax({
					url : "<?php echo $ajaxurl; ?>",
					type: "GET",
					data : { 
			                action: 'load_news_updates',
				            items: "<?php echo $items; ?>",
							before_widget: '<?php echo $before_widget; ?>',
							title: "<?php echo $title; ?>",
							after_widget: '<?php echo $after_widget; ?>',
							before_title: '<?php echo $before_title; ?>',
							after_title: '<?php echo $after_title; ?>',
							news_update_types: "<?php echo implode(",", $news_update_types); ?>",
							background_colour: "<?php echo $background_colour; ?>",
							text_colour: "<?php echo $text_colour; ?>",
							border_colour: "<?php echo $border_colour; ?>",
							border_height: "<?php echo $border_height; ?>",
							path: "<?php echo $path; ?>",
							moretitle: "<?php echo $moretitle; ?>",
							widget_id: "<?php echo $this->number; ?>",
							widget_base: "<?php echo $this->id_base; ?>",
							}
				}).done(function (data, status, jqXHR) {
					if (data != 0){	
						jQuery('.ht_news_updates_restricted_<?php echo $this->number; ?>').append(data);
						jQuery('.ht_news_updates_restricted_<?php echo $this->number; ?>').slideDown(200);
				  	}
				}).fail(function (jqXHR,status,err) {
				}).always(function () {
				})
			});
		</script>
		<?php        
          
		echo "<div data-id='".$this->number."' id='news-updates-restricted-".$this->number."' class='ht_news_updates_restricted_".$this->number."'></div>";
        endif;
 
    }
		
   function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['moretitle'] = strip_tags($new_instance['moretitle']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        $moretitle = esc_attr($instance['moretitle']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('moretitle'); ?>"><?php _e('Title for more:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('moretitle'); ?>" name="<?php echo $this->get_field_name('moretitle'); ?>" type="text" value="<?php echo $moretitle; ?>" /><br><?php echo __('Leave blank for the default title','govintranet') . '<br>' ; ?>
          
        </p>
        <?php
	        
    }

}
add_action('wp_ajax_load_news_updates', 'load_news_updates');
add_action('wp_ajax_nopriv_load_news_updates', 'load_news_updates');

function load_news_updates(  ) {
		$html= "";
		$items = absint( $_GET['items'] );
		$widget_id =  stripcslashes($_GET['widget_id']) ;
		$widget_base =  stripcslashes($_GET['widget_base']) ;
		$title = stripcslashes($_GET['title']);
		$before_widget = (stripcslashes($_GET['before_widget']));
		$after_widget = (stripcslashes($_GET['after_widget']));
		$before_title = (stripcslashes($_GET['before_title']));
		$after_title = (stripcslashes($_GET['after_title']));
		$news_update_types = explode(",", esc_attr($_GET['news_update_types']));
		$background_colour = (stripcslashes($_GET['background_colour']));
		$text_colour = (stripcslashes($_GET['text_colour']));
		$border_colour = (stripcslashes($_GET['border_colour']));
		$border_height = (stripcslashes($_GET['border_height']));
		$path = (stripcslashes($_GET['path']));
		$moretitle  = (stripcslashes($_GET['moretitle']));
		$hidetypes = array();

		$kcount = 0;
		foreach ($news_update_types as $n){
			if ($n) $kcount++;
		}
		if ( !$kcount ):
			$terms = get_terms('news-update-type');
			$news_update_types = array();
			if ( $terms ) foreach ( $terms as $term ){
				$news_update_types[] = $term->term_id;
			}
		endif;

 		global $post;
		$cuser = get_current_user_id();

		if ( isset($cuser) && $cuser > 0 ):
			// remove hidden types
			$hidetypes = get_user_meta($cuser, 'user_hide_news_updates', true );
			if ( !$hidetypes ) $hidetypes = array();
			$widgettypes = $news_update_types;
			$updatedtype = array();
			if ( $widgettypes) foreach ( $widgettypes as $w ){
				if ( !in_array($w, $hidetypes) ) $updatedtype[] = $w;
			}
			$news_update_types = $updatedtype;
			if (empty($news_update_types)):
				// widget set to display news update types that are ALL blocked by the user
				exit();
			endif;
		endif;

		//process expired news
		
		$tzone = get_option('timezone_string'); 
		if ( $tzone ) date_default_timezone_set($tzone);
		$tdate= date('Ymd');
		
		$oldnews = query_posts(array(
			'post_type'=>'news-update',
			'meta_query'=>array(array(
				'key'=>'news_update_expiry_date',
				'value'=>$tdate,
				'compare'=>'<='
		))));
		
		if ( count($oldnews) > 0 ){
			foreach ($oldnews as $old) {
				if ($tdate == date('Ymd',strtotime(get_post_meta($old->ID,'news_update_expiry_date',true)) )): // if expiry today, check the time
					if (date('H:i:s',strtotime(get_post_meta($old->ID,'news_update_expiry_time',true))) > date('H:i:s') ) continue;
				endif;
				$expiryaction = get_post_meta($old->ID,'news_update_expiry_action',true);
				if ($expiryaction=='Revert to draft status'){
					  delete_post_meta($old->ID, 'news_update_expiry_date');
					  delete_post_meta($old->ID, 'news_update_expiry_time');
					  delete_post_meta($old->ID, 'news_update_expiry_action');
					  delete_post_meta($old->ID, 'news_auto_expiry');
					  $my_post = array();
					  $my_post['ID'] = $old->ID;
					  $my_post['post_status'] = 'draft';
					  wp_update_post( $my_post );
				}	
				if ($expiryaction=='Move to trash'){
					  delete_post_meta($old->ID, 'news_update_expiry_date');
					  delete_post_meta($old->ID, 'news_update_expiry_time');
					  delete_post_meta($old->ID, 'news_update_expiry_action');
					  delete_post_meta($old->ID, 'news_auto_expiry');
					  $my_post = array();
					  $my_post['ID'] = $old->ID;
					  $my_post['post_status'] = 'trash';
					  wp_update_post( $my_post );
				}	
			}
			wp_reset_query();
		}

		$cquery = array(
			'post_status' => 'publish',
			'orderby' => 'post_date',
		    'order' => 'DESC',
		    'post_type' => 'news-update',
		    'posts_per_page' => $items,
		    'tax_query' => array(array(
			    'taxonomy' => 'news-update-type',
			    'field' => 'id',
			    'terms' => (array)$news_update_types,
			    'compare' => "IN",
		    ))
			);

		$news = new WP_Query($cquery); 
		if ($news->post_count!=0){
			$html.= "<style>";
			if ( $border_colour ):
				$html.= ".need-to-know-container.".sanitize_file_name($title)." { background-color: ".$background_colour."; color: ".$text_colour."; padding: 1em; margin-top: 10px; border-top: ".$border_height."px solid ".$border_colour." ; }\n";
			else:
				$html.= ".need-to-know-container.".sanitize_file_name($title)." { background-color: ".$background_colour."; color: ".$text_colour."; padding: 1em; margin-top: 16px; border-top: 5px solid rgba(0, 0, 0, 0.45); }\n";
			endif;
			$html.= ".need-to-know-container.".sanitize_file_name($title)." h3 { background-color: ".$background_colour."; color: ".$text_colour."; }\n";
			$html.= ".need-to-know-container.".sanitize_file_name($title)." a { color: ".$text_colour."; }\n";
			$html.= ".need-to-know-container.".sanitize_file_name($title)." .widget-box { background: ".$background_colour."; }\n";
			$html.= ".need-to-know-container.".sanitize_file_name($title)." .widget-box h3 { padding: 0 ; margin-top: 0; border: none ; color: ".$text_colour."; }\n";
			$html.= ".need-to-know-container.".sanitize_file_name($title)." .widget-box ul  {  padding: 0; }\n";
			$html.= ".need-to-know-container.".sanitize_file_name($title)." .widget-box ul li { border-top: 1px solid rgba(255, 255, 255, 0.45); padding: 0.5em 0; }\n";
			$html.= ".need-to-know-container.".sanitize_file_name($title)." .widget-box p.more-updates { margin-bottom: 0 !important; margin-top: 10px; font-weight: bold; }\n";
			$html.= "#content .need-to-know-container .widget-box { background-color: transparent;}";
			$html.= "#content .need-to-know-container .widget-box { border-top: 0; margin-top: 0;}";
			$html.= ".need-to-know-container.".sanitize_file_name($title)." .category-block { background: ".$background_colour."; }\n";
			$html.= ".need-to-know-container.".sanitize_file_name($title)." .category-block h3 { padding: 0 ; margin-top: 0; border: none ; color: ".$text_colour."; }\n";
			$html.= ".need-to-know-container.".sanitize_file_name($title)." .category-block ul  {  padding: 0; }\n";
			$html.= ".need-to-know-container.".sanitize_file_name($title)." .category-block ul li { border-top: 1px solid rgba(255, 255, 255, 0.45); padding: 0.5em 0; }\n";
			$html.= ".need-to-know-container.".sanitize_file_name($title)." .category-block p.more-updates { margin-bottom: 0 !important; margin-top: 10px; font-weight: bold; }\n";
			$html.= "#content .need-to-know-container .category-block { background-color: transparent;}";
			$html.= "#content .need-to-know-container .category-block { border-top: 0; margin-top: 0;}";
			$html.= ".home .need-to-know-container.".sanitize_file_name($title)."  { margin-bottom: ; margin-top: 15px; }\n";

			$html.= "</style>";	
	
			if ( $title ) {
				$html.= "<div class='need-to-know-container ".sanitize_file_name($title)."'>";
				$html.= $before_widget; 
				if ( $title == "no_title_" . $id ) $title = "";
				if ( $title ) $html.= $before_title . $title . $after_title;
			}
			$html.= "
			<div class='need-to-know'>
			<ul class='need'>";
		} 
		$k=0;
		$alreadydone= array();
		while ($news->have_posts()) {
			$news->the_post();
			$k++;
			if ($k > $items){
				break;
			}
			$thistitle = get_the_title($news->ID);
			$thisURL=get_permalink($news->ID);
			$display_types = '';
			$display_types = array();
			$types_array = get_the_terms($news->ID, 'news-update-type'); 
			if ( $types_array ) foreach ( $types_array as $t ){
				$display_types[] = $t->name;
				if ( version_compare( get_option('acf_version','1.0'), '5.5', '>' ) && function_exists('get_term_meta') ):
					$icon = get_term_meta($t->term_id, "news_update_icon", true);
				else:
					$icon = get_option('news-update-type_'.$t->term_id.'_news_update_icon'); 
				endif;
				if ($icon=='') $icon = get_option('options_need_to_know_icon');
				if ($icon=='') $icon = "flag";
			}
			$display_types = implode(", ", $display_types); 
			$html.= "<li><a href='{$thisURL}' title='".$display_types." update'><span class='glyphicon glyphicon-".$icon."'></span> ".$thistitle."</a>";
			if ( get_comments_number() ){
				$html.= " <a href='".$thisURL."#comments'>";
				$html.= '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number(), 'govintranet' ), get_comments_number() ) . '</span>';
				$html.= "</a>";
			}
			$html.= "</li>";
		}
		if ($news->post_count!=0){ 
			$html.= "</ul></div>"; 
			if ( !$moretitle ) $moretitle = $title;
			if ( is_array($news_update_types) && count($news_update_types) < 2 ): 
				$term = intval($news_update_types[0]); 
				$landingpage = get_term_link($term, 'news-update-type'); 
				$html.= '<p class="more-updates"><a title="'.$moretitle.'" class="small" href="'.$landingpage.'">'.$moretitle.'</a> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';	
			else: 
				$landingpage_link_text = $moretitle;
				$landingpage = site_url().'/news-update/';
				$html.= '<p class="more-updates"><a title="'.$landingpage_link_text.'" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';	
			endif;
			$html.= "<div class='clearfix'></div>";			
			$html.= $after_widget;
			$html.= "</div>";
		}
		
		wp_reset_query();	
		echo $html;	 
		$userid = get_current_user_id();
        if ( $userid ):
        	$acf_key = "widget_" . $widget_base . "-" . $widget_id . "_user_" . $userid; 
        else:
	        $acf_key = "widget_" . $widget_base . "-" . $widget_id . "_user_all"; 
        endif;
		set_transient($acf_key, $html, 60*30 );
		exit();

 }

add_action('widgets_init', create_function('', 'return register_widget("htnewsupdatesrestricted");'));
		
?>