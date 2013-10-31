=== Ajax Pagination (twitter Style) ===
Contributors: nuwansh
Tags: AJAX, jquery, pagination, twitter
Tested up to: 3.3.1
Stable tag: trunk
Requires at least: 3.0.0

This plugin convert WordPress default pagination behavior into the Twitter style using Ajax functionality.

== Description ==

The "Ajax Pagination (Twitter style) plugin is used to convert WrodPress pagination system to completely Ajax functionality and it presents Twitter's pagination style. You can use this plugin to where the pagination is available in your template.

[For more information](http://bezago.com/blog/2012/01/23/ajax-pagination-twitter-old-style-wordpress-plugin/)

== Installation ==

= Follow the steps below to install the plugin. =

1. Download the plugin zip file.
1. Unzip.
1. Upload the `ajax-pagination/` folder to your `/wp-content/plugins/` directory.
1. Activate the plugin through the Plugins menu in WordPress.
1. Place  (localize the strings (second parameter) be replacing the parameters according to your current locale) in your template, for instance in `index.php`, `author.php` , `category.php` etc...
`
<?php if(function_exists('genarate_ajax_pagination')) genarate_ajax_pagination('Read More', 'blue');  ?>
`

= Usage =

`
<?php 
      if(function_exists('genarate_ajax_pagination')) 
        genarate_ajax_pagination($btn_name, $color_name, $loop, $query );  
?>
`

= Parameters =

**$btn_name**

*string (optional). The button name

*Default:  "Read More"

**$color_name**

*string (optional). The button background color name.

*Default: "blck"

*Others values:

1. blue
1. red
1. magenta
1. orange
1. yellow 

*Note: this plugin is used CSS3 functionality buttons. The original credits to [Super Awesome Buttons with CSS3 and RGBA](http://www.zurb.com/blog_uploads/0000/0617/buttons-03.html)*

**$loop**

*string (optional).

*Default: 'content'

*Note:

1. The 'twentyeleven' theme support 'content'
1. The 'twentyten' theme support 'loop'

The loop file is where the posts/content will be loaded. The default value is (if you don't set $loop variable) 'loop'. This usage is that, the plugin load your post content template, eg: `get_template_part($loop)`. Advantage of this parameter is that you can use the pagination in several places in your wordpress themes to load the posts/content from.

**$query**

*array (optional)

*Default: array()

If you are using custom query to display posts, pagination plugin also need the custom query array to run loop. The advantage of this parameter, you can use any Custom Post Type Template. eg:  `array('post_type' => 'custom')`


= Ajax callback function after complete next page is loaded =

  This plugin is used [jQuery](http://jquery.com) javaScript framework to handel the AJAX functionality on client side. So this plugin is [trigger](http://api.jquery.com/trigger/) customer event name called 'complete-paginate' once complete the next page append.
  Note: what is the important of the custom event in client side. If you have twitter or Facebook button or some specific event bind DOM in the loaded posts content. This custom event may help you to re-assign javascript functionality without any effect.  
 
  The custom event is bind into the read more button `(id="ajax_pagination_btn")`.
    eg:
`
$('#ajax_pagination_btn').bind('complete-paginate', function(event) {
  console.log('update other Js functionality');
});
`

== Frequently Asked Questions ==

= How can I get Ajax pagination button =

Place the following code after the main loop of the template.
`
<?php if(function_exists('genarate_ajax_pagination')) genarate_ajax_pagination('Read More', 'blue');  ?>
` 

= How can I change Button styles =

Third parameter is used to change button styles. This Button is available following colors

<ul>
<li> black (default: no need to set third parameter) </li>
<li> blue </li>
<li> red </li>
<li> magenta </li>
<li> orange </li>
<li> yellow </il>
</ul>

= How can I use pagination in the custom post type template =

If you have a specific template using custom post type, you have to assign the custom query array into the $query parameter.
eg:

1. Here custom post type is 'magazine'
2. Theme uses loop-magazine.php to load the posts content.
3. Now we should add following variables to the `genarate_ajax_pagination()` function.

`
    $btn_name   = 'Read More'
    $color_name = 'blue'
    $loop       = 'loop-magazine'
    $query      =  array('post_type' => 'magazine')

    <?php
       $projectspost = new WP_Query(array('post_type' => 'magazine'));
       //number of post for a page
       while ($projectspost->have_posts()) : $projectspost->the_post();
         get_template_part( 'loop', 'magazine' );
       endwhile;
    ?>
    <?php wp_reset_query();   //Restore global post data stomped by the_post(). ?>
    <?php 
        if(function_exists('genarate_ajax_pagination')) 
        genarate_ajax_pagination('Read More', 'blue', 'loop-magazine', array('post_type' => 'magazine')); 
    ?>
`

See more: http://www.zurb.com/blog_uploads/0000/0617/buttons-03.html

= Have you got any questions? =

Oh! that's good, Please email to me nuwan28 at gmail.com

*or*

Create a new issue [Ajax Pagination (twitter Style) - issues](https://github.com/nuwansh/Ajax-Pagination-Twitter-Style-for-Wordpress/issues)

== Screenshots ==

1. Pagination Button

== Changelog ==

= 1.0.0 =
* Initial Release

= 1.1 =
* optimized to query ajax functionality with fancy button
* The plugin upgraded into the 1.2 version, for WP 3.3.1 compatibility.
* Add jquery ‘complete-paginate’ custom event after next page loaded.
* Plugin is now compatibility to handle custom post type template in themes.

== Upgrade Notice ==

= 1.1 =
* The plugin is compatibility with wordpress 3.3.1 fixed
