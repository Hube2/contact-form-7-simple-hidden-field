=== Hidden Field for Contact Form 7 ===
Contributors: Hube2
Tags: contact form 7 simeple hidden field
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 2.0.2
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=hube02%40earthlink%2enet&lc=US&item_name=Donation%20for%20WP%20Plugins%20I%20Use&no_note=0&cn=Add%20special%20instructions%20to%20the%20seller%3a&no_shipping=1&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add simeple hidden fields in Contact Form 7


== Description ==

Create simple hidden fields and dynamically generated hidden fields with Conact Form 7

Simple Hidden fields are just that, simple hidden fields

*** New Dynamic Hidden Fields ***

How to create dynamic hidden fields

1) Create a filter to be called from your CF7 Dynamic Select Field.

Example Filter:

`function cf7_dynamic_hidden_do_example1($value, $args=array()) {
	$value = 'new value';
	return $value;
} // end function cf7_dynamic_select_do_example1
add_filter('wpcf7_dynamic_hidden_example1', 
             'cf7_dynamic_hidden_do_example1', 10, 2);`

2) Enter the filter name and any arguments into the Filter Field when adding a Dynamic Hidden Field.
For example, if we need to supply a post_id so that the filter can get the post title
filter value entered would look something like this:

`my-filter post_id=9`

***Do Not Include any extra spaces or quotes arround values, names or the =***

You can pass any number are arguments to your filter and they will be converted into an array. For example the
following:

`my-filter post_id=101 author_id=2`

This will call the function assocaited with the filter hook 'my-filter' with an arguments the argument array of:
`$args = array(
    'post_id'   => 101,
    'author_id' => 2
)`

If the filter does not exist or your filter does not return a value then the value of the hidden field will be left empty.

[Also on GitHub](https://github.com/Hube2/contact-form-7-simple-hidden-field)

== Installation ==

1. Upload the files to the plugin folder of your site
2. Activate it from the Plugins Page


== Screenshots ==


== Frequently Asked Questions ==

= Why Filters? =

Many other plugins of this type use shortcodes. I'm not a real fan of shortcodes, but that's not the only
reason. 

Filters are much more flexible that shortcodes. 

For example, a shortcode cannot return an array. A shortcode pretty much requires that only a text value is returned.


== Changelog ==

= 2.0.2 =
* corrected to work with CF7 >= 4.8

= 2.0.1 =
* corrected fatal function call error due to update in CF7

= 2.0.0 =
* Replaced depricated CF7 function calls

= 1.2.1 =

* Corrected a bug, incorrect field type name for dynamic hidden field when creating new field, introduced in 1.2.0

= 1.2.0 =

* Updated tag page to be completely compatible w/ CF7 >= V4.2
* Backwards compatible w/ CF7 < V4.2

= 1.1.3 =

* Updated to work with CF7 V4.2 (tag pane still needs some work to make it 100% but it can be used and it will still work with previous versions of CF7)

= 1.1.2 =

* Added ability to preset value with query string

= 1.1.1 =

* Added autocomplete="off" attribute to all hidden fields

= 1.1.0 =

* Added Dynamic Hidden Field

= 1.0.0 =

* initial release