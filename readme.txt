=== Webcomic ===
Contributors: mgsisk
Donate link: http://webcomicms.net/
Tags: webcomic, comic, multiple comics, storylines, chapters, library, management, themes, posts, publish, custom post type, custom taxonomy, template tags, widgets
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 3.0.5

Comic publishing power for WordPress.

== Description ==

Please see the [official Webcomic site](http://webcomicms.net/) for the users manual, video tutorials, support forum, bug reports, and feature requests. For more direct assistance, contact [&#115;&#117;&#112;&#112;&#111;&#114;&#116;&#064;&#119;&#101;&#098;&#099;&#111;&#109;&#105;&#099;&#109;&#115;&#046;&#110;&#101;&#116;](&#109;&#097;&#105;&#108;&#116;&#111;:&#115;&#117;&#112;&#112;&#111;&#114;&#116;&#064;&#119;&#101;&#098;&#099;&#111;&#109;&#105;&#099;&#109;&#115;&#046;&#110;&#101;&#116;).

= &#10030; Inkblot &amp; Archimedes Users &#10030; =

You must update your theme functions.php file and the mgs_core.php file found in the theme `/includes` directory after updating to Webcomic 3.0.5. You can download the latest versions of both Inkblot and Archimedes at [http://webcomicms.net/themes/](http://webcomicms.net/themes/).

= Upgrading from Webcomic 1 or 2? =

**Back up everything.** WordPress has a really handy export tool you can use to backup your data that we highly suggest you use before you attempt to upgrade. Upgrading directly from Webcomic 1 is *not* supported. Please upgrade to Webcomic 2 before you attempt to upgrade to Webcomic 3. You should also take note of the order of your chapters and anything else you'd really hate to lose, and watch this video:

[vimeo http://vimeo.com/12500716]

Webcomic adds a number of features related to creating, managing, and sharing webcomics. Key features include:

- **Integration:** Turn any WordPress theme into a Webcomic site with the flip of a switch. Webcomic 3's new integration option makes getting up and running easier than ever.
- **Management:** Manage webcomic posts, files, storylines, characters, print selling options, collection access, age restrictions, transcribing, buffers, and more.
- **Multiplicity:** Manage any number of collections on a single WordPress site or a network of sites.
- **Internationalization:** If Webcomic isn't already available in your language you can translate it into any language you speak using WordPress' own internationalization functions.
- **Extensibility:** Build your own Webcomic-powered WordPress themes using a collection of new template tags, or modify the way Webcomic works using action and filter hooks.

== Installation ==

Once you've confirmed with your web host that you're working with PHP 5 and have the latest version of WordPress up and running you can install Webcomic from the *Install Plugins*  page. Just do a search for "webcomic" and the first result should be the one you're looking for.

= Manual Installation =

If you can't use the *Install Plugins* page for whatever reason, follow these steps to manually install Webcomic:

1. Download and extract Webcomic from the [WordPress plugin directory](http://wordpress.org/extend/plugins/webcomic).
2. Upload the `webcomic` directory to the `/wp-content/plugins` directory. To install Webcomic as a special multisite plugin upload the contents of the `webcomic` directory to the `/wp-content/mu-plugins` directory.
3. Activate the plugin through the *Manage Plugins* page in WordPress (not required if you've installed Webcomic in the `wp-content/mu-plugins` directory).

= Using Webcomic =

Webcomic's *Integrate* feature allows it to be used with any WordPress theme right out of the box, but to get the most out of Webcomic you may want to check out one of the [official Webcomic themes](http://webcomicms.net/support/manual/themes) or build your own theme using Webcomic's extensive selection of widgets, template tags, actions, and filters.

== Upgrade Notice ==

= 3.0.5 =
Introduces the **Character Converter** tool. Various minor bug fixes.