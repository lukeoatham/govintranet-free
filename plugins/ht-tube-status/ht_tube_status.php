<?php
/*
Plugin Name: HT Tube status
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display London Underground tube status
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

class htTubeStatus extends WP_Widget {
    function htTubeStatus() {
        parent::WP_Widget(false, 'HT Tube status', array('description' => 'Tube status updates'));

    } 

    function widget($args, $instance) {

	    $styleurl = plugins_url('ht-tube-status/ht_tube_status.css');
		wp_enqueue_style( 'htTubeStatus_style', $styleurl );
    
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);

		echo $before_widget; 
		if ( $title )   echo $before_title . $title . $after_title; 


		// load feed from TfL
		
		include_once( ABSPATH . WPINC . '/feed.php' );
			
		$feedurl = "http://cloud.tfl.gov.uk/TrackerNet/LineStatus";
		$rss = file_get_contents($feedurl); 

		if (!$rss):
			echo "Can't find the TfL tube status feed.";
			exit;
		endif;
	
		$feedcontent = new SimpleXMLElement($rss);
		
		// build into arrays
		
		$linestatusName = array();
		$linestatusDesc = array();
		$linestatusClass = array();
		$linestatusID = array();
		$linestatusFrom = array();
		$linestatusTo = array();
		$linestatusDetails = array();
					
		if ($feedcontent->LineStatus) {
			$item = $feedcontent->LineStatus;
	
			foreach ($item as $i){
				$linestatusID[]=$i->Line['ID'];
				$linestatusName[]=$i->Line['Name'];
				$linestatusDesc[]=$i->Status['Description'];
				$linestatusClass[]=$i->Status['CssClass'];
				$linestatusFrom[]=$i->BranchDisruptions->BranchDisruption->StationFrom['Name'];
				$linestatusTo[]=$i->BranchDisruptions->BranchDisruption->StationTo['Name'];
				$linestatusDetails[]=$i['StatusDetails'];
			}			
	
			$counter = 0;
			$totallines = count($linestatusName);
			$output = array();
			
			foreach ((array)$linestatusName as $l){
				$last='';
				if ($counter == 0) $last = ' ht_first-link';
				
				
				$output[]= "<div class='row'>";
				$output[]= "<div class='col-lg-6 col-md-12 col-sm-6 ht_tubeline ht_tubeline".$linestatusID[$counter]." ".$linestatusClass[$counter]."'>".$linestatusName[$counter]."</div>";

				//if problems
				if ($linestatusDetails[$counter] != ""):
					$output[]= "<div class='col-lg-6 col-md-12 col-sm-6 ht_tubestatus ".$linestatusClass[$counter].$last."'>";
					$output[]= '<a data-toggle="collapse" data-parent="#ht_tube_status_widget" href="#collapse'.$counter.'">'.$linestatusDesc[$counter]."</a>";
					$output[]= "</div><div class='col-lg-12 col-md-12 col-sm-12'><p id='collapse".$counter."' class='collapse out'>".$linestatusDetails[$counter]."</p></div>";
				else: 
				//if good service
					$output[]= "<div class='col-lg-6 col-md-12 col-sm-6 ht_tubestatus ".$linestatusClass[$counter].$last."'>".$linestatusDesc[$counter];
				$output[]= "</div>";

				endif;				

				//if ($linestatusTo[$counter]) $output[]= "<br>From ".$linestatusFrom[$counter]." to ".$linestatusTo[$counter];

				$output[]= "</div>";
				$counter++;
			}
			
			// ouput
			
			echo "<div id='ht_tube_status_widget' class='col-sm-12'>";
			echo implode("",$output);
			echo "</div>";
	
		}

       echo $after_widget; 
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

add_action('widgets_init', create_function('', 'return register_widget("htTubeStatus");'));

?>