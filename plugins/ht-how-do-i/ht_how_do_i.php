<?php
/*
Plugin Name: HT How do I?
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display a How do I? search box
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

class htHowDoI extends WP_Widget {
    function htHowDoI() {
        parent::WP_Widget(false, 'HT How Do I', array('description' => 'How do I search box'));

    } 

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
		?>
	      <?php echo $before_widget; ?>
	          <?php if ( $title )
	                echo $before_title . $title . $after_title; ?>
					<div class="well well-sm">
					<form class="form-horizontal" role="form" method="get" id="sbc-search" action="<?php echo site_url('/'); ?>">
						<div class="form-group input-md">
							<input type="text" value="" name="s" id="sbc-s" class="multi-cat form-control input-md" placeholder="How do I..." />
						</div>
						<label for="cat">Search in: </label>
						<div class="form-group input-md">
							<select name='cat' id='cat' class='form-control input-md'>
								<option value='0' selected='selected'>All tasks and guides</option>
									<?php
									$terms = get_terms('category');
										if ($terms) {
									  		foreach ((array)$terms as $taxonomy ) {
									  			if ($taxonomy->name == 'Uncategorized'){
										  			continue;
									  			}
										  		echo "<option class='level-0' value='".$taxonomy->term_id."'>".$taxonomy->name."</option>";
										  	}
										  }
									?>
							</select>
						</div>
						<div class="form-group input-md">
							<button type="submit" class="btn btn-primary input-md">Search</button>
						</div>
						<input type="hidden" name="post_type[]" value="task" />
					</form>
					</div>							
					<script type='text/javascript'>
					    jQuery(document).ready(function(){
							jQuery('#sbc-search').submit(function(e) {
							    if (jQuery.trim(jQuery("#sbc-s").val()) === "") {
							        e.preventDefault();
							        jQuery('#sbc-s').focus();
							    }
							});	
						});	
					
					</script>
              <?php echo $after_widget; ?>
			  <?php
		}

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br>

        </p>
        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htHowDoI");'));

?>