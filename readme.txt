=== Plugin Name ===
Contributors: zaen, neojp
Donate link: http://zaen.co/wp/splashgate
Tags: splash, splashpage
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 1.1

Caching and SEO friendly way to allow any page to be used as a splash page or splash overlay.

== Description ==

SplashGate is a splash page plugin.  It does optional full page redirection that won't mess up search engine indices, and makes permanently available a url that will always redirect to the actively splashed page.  Alternatively, splash page content can be made to appear as an overlay (using fancybox) instead of redirecting.

Splash management is centralized, so it's easy to see if a splash page is active, which page is the active splash, and to easily disable or enable new pages.  There is also a dashboard widget, recurrence timing options, and overlay sizing configuration.

== Installation ==

1. Upload `splashgate` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure SplashGate from the management page under `Settings`

== Frequently Asked Questions ==

= How do I style the splash page content? =

Templates are not provided, by design.  You can manually create a template to style your splash page however you like - by default the SplashGate will redirect homepage visitors to that page, whatever template it uses. If using the fancybox SplashGate option, it is recommended to create a custom template for that page content that will look nice in the small fancybox overlay.

= How can I test the splash page? =

There is a permanent path to the splashpage, "[wp-site-root]/splashgate/".  Even if you've already viewed the splashpage, and can no longer see it because your cookie hasn't expired, manually visiting the splashgate path will cause the active splash page to appear. 

= Will this mess up my SEO? Will it still work on a cached site? =

SplashGate uses javascript for redirection, not server side logic.  So, search engines will not be redirected, and thus will not mistakenly index your splash page content as the home page.  This also allows SplashGate to work even for sites that do a lot of caching.


== Screenshots ==

1. The SplashGate Management page

== Changelog ==

= 1.1 =
* Bug fix so SplashGate works without mod_rewrite / don't have to use clean urls
* Bug fix so SplashGate can be used on sites where Wordpress is not the root of the site.
* Bug fix various undefined variable php warnings.
* Fancybox advanced extendable options.
* Fancybox autoScale set to false by default.
* Fancybox configuration option added to admin panel, so the size can be adjusted.

== Upgrade Notice ==

= 1.1 =
Various bug fixes and minor improvements to admin panel.