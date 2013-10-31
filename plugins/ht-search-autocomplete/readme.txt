=== SearchAutocomplete ===
Contributors: hereswhatidid
Donate link: http://hereswhatidid.com/contact/
Tags: jquery autocomplete, jquery ui, themeroller, search, autocomplete, ajax
Requires at least: 3.x
Tested up to: 3.6.0
Stable tag: 2.1.2

SearchAutocomplete implements the jQuery UI Autocomplete functionality on your Wordpress installation. 

== Description ==
 
SearchAutocomplete implements the jQuery UI Autocomplete functionality on your Wordpress installation. It provides several basic options such as:

* jQuery ThemeRoller integration
* Generic field selection. Any valid CSS selector can be used to pick the search field
* Custom Post Type and Taxonomy support
* Minimum character count
* Hotlink resulting Post/Page titles directly to their respective items

If you're going to mark this plugin as broken please contact me to let me know what the problem is.

[Contact Author](http://hereswhatidid.com/contact/)

= Contributors =
[Gabe Shackle](http://hereswhatidid.com)

== Plugin Official Site ==

<a href="http://hereswhatidid.com/search-autocomplete/">http://hereswhatidid.com/search-autocomplete/</a>

If you have any questions or comments <a href="http://hereswhatidid.com">please contact me</a>.

== Thanks to ==

* <a href="http://hereswhatidid.com">here's what I did...</a>
* <a href="http://jquery.com">jQuery</a>
* <a href="http://jqueryui.com/demos/autocomplete/">jQuery UI Autocomplete</a>
* <a href="http://jqueryui.com/themeroller/">jQuery UI ThemeRoller</a>

== Installation ==

This section describes how to install the plugin and get it working.

1. Download the plugin from the <a href="http://wordpress.org/extend/plugins/search-autocomplete/">Wordpress repository</a>
2. Upload `search-autocomplete` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. {Optional} If you want to add aditional themes to the settings, create the theme with the <a href="http://jqueryui.com/themeroller/">jQuery UI ThemeRoller</a> and upload the resulting "/css/" directory to the "/css/" of this plugins installed directory.

== Frequently Asked Questions ==

= Can I just modify the existing theme files rather than upload new jQuery UI Themes? =

Yes, but it's recommended that you create a copy of the theme that you are going to modify rather than directly editing it.  Future releases of this plugin may overwrite your changes if they are made to default theme files.

Please <a href="http://hereswhatidid.com/contact/">contact the author</a> for support. Thanks!

== Change log ==

= 2.1.2 - 30 Jul 2013
* Fixed bug with js file path

= 2.1.1 - 29 Jul 2013
* Fixed bug with hotlinking options not saving

= 2.1.0 - 26 Jul 2013
* Added new passed parameter to the title and url filters that contains information about the current term or post (id, type, taxonomy, posttype)

= 2.0.6 - 24 Jul 2013 =
* Fixed bug with taxonomy/post order was being ignored
* Fixed bug Taxonomy type options being ignored

= 2.0.5 - 17 Jul 2013 =
* Fixed bug with minLength being ignored

= 2.0.4 - 10 Jun 2013 =
* Fixed bug with ThemeRoller dropdown generation
* Fixed bug with Taxonomy searches that left blank titles in the dropdown
* Add ability to style the matching characters in the dropdown

= 2.0.2 - 1 Jun 2013 =
* Fixed bug with saving settings and no post types are selected
* Added option for disabling plugin styles
* Added filters for autocomplete content: 'search_autocomplete_modify_title', 'search_autocomplete_modify_url', 'search_autocomplete_modify_results'

= 2.0.1 - 1 Jan 2012 =
* Fixed bug with existing stylesheet paths not resolving properly

= 2.0.0 - 29 Dec 2012 =
* Complete overhaul to use the built-in WordPress AJAX methods
* Added support for custom post types
* Added support for custom taxonomies

= 1.0.9 - 28 May 2011 =
* Fixed potential SQL injection point.

= 1.0.8 - 28 May 2011 =
* Fixed issues resulting from no value entered for option fields.
* Added options for linking directly to keyword/category term pages.

= 1.0.6 - 23 Mar 2011 =
* Removed deprecated function calls

= 1.0.5 - 26 Feb 2011 =
* Database prefix adjustment

= 1.0.4 - 25 Feb 2011 =
* Directory separator fix specific to server OS

= 1.0.3 - 24 Feb 2011 =
* Directory separator fix

= 1.0.1 - 20 Feb 2011 =
* Fixed a pathing issue for the CSS

= 1.0 - 20 Feb 2011 =
* Initial submition of the plugin

== Upgrade Notice ==

= 1.0.9 - 28 May 2011 =
Fixed potential SQL injection point.

= 1.0.5 =
Database prefix adjustment

= 1.0.4 =
Directory separator fix specific to server OS

= 1.0.3 =
Directory separator fix

= 1.0.1 =
Fixed a pathing issue for the CSS

= 1.0 =
This is the first version.

== Screenshots ==

1. SearchAutocomplete theme samples
2. SearchAutocomplete settings page