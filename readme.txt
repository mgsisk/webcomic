=== Webcomic ===

Contributors: mgsisk
Donate link: http://patreon.com/mgsisk
Requires at least: 4.1
Tested up to: 4.8.2
Stable tag: 4.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: webcomics, comics, multiple comics, storylines, characters, custom post type, custom taxonomy, template tags, shortcodes, widgets, twitter, facebook, open graph

Comic publishing power for the web.

== Description ==

Webcomic provides a host of features for creating, managing, and sharing webcomics. Take control of your webcomic with multi-comic management, theme integration, dynamic navigation, and more. You can read all about Webcomic's extensive features in [The Beginner's Guide to Webcomic](http://github.com/mgsisk/webcomic/wiki).

== Installation ==

You can install Webcomic from the **Plugins > Add New** page in the administrative dashboard. Just do a search for `webcomic` and the first result should be the one you're looking for.

= Manual Installation =

1. Download and extract Webcomic from the [WordPress plugin directory](http://wordpress.org/extend/plugins/webcomic).
2. Upload the `webcomic` directory to your `wp-content/plugins` directory.
3. Activate Webcomic through the **Plugins > Installed Plugins** page in the administrative dashboard.

== Frequently Asked Questions ==

= How do I use Webcomic? =

Once installed and activated you can use Webcomic in one of three ways:

1. Turn on the [**Integrate** option](http://github.com/mgsisk/webcomic/wiki/Configuring#Integrate) found on the **Settings > Webcomic** page in the administrative dashboard.
2. Use a [Webcomic-ready theme](http://github.com/mgsisk/webcomic/wiki/Themes) to get the most out of Webcomic's features with minimal hassle.
3. Leverage Webcomic's new [template tags, shortcodes](http://github.com/mgsisk/webcomic/wiki/Template-Tags-and-Shortcodes), and [widgets](http://github.com/mgsisk/webcomic/wiki/Widgets) to [modify an existing theme](http://wordpress.org/extend/themes) or [build your own](http://codex.wordpress.org/Theme_Development).

= Where can I get help with Webcomic? =

- [Beginner's Guide](http://github.com/mgsisk/webcomic/wiki)
- [Support Forum](http://groups.google.com/d/forum/webcomicnu)
- [Issue Tracker](http://github.com/mgsisk/webcomic/issues)
- [Email Support](mailto:help@mgsisk.com)

== Changelog ==

You can view the complete changelog (since v3) and follow changes as they happen at [the master repository on GitHub](http://github.com/mgsisk/webcomic/commits/master).

= 2.1 (July 17, 2009) =

- Settings page updates:
	- **New** Buffer Alert. When enabled, Webcomic will send an e-mail reminder the specified number of days prior to any comic buffer running out.
	- **New** Keyboard Shortcuts. When enabled, users can quickly browser a comic series using the left and right arrow keys to see the previous or next comic. The key combinations shift+left, shift+right, and shift+down take users to the first, last, or a random comic in the series, respectively.
	- A small donate link has been added next to the Webcomic version information.
	- Slight reorganization for easier use. Save posts as drafts, Fallback Matching, and ComicPress Compatibility options have been removed.
- Library page updates:
	- **New** Screen Options. Users can now toggle displaying the collection, comments, and date columns, as well as set the number of comics listed per page.
	- **New** Grid View for Orphan Files. When Thumbnail view is selected orphaned files are now displayed in a more practical grid view.
	- **New** Bulk Actions for Orphan Files. Delete, Rename, and Generate Post functions have been merged into a new Bulk Actions dropdown for orphan files.
	- Post options now provide a Publish dropdown that allows you to save posts as drafts on a case-by-case basis. Date and time options are now hidden unless an appropriate publish option is selected.
	- Slight reorganization for easier use. Author information has been merged into the Post column and replaced with a Comments column.
- Chapters page updates:
	- "How to Use Chapters" instructions have been replaced with a link to the Chapters documentation.
- Metabox updates:
	- **New** Orphan File Selector. The metabox now provides a list of orphan files that can be easily assigned to a comic post without any associated comic file.
- Core updates:
    - **New** Embed Formats. A new 'format' parameter on the_comic_embed() allows you to specify whether embed code should be output in standard html or bbcode.
	- **New** Navigation Bookends. A new 'bookend' parameter on the comic navigation template tags allows you to define a beginning and ending static page (or post) that users will be setnt to when the click first/previous on the first comic or next/last on the last comic.
	- **New** Transcript Backups. Webcomic now saves a transcript backup when an "improved" transcript is submitted by a user that can be restored if the original transcript is preferred.
	- **New** Template tags: get_comic_buffer, the_comic_buffer, the_comic_series. the_comic_embed has a new format option which allows you to specify HTML or BBCode output. The parameter order of the chapter navigation template tags has changed, and the shortcut nav functions now accept only a single $args parameter. get_the_comic() no longer returns false when a comic file can't be found, but the 'file' property is set to false.
	- **New** Comic Buffer Widget. Allows you to display the total number of buffer comics for a particular series, or the date or date and time the buffer for a series will run out.
	- **New** Widget Updates. All widgets have been updated to use the WP_Widgets class, enabling multi-widget use on all Webcomic widgets.
	- **New** CSS Filters. Webcomic now adds comic-specific CSS classes to both the body_class and post_class template tags.
	- Webcomic now automatically performs Fallback Matching based on post dates, slugs, and custom fields as necessary. Custom date formats have been removed; the format is always YYYY-MM-DD unless an alternate format can be found from a previous version of Webcomic, ComicPress Manager, or stripShow.
	- Internal 1.x to 2 upgrade functions have been removed. Users may still be able to upgrade directly from Webcomic 1.x to 2.1, though certain settings will be reset to their defaults.
	- Contextual help links now point to the new documentation at Google Code.
	- Various other feature enhancementsa nd bug fixes.
- Includes all point release updates:
	- 2.0.1: Includes bug fix to address a Comic Archive widget bug that prevented comics from being organized by chapters.
	- 2.0.1: Includes bug fix to address an array keys errror in wc-core.php.
	- 2.0.2: Includes bug fixes for the Webcomic Add Post metabox which should address upload errors in 2.0.0 and 2.0.1
	- 2.0.3: Includes bug fixes that prevented random_comic_link() from functioning properly on certain pages
	- 2.0.3: Includes fixes for the "Property name must be a string" error
	- 2.0.4: Includes a fix that should address the Secure URL's broken image issue some useres experienced.
	- 2.0.5: Includes minor fixes to the Library page related to automatic post generation and variable names.
	- 2.0.6: Includes minor fixes for upload functions related to getting the correct filename in older versions of PHP.
	- 2.0.7: Includes minor fixes to various Library and Core functions.
	- 2.0.8: Includes a minor fix to the Library related to Fallback comics.
	- 2.0.9: Includes a fix for the category.php error experienced by some users after upgrading to WordPress 2.8
	- 2.0.10: Includes "\" fix.

= 2 (May 25, 2009) =

- Completely rewritten for the 2 release to add new features, address bugs, and optimize performance.
- Settings page updates:
	- Reorganized structure for easier configuration.
	- Automatic post generation options have been removed.
	- **New** Secure URL's option. When enabled, the URL for comic images is obscured to hide both the name and location of comic files.
	- **New** Post Drafting option. When enabled, Webcomic will save automatically generated posts as drafts instead of publishing them.
	- **New** Transcript options. You can now enable or disable user-submitted transcripts, as well as require a name and e-mail address for user submitted transcripts.
	- **New** Fallback Matching options. These options are actually the old method Webcomic used to match comic files with posts, remaining as a fallback option when a comic isn't already linked to a post. A new ID option is available.
	- **New** ComicPress Compatibility. A new option for ComicPress users testing Webcomic & InkBlot on their existing WordPress site that addresses incompatibilities between the directory structures of Webcomic 2 and ComicPress. Most users can permanently disable this option.
- Library page updates:
	- Reorganized structure for easier use.
	- Thumbnail view now works with the Orphan Comics list.
	- The Publish On options are now always visible.
	- Regenerate All Thumbnails has been removed.
	- The Edit Comic page has been removed in favor of the Webcomic post metabox.
	- **New** Fallback update option. The Library now recognizes comics being matched with a post using the Fallback Method and will update them for you.
	- **New** Bulk Actions. This replaces the Update Collection dropdown and provides actions for regenerating thumbnails, deleting comics, posts, or comics and posts, and reassigning chapters for selected comics.
	- **New** Orphan Post Generator options. The Orphan Post Generator has been completely reworked to handle almost any update schedule.
	- **New** Single Post Generator. These options allow you to generate a post for only one orphaned comic instead of doing it en masse.
- Chapters page updates:
	- Reorganized structure for easier use.
	- The Using Chapters instructions have been updated.
- Core updates:
	- Webcomic now always stores comics in subdirectories of the root comic directory, named after comic category slugs.
	- Webcomic now links comic files to their associated post, eliminating the need to scan comic directories for comic files. The old matching methods remain as the Fallback Matching options.
	- Numerous changes have been made to various Webcomic template tags to add new features, address bugs, and optimize performance.
	- **New** Multi-Comic Themes. Webcomic can now load entirely new WordPress themes based on the current comic series a user is viewing.
	- **New** Bookmark Comic widget. Allows users to save their place for a particular comic series. Supports multiple comics on the same site, using a unique ID to differentiate series to allow users to save one bookmark for each series running on your site. This widget uses the new bookmark_comic() template tag
	- **New** jQuery functions. Webcomic now includes the javascrpt necessary for certain functionsñparticularly dropdown_comics()ñto function properly.
	- **New** WordPress MU fixes. Webcomic has been updated with numerous fixes and should now properly support WordPress MU.
	- **New** Contextual Help. The contextual help menu on Webcomic pages now includes links to Webcomic documentation.
	- **New** Code Breaker. All Webcomic powered sites can now enable a super secret feature by entering a special code on their website.

= 1.9 (April 8, 2009) =

- Includes a critical bug fix that caused Series to be set as their own parent when editing them from the Chapter's page, resulting in numerous Chapter-related problems.
- Includes numerous fixes related to I18n functions (internationalization support) to correct strings that could not be correctly translated.
- random_comic() has been deprecated and will be removed in the next release. Use random_comic_link() instead.
- Various minor bug fixes and feature enhancements.

= 1.8 (March 19, 2009) =

- Includes Multi-Comic support. Webcomic can now manage any number of webcomics on a single WordPress installation, each with it's own Library and Chapter hierarchy. All Webcomic features and functions have been upgraded to fully support multiple webcomics.
- Includes User-Submittable Transcripts. Webcomic has a new option (transcript e-mail) that, when provided, allows users to submit individual comic transcripts to the e-mail address you provide.
- Includes Enhanced Chapter System. Chapters now take full advantage of the WordPress Taxonomy API, allowing for Chapter archive pages and chapter feeds.
- Includes Enhanced Template Tags. All core Webcomic template tags have been upgraded to enhance performance and enable multi-comic support.
- Includes Enhanced Navigation Options. Webcomic can now make comic images clickable next or previous comic links, limit first/back/next/last comic navigation to the current storyline (chapter or volume), and more.
- Includes new template tags: in_comic_category(), get_post_comic_category(), get_post_chapters(), single_chapter_title(), chapter_description(), chapters_nav_link(), first_chapter_link(), previous_chapter_link(), next_chapter_link(), last_chapter_link(), the_chapter_link() (replases the_chapter() and the_volume()).

= 1.7 (February 6, 2009) =

- Includes the Edit Comic option. You can now rename comics that are already associated with a post from the Library, as well as get a quick overview of what files comprise a single comic (the master file and any related thumbnail images).
- All core functions have been rewritten to improve performance and add new features. Please check the Webcomic Codex for fully updated documentation.
	- get_the_comic() now returns an array of comic related information instead of specifically formatted output (similar to get_the_chapter()).
	- get_the_chapter() can now provide both chapter and volume information (get_the_volume() has been removed; use get_the_chapter('volume') instead).
	- get_the_collection() now accepts an array argument which takes any key/value pairs that the WordPress function get_terms() will accept (see wp-includes/taxonomy.php).
- Includes new template tags get_comic_image(), the_current_chapter(), and the_current_volume().
- All plugin files now include inline documentation.
- Additional bug fixes and feature enhancements.

= 1.6 (January 7, 2009) =

- Includes Meta Box. Webcomic now adds a new meta box to the add/edit post pages, which allows you to upload a comic directly from the add/edit post page and add custom descriptions, transcripts, and filenames more easily. Many thanks to Andrew Naylor for inspiring this addition with his original modifications.
- Inclueds new permission scheme. Webcomic permissions have been updated to check for specific user capabilities instead of limiting all plugin access to site administrators. WordPress Author's now have access to a limited Comic Library and WordPress Editor's now have access to the full Comic Library and Comic Chapters.
- Includes enhanced comic library. The Comic Library now offers the option to regenerate individual comic thumbnails, delete comic posts, and now compares filenames during upload to prevent accidental overwrites (with a new option to force overwriting an existing file).
- Includes enhanced auto post. Automatic post creation is now compatible with all file name options. When enabled, new options to set (or override for the ìDateî naming option) the publish datetime for the generated comic post are available.
- Includes enhanced orphan post generation. Orphaned post generation is now compatible with all file name options. New options to set (or override for the ìDateî naming option) the publish datetime and interval (îPost every week starting January 1, 2009?, for example) for the generated comic posts are now available.
- Includes internationalization support. Webcomic now makes full use of WordPress's I18n features to allow for localization.
- The library view option is now set per-user instead of globally. If you're using the thumbnail view when you upgrade your view will initially be reset to the list view.
- Corrected a flaw in the search functions that prevented transcripts and custom descriptions from being found when searching for more than one term.
- Additional minor bug fixes and feature enhancements

= 1.5 (December 30, 2008) =

- Added Search Integration. Comic transcripts and custom descriptions are now seamlessly integrated into the WordPress search function and will be included in searches.
- Added custom column to the Media Library. This will display the custom field value of comic_filename (if custom filenames are being used).
- Minor bug fixes and feature enhancements.

= 1.4 (December 24, 2008) =

- Added Thumbnail Options. Webcomic now has an independent set of media options for generating comic thumbnails.
- Added Feed Options. You can now select the size of the comic image that appears in site feeds.
- Includes new template tag: get_the_collection.
- Most of the code base has been rewritten to improve performance, add features, and fix bugs.

= 1.3 (December 21, 2008) =

- Corrected secure filenames bug that prevented thumbnails from being retrieved.
- Corrected comic_archive() and dropdown_comics() bug that displayed post revisions, autosaves, etc.
- Added code to correctly set the total page count for Volumes.

= 1.2 (December 19, 2008) =

- Includes Automatic Post Creation. When enabled, Webcomic will attempt to create a new comic post during upload. This option is only available when using the Date name format, and comics must only have date information in their filename.
- Added Generate Missing Posts option to the Library page. Webcomic will attempt to create comic posts for orphaned comics when activated. This option is only available when using the Date name format, and comics must only have date information in their filename.
- Added a validation check to custom date names. Webcomic now checks to make sure you have (at least) a year, month, and day or week PHP date string identifier and resets to the default date format if one or more of these is missing.
- Rewrote most of the Webcomic functions to add features and improve performance.
- Includes new template tags: get_the_chapter and get_the_volume.

= 1.1 (December 11, 2008) =

- Includes Secure option for filenames. When enabled, Webcomic appends a secure hash to comic filenames during upload to prevent read-ahead and archive scraping.
- Corrected the Markdown plugin error that prevented WordPress from automatically activating Webcomic.

= 1 (December 4, 2008) =

- Initial stable, feature-complete public release.
- Includes Settings page:
	- Set the comic category.
	- Define the comic directory. The comic directory and the thumbs subdirectory (for storying comic thumbnails generated by Webcomic) are automatically created if they do not exist.
	- Set the current chapter. If set, new comic posts will be automatically assigned to the current chapter.
	- Add or remove comic images from site feeds.
	- Select Date, Title, or Custom name formats for comic filenames.
- Includes Library page:
	- See all comics with related post information.
	- Easily see which posts don't have a comic and which comics don't have a post.
	- Upload, rename, and delete comics. Webcomic will automatically generate comic thumbnails based your WordPress media settings
	- Regenerate all comic thumbnails if your media settings change.
	- Quickly assign multiple comics to volumes and chapters.
	- Choose between list or thumbnail view.
- Includes Chapters page:
	- Create, modify, and delete volunmes and chapters to organize your comic library.
	- Add unique titles and descriptions volumes and chapters.
	- See a total page count for volumes and a running page count for chapters.
- Includes new template tags for WordPress themes: comics_nav_link, comic_archive, comic_loop, dropdown_comics, first_comic_link, get_the_comic, ignore_comics, last_comic_link, next_comic_link, previous_comic_link, random_comic, recent_comics, the_chapter, the_comic, the_comic_embed, the_comic_transcript, and the_volume.
- Includes new widgets for WordPress themes: Random Comic, Recent Comics, Dropdown Comics, Comic Archive, and Recent Posts (modified to ignore comic posts).

== Upgrade Notice ==

= 4 =

Existing Webcomic users should read this before upgrading: http://github.com/mgsisk/webcomic/wiki/Upgrading

== Special Thanks ==

To [Mihari](http://katbox.net) for ongoing feedback and feature suggestions.

To everyone that continues to use and support Webcomic.