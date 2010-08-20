=== Plugin Name ===
Contributors: psilva
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mail%40pedrosilva%2einfo&lc=US&item_name=Pedro%20Silva&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Tags: git, gitweb, version control, widget, code
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: trunk

Show git projects made public via a gitweb instance in Wordpress.

== Description ==

This widget displays a list of git repositories in the sidebar, given a public gitweb server.

It optionally filters out from this list repositories not belonging to a specific owner.

It can also fetch each repository's description by parsing the corresponding atom feed. 
This is discouraged for very large gitweb instances (i.e. don't try it on git.kernel.org).

== Installation ==

1. Upload `gitweb-widget.php` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the gitweb server URL, projects owner and whether to fetch project descriptions in the widgets section of the administration area

== Frequently Asked Questions ==

None yet.

== Screenshots ==

1. gitweb-widget admin panel
2. gitweb-widget sidebar listing

== Changelog ==

= 0.0.1 =
* First release

== Upgrade Notice ==

* First release
