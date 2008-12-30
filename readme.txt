=== WebComic ===
Contributors: mgsisk
Donate link: http://maikeruon.com/wcib/
Tags: webcomic, comic, file manager, taxonomy, chapters, volumes
Requires at least: 2.5
Tested up to: 2.7
Stable tag: 1.5

WebComic makes any WordPress theme webcomic ready by adding new template tags and widgets specifically designed for publishing webcomics.

== Description ==

WebComic makes any WordPress theme webcomic ready by adding additional functionality, template tags, and widgets specifically designed for publishing webcomics.

= New in 1.5 =

* __Search Integration:__ Comic transcripts and custom comic descriptions (using the comic\_transcript and comic\_description custom fields, respectively) are now seamlessly integrated into WordPress' search functionality and will be included in searches.
* __custom column:__  The Comic Library now has a custom column, which will display the custom field value of _comic\_filename_ if custom filenames are being used.
* Minor bug fixes and feature enhancements.

= Major Features =

* __Flexible File Names:__ WebComic can find your comics using a date format of your choice (defaults to YYYY-MM-DD), post slugs, or entirely custom values defined by you using the comic_filename custom field.
* __Manage your Library:__ The Library allows you to manage your comics and their associated posts from within WordPress. View all of your posts and which comic they’re associated with, see which posts don’t have a comic (and which comics don’t have a post), and upload, rename, and delete comics through WordPress.
* __Organize your Chapters:__ The Chapters feature allows you to create volumes and chapters and assign posts to them. WebComic has a number of widgets and template tags designed to display your comics organized by volume and chapter.
* __Template Tags for WordPress Themes:__ Leverage WebComics new template tags and widgets to turn _any_ WordPress theme into a webcomic site.

WebComic also takes advantage of WordPress custom fields to provide a simple way of adding custom comic descriptions and comic transcripts.

= New Widgets =

* __Chapter Archive:__ Displays your entire comic archive, organized by volume, chapter, and page.
* __Dropdown Comics:__ Displays a dropdown menu listing all of your comics. Options include the ability to group posts by chapter or chapters by volume and to automatically number posts or chapters. This widget requires additional javascript functionality to work correctly.
* __Random Comic:__ Displays a random comic as a simple text link, an image, or a linked image.
* __Recent Comics:__ Displays a list of your recently posted comics (up to ten) as simple text links, images, or linked images.
* __Recent Posts:__ WebComic modifies the standard Recent Posts widget to ignore your comic posts.

== Installation ==

1. Upload the `webcomic` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Start using WebComic!

To take full advantage of WebComic, you'll need at least some understanding of creating or modifying WordPress themes. If this sounds scary, check out [InkBlot](http://rad.maikeruon.com/wcib/inkblot/), a highly customizable WordPress theme designed to take full advantage of all the features WebComic offers.

== Frequently Asked Questions ==

= How do I use this now? =

Please see the [WebComic](http://rad.maikeruon.com/wcib/webcomic/) section at the WebComic and InkBlot development site for complete documentation, including *Frequently Asked Questions*.

= Where can I get help with this? =

Please see the [WebComic & InkBlot Codex](http://maikeruon.com/wcib/codex/) or  [WebComic & InkBlot Support Forum](http://www.maikeruon.com/wcib/forum/viewforum.php?f=4) for assistance.

== Screenshots ==

1. Settings Page
2. Comic Library
3. Chapters Page

== Release Notes ==

= 1.5 =

* Added _Search Integration_. Comic transcripts and custom descriptions are now seamlessly integrated into the WordPress search function and will be included in searches.
* Added _custom_ column to the Media Library. This will display the custom field value of _comic\_filename_ (if custom filenames are being used).
* Minor bug fixes and feature enhancements.

= 1.4 =

* Added _Thumbnail Options_. WebComic now has an independent set of media options for generating comic thumbnails.
* Added _Feed Options_. You can now select the size of the comic image that appears in site feeds.
* Includes new template tag: get\_the\_collection.
* Most of the code base has been rewritten to improve performance, add features, and fix bugs.

= 1.3 =

* Corrected secure filenames bug that prevented thumbnails from being retrieved.
* Corrected comic\_archive() and dropdown\_comics() bug that displayed post revisions, autosaves, etc.
* Added code to correctly set the total page count for Volumes.

= 1.2 =

* Includes _Automatic Post Creation_. When enabled, WebComic will attempt to create a new comic post during upload. This option is only available when using the _Date_ name format, and comics must only have date information in their filename.
* Added _Generate Missing Posts_ option to the Library page. WebComic will attempt to create comic posts for orphaned comics when activated. This option is only available when using the _Date_ name format, and comics must only have date information in their filename.
* Added a validation check to custom date names. WebComic now checks to make sure you have (at least) a year, month, and day or week PHP date string identifier and resets to the default date format if one or more of these is missing.
* Rewrote most of the WebComic functions to add features and improve performance.
* Includes new template tags: get\_the\_chapter and get\_the\_volume.

= 1.1 =

* Includes _Secure_ option for filenames. When enabled, WebComic appends a secure hash to comic filenames during upload to prevent read-ahead and archive scraping.
* Corrected the Markdown plugin error that prevented WordPress from automatically activating WebComic.

= 1.0 =

* Initial stable, feature-complete public release.
* Includes _Settings_ page:
	* Set the comic category.
	* Define the comic directory. The comic directory and the thumbs subdirectory (for storying comic thumbnails generated by WebComic) are automatically created if they do not exist.
	* Set the current chapter. If set, new comic posts will be automatically assigned to the current chapter.
	* Add or remove comic images from site feeds.
	* Select Date, Title, or Custom name formats for comic filenames.
* Includes _Library_ page:
	* See all comics with related post information.
	* Easily see which posts don't have a comic and which comics don't have a post.
	* Upload, rename, and delete comics. WebComic will automatically generate comic thumbnails based your WordPress media settings
	* Regenerate all comic thumbnails if your media settings change.
	* Quickly assign multiple comics to volumes and chapters.
	* Choose between list or thumbnail view.
* Includes _Chapters_ page:
	* Create, modify, and delete volunmes and chapters to organize your comic library.
	* Add unique titles and descriptions volumes and chapters.
	* See a total page count for volumes and a running page count for chapters.
* Includes new template tags for WordPress themes: comics\_nav\_link, comic\_archive, comic\_loop, dropdown\_comics, first\_comic\_link, get\_the\_comic, ignore\_comics, last\_comic\_link, next\_comic\_link, previous\_comic\_link, random\_comic, recent\_comics, the\_chapter, the\_comic, the\_comic\_embed, the\_comic\_transcript, and the\_volume.
* Includes new widgets for WordPress themes: Random Comic, Recent Comics, Dropdown Comics, Comic Archive, and ecent Posts (modified to ignore comic posts).