=== Rooftop S3 Upload Setup ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: https://github.com/rooftopcms
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 4.8.1
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Rooftop admin UI for setting up S3 uploads

== Description ==

By default, we disable attachments in Rooftop until the user (or we do it for them) has specified their S3 credentials via the rooftop-s3-upload-setup UI.
Once these details are added, the add media capability is enabled and users can upload attachments to their posts.

== Installation ==

rooftop-s3-upload-setup is a Composer plugin, so you can include it in your Composer.json.

Otherwise you can install manually:

1. Upload the `rooftop-s3-upload-setup` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. There is no step 3 :-)

== Frequently Asked Questions ==

= Can this be used without Rooftop CMS? =

Yes, it's a Wordpress plugin you're welcome to use outside the context of Rooftop CMS. We haven't tested it, though.


== Changelog ==

= 1.2.1 =
* Tweak readme for packaging

= 1.2.0 =
* Update to latest s3 lib


== What's Rooftop CMS? ==

Rooftop CMS is a hosted, API-first WordPress CMS for developers and content creators. Use WordPress as your content management system, and build your website or application in the language best suited to the job.

https://www.rooftopcms.com
