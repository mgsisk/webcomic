=== WebComic ===
Contributors: mgsisk
Donate link: http://maikeruon.com/wcib/
Tags: webcomic, comic, file manager, taxonomy, chapters, volumes
Requires at least: 2.5
Tested up to: 2.7
Stable tag: 1.7

WebComic makes any WordPress theme webcomic ready by adding new template tags and widgets specifically designed for publishing webcomics.

== Description ==

WebComic makes any WordPress theme webcomic ready by adding additional functionality, template tags, and widgets specifically designed for publishing webcomics.

= New in 1.7 =

* Includes the _Edit Comic_ option. You can now rename comics that are already associated with a post from the Library, as well as get a quick overview of what files comprise a single comic (the master file and any related thumbnail images).
* All core functions have been rewritten to improve performance and add new features. Please check the WebComic Codex for fully updated documentation.
	* get\_the\_comic() now returns an array of comic related information instead of specifically formatted output (similar to get\_the\_chapter()).
	* get\_the\_chapter() can now provide both chapter and volume information (get\_the\_volume() has been removed; use get\_the\_chapter('volume') instead).
	* get\_the\_collection() now accepts an array argument which takes any key/value pairs that the WordPress function get\_terms() will accept (see wp-includes/taxonomy.php).
* Includes new template tags get\_comic\_image(), the\_current\_chapter(), and the\_current\_volume().
* All plugin files now include inline documentation.
* Additional bug fixes and feature enhancements.

= Major Features =

* __Flexible File Names:__ WebComic can match comic files with posts based on a custom date format, post slugs, or entirely custom values defined by you using custom fields.
* __Manage Your Library:__ The Library allows you to manage your comics and associated posts from within WordPress. Upload, rename, and delete comics, view all of your comics and which posts they're associated with, see which comics don't have a post (and which posts don't have a comic), automatically generate posts during upload or missing posts for orphaned comics, and more.
* __Organize Your Collection:__ Take advantage of WebComics Chapters feature to organize your comics into unique volumes and chapters with titles, descriptions, and page counts. WebComic has a number of functions and widgets that allow you to take full advantage of comic chapters in all kinds of ways.
* __Template Tags for WordPress Themes:__ Leverage WebComic's new template tags to turn any WordPress theme into a webcomic site.

WebComic also takes advantage of WordPress custom fields to provide a simple way of adding custom comic descriptions and comic transcripts, both of which are fully searchable using the standard WordPress search funcitons.

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

To take full advantage of WebComic, you'll need at least some understanding of creating or modifying WordPress themes. If this sounds scary, check out [InkBlot](http://maikeruon.com/wcib/about/inkblot/), a highly customizable WordPress theme designed to take full advantage of all the features WebComic offers.

== Frequently Asked Questions ==

= Where can I get help with this? =

Please see the [WebComic & InkBlot Codex](http://maikeruon.com/wcib/codex/) for full documentation or the [WebComic & InkBlot Support Forum](http://www.maikeruon.com/wcib/forum/viewforum.php?f=4) for assistance.

== Screenshots ==

1. Settings 
2. Library
3. Chapters
4. Meta Box

== Release Notes ==

= 1.7 - February 6, 2009 =

* Includes the _Edit Comic_ option. You can now rename comics that are already associated with a post from the Library, as well as get a quick overview of what files comprise a single comic (the master file and any related thumbnail images).
* All core functions have been rewritten to improve performance and add new features. Please check the WebComic Codex for fully updated documentation.
	* get\_the\_comic() now returns an array of comic related information instead of specifically formatted output (similar to get\_the\_chapter()).
	* get\_the\_chapter() can now provide both chapter and volume information (get\_the\_volume() has been removed; use get\_the\_chapter('volume') instead).
	* get\_the\_collection() now accepts an array argument which takes any key/value pairs that the WordPress function get\_terms() will accept (see wp-includes/taxonomy.php).
* Includes new template tags get\_comic\_image(), the\_current\_chapter(), and the\_current\_volume().
* All plugin files now include inline documentation.
* Additional bug fixes and feature enhancements.

= 1.6 - January 7, 2009 =

* Includes _Meta Box_. WebComic now adds a new meta box to the add/edit post pages, which allows you to upload a comic directly from the add/edit post page and add custom descriptions, transcripts, and filenames more easily. Many thanks to Andrew Naylor for inspiring this addition with his original modifications.
* Inclueds new permission scheme. WebComic permissions have been updated to check for specific user capabilities instead of limiting all plugin access to site administrators. WordPress _Author's_ now have access to a limited Comic Library and WordPress _Editor's_ now have access to the full Comic Library and Comic Chapters.
* Includes enhanced comic library. The Comic Library now offers the option to regenerate individual comic thumbnails, delete comic posts, and now compares filenames during upload to prevent accidental overwrites (with a new option to force overwriting an existing file).
* Includes enhanced auto post. Automatic post creation is now compatible with all file name options. When enabled, new options to set (or override for the "Date" naming option) the publish datetime for the generated comic post are available.
* Includes enhanced orphan post generation. Orphaned post generation is now compatible with all file name options. New options to set (or override for the "Date" naming option) the publish datetime and interval ("Post every week starting January 1, 2009", for example) for the generated comic posts are now available.
* Includes internationalization support. WebComic now makes full use of WordPress's I18n features to allow for localization.
* The library view option is now set per-user instead of globally. If you're using the thumbnail view when you upgrade your view will initially be reset to the list view.
* Corrected a flaw in the search functions that prevented transcripts and custom descriptions from being found when searching for more than one term.
* Additional minor bug fixes and feature enhancements

= 1.5 - December 30, 2008 =

* Added _Search Integration_. Comic transcripts and custom descriptions are now seamlessly integrated into the WordPress search function and will be included in searches.
* Added _custom_ column to the Media Library. This will display the custom field value of _comic\_filename_ (if custom filenames are being used).
* Minor bug fixes and feature enhancements.

= 1.4 - December 24, 2008 =

* Added _Thumbnail Options_. WebComic now has an independent set of media options for generating comic thumbnails.
* Added _Feed Options_. You can now select the size of the comic image that appears in site feeds.
* Includes new template tag: get\_the\_collection.
* Most of the code base has been rewritten to improve performance, add features, and fix bugs.

= 1.3 - December 21, 2008 =

* Corrected secure filenames bug that prevented thumbnails from being retrieved.
* Corrected comic\_archive() and dropdown\_comics() bug that displayed post revisions, autosaves, etc.
* Added code to correctly set the total page count for Volumes.

= 1.2 - December 19, 2008 =

* Includes _Automatic Post Creation_. When enabled, WebComic will attempt to create a new comic post during upload. This option is only available when using the _Date_ name format, and comics must only have date information in their filename.
* Added _Generate Missing Posts_ option to the Library page. WebComic will attempt to create comic posts for orphaned comics when activated. This option is only available when using the _Date_ name format, and comics must only have date information in their filename.
* Added a validation check to custom date names. WebComic now checks to make sure you have (at least) a year, month, and day or week PHP date string identifier and resets to the default date format if one or more of these is missing.
* Rewrote most of the WebComic functions to add features and improve performance.
* Includes new template tags: get\_the\_chapter and get\_the\_volume.

= 1.1 - December 11, 2008 =

* Includes _Secure_ option for filenames. When enabled, WebComic appends a secure hash to comic filenames during upload to prevent read-ahead and archive scraping.
* Corrected the Markdown plugin error that prevented WordPress from automatically activating WebComic.

= 1.0 - December 4, 2008 =

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