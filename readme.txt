=== NiftyFrog OG ===
Contributors: misschell
Tags: og, Twitter, Twitter Card, meta tags, Facebook, crossposting, Open Graph, crosspost, og:image
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CZA4ZWJUVVZHN
Requires at least: 3.4
Tested up to: 4.1.1
Stable tag: 0.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Places meta tags in your blog's header, so a suitable image and description show, when crossposting to Facebook or generating a Twitter Card.

== Description ==
Have you ever tried to crosspost from your blog to Facebook, only to find that Facebook stubbornly refuses to share the post with a relevant image? Or that the description is either missing, or from some other section of your page? This plugin places meta tags for Facebook and Twitter Cards in your blog's header, so that these services don't have to guess what to show as your crosspost.

Instead of a random button from your sidebar being shown as the thumbnail, the featured image from your post is specified. If this image hasn't been set, the first image in your post will be specified. If there is no image in your post, a default image, which you set in the plugin options, will be specified.

If you have written an excerpt for your post, the crosspost text is taken from this. If you haven't, it is taken from the post content.

If you set your Facebook user ID in the plugin options, a meta tag will be created that lets Facebook know you are the admin for any Facebook apps/widgets/insights for the page.

Requires WordPress 3.4 or above, and at least PHP 5

= Note =
This plugin has been written for, and tested on, single site installs, not multi-site.

== Installation ==
1. Unzip the zip file, and upload the entire folder to your plugins directory (/wp-content/plugins/).
2. Activate the NiftyFrog OG plugin in your WordPress dashboard.
3. In the dashboard, go to Settings -> NF OG Options, and enter your Facebook numeric user id, Twitter ID, and the URL to your default image.

== Frequently Asked Questions ==
= What if I don't enter my Facebook user ID? =

The user ID is used to create a fb:admins meta tag, which Facebook uses to determine who can do things like view insight information for your page. This tag will not be created until/unless you set the user ID. Other meta tags will still be created.

= What if I don't set a default image URL? =

If you don't set this URL, this plugin will point the og:image and twitter:image meta tags to an image in the content of your post or page, if one exists. If it does not, these meta tags will not be created.

= Will NiftyFrog OG work on a WordPress multisite network? =

This plugin has been developed for a single blog site, not a multisite setup.

= After installing this, why doesn't my crosspost to Facebook look different than it did before? =

If you have already posted a link to Facebook, Facebook has stored that meta information, and will continue to display links to it the same way. Refresh this information on Facebook, at https://developers.facebook.com/tools/debug/og/object/

= How long will my post description be? =

The description that will be shown when crossposting is limited to 200 characters, but may be shortened to cut off after a word, rather than in the middle of a word.

= Why isn't a Twitter Card created when I crosspost to Twitter? =

Before cards will be created for links to your pages, you will have to request approval for your website from Twitter, at https://cards-dev.twitter.com/validator

== Screenshots ==
1. Select NF OG Options in the Settings menu.
2. Only three options need to be set up.
3. Helper link or popup for finding numeric Facebook ID.

== Changelog ==
= 0.3 =
* Quotes don't break meta tags now.
= 0.2 =
* Fixed a line that made it sometimes not find post content.
= 0.1 =
* Initial version
