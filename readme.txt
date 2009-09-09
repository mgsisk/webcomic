=== Webcomic ===
Contributors: mgsisk
Donate link: http://maikeruon.com/webcomic/
Tags: webcomic, comic, multiple comics, inkblot, archimedes, hydrogen, silkscreen, storylines, chapters, library, management, themes, posts, publish
Requires at least: 2.8
Tested up to: 2.8.4
Stable tag: 2.1.1

Webcomic adds a collection of new features to WordPress designed specifically for publishing webcomics.

== Description ==

Webcomic adds a collection of new features to WordPress designed specifically for publishing webcomics, developing webcomic themes, and managing webcomic sites.

**Inkblot Users** - Remember to download the most recent version of Inkblot from the new [Google Code site](http://code.google.com/p/webcomic/downloads/list) and update your theme files before installing Webcomic 2.1

= 2.1.1 Updates =

- Fixed "arrar" error
- Fixed "This is a comic category" message showing up for non-comic categories on the Edit Category page.
- *New* Thanks to improvements in WordPress core and the removal of some now-unnecessary validations Chapter names no longer have to be unique across all series. You can now have chapters and volumes with the same names in the same series or different series.

= New in 2.1 =

- **Buffer Comics:** New template tags and widgets allow you to display buffer comic information for each of your comic series. Enable Buffer Alerts and Webcomic will send an e-mail to you before any buffers run out, reminding you to get back to work.
- **Transcript Backups:** Expanding on Webcomics already robust integrated transcription system are transcript backups. Whenever an "improved" transcript is submitted the previous version is saved and can be easily restored if the new transcript is anything but improved.
- **Orphan Files:** Orphan files have received a complete overhaul. Delete, rename, or generate posts individually or en masse using the new Bulk Actions dropdown, view orphans in a more functional thumbnail grid, and easily assign an orphan file to a comic post using the new orphan file selector in the Webcomic metabox.
- **Screen Options:** Take advantage of new Library screen options to adjust the comic list columns and comics per page just like other WordPress list pages.
- **Widget Upgrades:** Besides a new Buffer Comic widget all Webcomic widgets have been upgraded to use the new WP_Widget class so that they can be used any number of times throughout the site.
- **Transparent Fallback:** Dates or slugs? It doesn't matter anymore; Webcomic now intelligently and transparently matches comic files with comic posts that aren't already linked to a comic file.
- **Much More:** For a complete overview of all the changes in Webcomic 2.1 please see the changelog.

= Feature Highlights =

- **Comic Publishing Power:** Stop hacking WordPress and start using it. Webcomic's simple configuration, library management, chapter organization and new template tags for WordPress themes give you the power to take control of your comic and your site to make it exactly what you want.
- **Multi-Comic Support:** One comic not enough? Webcomic can manage any number of webcomics on a single WordPress site; just select two or more Comic Categories and Webcomic takes care of the rest. And if that weren't enough, you can load entirely separate WordPress themes for different comic series.
- **Integrated Transcribing:** Say goodbye to [ohnorobot](http://ohnorobot.com/). Webcomic's fully-integrated transcription system provides automatic indexing of published transcripts for site searches, the ability to request improvement of transcripts from users, and allow users to submit new transcripts.
- **Chapter Organization:** Take control of your storylines. Webcomic's Chapter system utilizes the WordPress Taxonomy API to provide an entirely new, independent method of organizing comic posts that doesn't interfere with post categories and tags. Use all three to create infinitely complext storylines.
- **Library Management:** Config files? Dates in filenames? Not here. Webcomic provides a simple configuration page and robust but easy-to-use comic management right through WordPress. Name your comics whatever you want, automatically generate posts for orphaned comics, and more.
- **Internationalization Support:** The world is a big place, and Webcomic understands that. It might even speak your lanuage. If it doesn't, though, that's alright. Webcomic utilizes WordPress' I18n functions for localization, allowing it to be fully translated into a language near you.
- **Much More:** For a complete overview of all the features Webcomic provides please see the [documentation](http://code.google.com/p/webcomic/wiki/Requirements).

== Installation ==

1. Upload the `webcomic` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Start using Webcomic!

To take full advantage of Webcomic, you'll need:

- Some knowledge of creating WordPress themes and working with Template Tags, or
- A WordPress theme designed to use the new Template Tags Webcomic provides

If building themes isn't your thing, check out [the official plugin website](http://maikeruon.com/webcomic/) for a collection of WordPress themes that are specifically designed to fully utilize Webcomic.

== Frequently Asked Questions ==

= Where can I get help with this? =

Please see [the Webcomic project page](http://code.google.com/p/webcomic/) at Google Code for complete documentation or the [Webcomic Google Group](http://groups.google.com/group/webcomic-discuss) for more direct assistance.

= Wouldn't it be great if...? =

All feature suggestions are taken seriously; Webcomic owes most of it's features to helpful users that have pointed out shortomings, suggested improvements, or hacked together features that eventually found their way into an official release. That doesn't mean *every* feature suggested will end up in Webcomic, but a suggestion never hurt. Feature suggestions can be posted on the [Google Group](http://groups.google.com/group/webcomic-discuss) or more formal enhancment issues can be filed at the [Google Code project page](http://code.google.com/p/webcomic/).

= Can I use Webcomic for a comic/manga/graphic novel hosting site? =

You can certainly try, but the Webcomic developer does not in any way endorse the illegal distrubution of licensed, published works and no support will be provided for such uses.

== Screenshots ==

1. Settings 
2. Library
3. Chapters
4. Metabox

== Changelog ==

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
    - **New** Embed Formats. A new 'format' parameter on the\_comic\_embed() allows you to specify whether embed code should be output in standard html or bbcode.
	- **New** Navigation Bookends. A new 'bookend' parameter on the comic navigation template tags allows you to define a beginning and ending static page (or post) that users will be setnt to when the click first/previous on the first comic or next/last on the last comic.
	- **New** Transcript Backups. Webcomic now saves a transcript backup when an "improved" transcript is submitted by a user that can be restored if the original transcript is preferred.
	- **New** Template tags: get\_comic\_buffer, the\_comic\_buffer, the\_comic\_series. the\_comic\_embed has a new format option which allows you to specify HTML or BBCode output. The parameter order of the chapter navigation template tags has changed, and the shortcut nav functions now accept only a single $args parameter. get\_the\_comic() no longer returns false when a comic file can't be found, but the 'file' property is set to false.
	- **New** Comic Buffer Widget. Allows you to display the total number of buffer comics for a particular series, or the date or date and time the buffer for a series will run out.
	- **New** Widget Updates. All widgets have been updated to use the WP\_Widgets class, enabling multi-widget use on all Webcomic widgets.
	- **New** CSS Filters. Webcomic now adds comic-specific CSS classes to both the body\_class and post\_class template tags.
	- Webcomic now automatically performs Fallback Matching based on post dates, slugs, and custom fields as necessary. Custom date formats have been removed; the format is always YYYY-MM-DD unless an alternate format can be found from a previous version of Webcomic, ComicPress Manager, or stripShow.
	- Internal 1.x to 2.0 upgrade functions have been removed. Users may still be able to upgrade directly from Webcomic 1.x to 2.1, though certain settings will be reset to their defaults.
	- Contextual help links now point to the new documentation at Google Code.
	- Various other feature enhancementsa nd bug fixes.
- Includes all point release updates:
	- 2.0.1: Includes bug fix to address a Comic Archive widget bug that prevented comics from being organized by chapters.
	- 2.0.1: Includes bug fix to address an array keys errror in wc-core.php.
	- 2.0.2: Includes bug fixes for the Webcomic Add Post metabox which should address upload errors in 2.0.0 and 2.0.1
	- 2.0.3: Includes bug fixes that prevented random\_comic\_link() from functioning properly on certain pages
	- 2.0.3: Includes fixes for the "Property name must be a string" error
	- 2.0.4: Includes a fix that should address the Secure URL's broken image issue some useres experienced.
	- 2.0.5: Includes minor fixes to the Library page related to automatic post generation and variable names.
	- 2.0.6: Includes minor fixes for upload functions related to getting the correct filename in older versions of PHP.
	- 2.0.7: Includes minor fixes to various Library and Core functions.
	- 2.0.8: Includes a minor fix to the Library related to Fallback comics.
	- 2.0.9: Includes a fix for the category.php error experienced by some users after upgrading to WordPress 2.8
	- 2.0.10: Includes "\" fix.

= 2.0 (May 25, 2009) =

- Completely rewritten for the 2.0 release to add new features, address bugs, and optimize performance.
- Settings page updates:
	- Reorganized structure for easier configuration.
	- Automatic post generation options have been removed.
	- **New** Secure URL’s option. When enabled, the URL for comic images is obscured to hide both the name and location of comic files.
	- **New** Post Drafting option. When enabled, Webcomic will save automatically generated posts as drafts instead of publishing them.
	- **New** Transcript options. You can now enable or disable user-submitted transcripts, as well as require a name and e-mail address for user submitted transcripts.
	- **New** Fallback Matching options. These options are actually the old method Webcomic used to match comic files with posts, remaining as a fallback option when a comic isn’t already linked to a post. A new ID option is available.
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
	- **New** Bookmark Comic widget. Allows users to save their place for a particular comic series. Supports multiple comics on the same site, using a unique ID to differentiate series to allow users to save one bookmark for each series running on your site. This widget uses the new bookmark\_comic() template tag
	- **New** jQuery functions. Webcomic now includes the javascrpt necessary for certain functions–particularly dropdown\_comics()–to function properly.
	- **New** WordPress MU fixes. Webcomic has been updated with numerous fixes and should now properly support WordPress MU.
	- **New** Contextual Help. The contextual help menu on Webcomic pages now includes links to Webcomic documentation.
	- **New** Code Breaker. All Webcomic powered sites can now enable a super secret feature by entering a special code on their website.

= 1.9 (April 8, 2009) =

- Includes a critical bug fix that caused Series to be set as their own parent when editing them from the Chapter’s page, resulting in numerous Chapter-related problems.
- Includes numerous fixes related to I18n functions (internationalization support) to correct strings that could not be correctly translated.
- random\_comic() has been deprecated and will be removed in the next release. Use random\_comic\_link() instead.
- Various minor bug fixes and feature enhancements.

= 1.8 (March 19, 2009) =

- Includes Multi-Comic support. Webcomic can now manage any number of webcomics on a single WordPress installation, each with it’s own Library and Chapter hierarchy. All Webcomic features and functions have been upgraded to fully support multiple webcomics.
- Includes User-Submittable Transcripts. Webcomic has a new option (transcript e-mail) that, when provided, allows users to submit individual comic transcripts to the e-mail address you provide.
- Includes Enhanced Chapter System. Chapters now take full advantage of the WordPress Taxonomy API, allowing for Chapter archive pages and chapter feeds.
- Includes Enhanced Template Tags. All core Webcomic template tags have been upgraded to enhance performance and enable multi-comic support.
- Includes Enhanced Navigation Options. Webcomic can now make comic images clickable next or previous comic links, limit first/back/next/last comic navigation to the current storyline (chapter or volume), and more.
- Includes new template tags: in\_comic\_category(), get\_post\_comic\_category(), get\_post\_chapters(), single\_chapter\_title(), chapter\_description(), chapters\_nav\_link(), first\_chapter\_link(), previous\_chapter\_link(), next\_chapter\_link(), last\_chapter\_link(), the\_chapter\_link() (replases the\_chapter() and the\_volume()).

= 1.7 (February 6, 2009) =

- Includes the Edit Comic option. You can now rename comics that are already associated with a post from the Library, as well as get a quick overview of what files comprise a single comic (the master file and any related thumbnail images).
- All core functions have been rewritten to improve performance and add new features. Please check the Webcomic Codex for fully updated documentation.
	- get\_the\_comic() now returns an array of comic related information instead of specifically formatted output (similar to get\_the\_chapter()).
	- get\_the\_chapter() can now provide both chapter and volume information (get\_the\_volume() has been removed; use get\_the\_chapter(’volume’) instead).
	- get\_the\_collection() now accepts an array argument which takes any key/value pairs that the WordPress function get\_terms() will accept (see wp-includes/taxonomy.php).
- Includes new template tags get\_comic\_image(), the\_current\_chapter(), and the\_current\_volume().
- All plugin files now include inline documentation.
- Additional bug fixes and feature enhancements.

= 1.6 (January 7, 2009) =

- Includes Meta Box. Webcomic now adds a new meta box to the add/edit post pages, which allows you to upload a comic directly from the add/edit post page and add custom descriptions, transcripts, and filenames more easily. Many thanks to Andrew Naylor for inspiring this addition with his original modifications.
- Inclueds new permission scheme. Webcomic permissions have been updated to check for specific user capabilities instead of limiting all plugin access to site administrators. WordPress Author’s now have access to a limited Comic Library and WordPress Editor’s now have access to the full Comic Library and Comic Chapters.
- Includes enhanced comic library. The Comic Library now offers the option to regenerate individual comic thumbnails, delete comic posts, and now compares filenames during upload to prevent accidental overwrites (with a new option to force overwriting an existing file).
- Includes enhanced auto post. Automatic post creation is now compatible with all file name options. When enabled, new options to set (or override for the “Date” naming option) the publish datetime for the generated comic post are available.
- Includes enhanced orphan post generation. Orphaned post generation is now compatible with all file name options. New options to set (or override for the “Date” naming option) the publish datetime and interval (”Post every week starting January 1, 2009?, for example) for the generated comic posts are now available.
- Includes internationalization support. Webcomic now makes full use of WordPress’s I18n features to allow for localization.
- The library view option is now set per-user instead of globally. If you’re using the thumbnail view when you upgrade your view will initially be reset to the list view.
- Corrected a flaw in the search functions that prevented transcripts and custom descriptions from being found when searching for more than one term.
- Additional minor bug fixes and feature enhancements

= 1.5 (December 30, 2008) =

- Added Search Integration. Comic transcripts and custom descriptions are now seamlessly integrated into the WordPress search function and will be included in searches.
- Added custom column to the Media Library. This will display the custom field value of comic\_filename (if custom filenames are being used).
- Minor bug fixes and feature enhancements.

= 1.4 (December 24, 2008) =

- Added Thumbnail Options. Webcomic now has an independent set of media options for generating comic thumbnails.
- Added Feed Options. You can now select the size of the comic image that appears in site feeds.
- Includes new template tag: get\_the\_collection.
- Most of the code base has been rewritten to improve performance, add features, and fix bugs.

= 1.3 (December 21, 2008) =

- Corrected secure filenames bug that prevented thumbnails from being retrieved.
- Corrected comic\_archive() and dropdown\_comics() bug that displayed post revisions, autosaves, etc.
- Added code to correctly set the total page count for Volumes.

= 1.2 (December 19, 2008) =

- Includes Automatic Post Creation. When enabled, Webcomic will attempt to create a new comic post during upload. This option is only available when using the Date name format, and comics must only have date information in their filename.
- Added Generate Missing Posts option to the Library page. Webcomic will attempt to create comic posts for orphaned comics when activated. This option is only available when using the Date name format, and comics must only have date information in their filename.
- Added a validation check to custom date names. Webcomic now checks to make sure you have (at least) a year, month, and day or week PHP date string identifier and resets to the default date format if one or more of these is missing.
- Rewrote most of the Webcomic functions to add features and improve performance.
- Includes new template tags: get\_the\_chapter and get\_the\_volume.

= 1.1 (December 11, 2008) =

- Includes Secure option for filenames. When enabled, Webcomic appends a secure hash to comic filenames during upload to prevent read-ahead and archive scraping.
- Corrected the Markdown plugin error that prevented WordPress from automatically activating Webcomic.

= 1.0 (December 4, 2008) =

- Initial stable, feature-complete public release.
- Includes Settings page:
	- Set the comic category.
	- Define the comic directory. The comic directory and the thumbs subdirectory (for storying comic thumbnails generated by Webcomic) are automatically created if they do not exist.
	- Set the current chapter. If set, new comic posts will be automatically assigned to the current chapter.
	- Add or remove comic images from site feeds.
	- Select Date, Title, or Custom name formats for comic filenames.
- Includes Library page:
	- See all comics with related post information.
	- Easily see which posts don’t have a comic and which comics don’t have a post.
	- Upload, rename, and delete comics. Webcomic will automatically generate comic thumbnails based your WordPress media settings
	- Regenerate all comic thumbnails if your media settings change.
	- Quickly assign multiple comics to volumes and chapters.
	- Choose between list or thumbnail view.
- Includes Chapters page:
	- Create, modify, and delete volunmes and chapters to organize your comic library.
	- Add unique titles and descriptions volumes and chapters.
	- See a total page count for volumes and a running page count for chapters.
- Includes new template tags for WordPress themes: comics\_nav\_link, comic\_archive, comic\_loop, dropdown\_comics, first\_comic\_link, get\_the\_comic, ignore\_comics, last\_comic\_link, next\_comic\_link, previous\_comic\_link, random\_comic, recent\_comics, the\_chapter, the\_comic, the\_comic\_embed, the\_comic\_transcript, and the\_volume.
- Includes new widgets for WordPress themes: Random Comic, Recent Comics, Dropdown Comics, Comic Archive, and Recent Posts (modified to ignore comic posts).

== Additional Requirements ==

Webcomic requires PHP 5.

== Special Thanks ==

To everyone that's given Webcomic a chance. I'd especially like to thank the following donors:

- shassinger
- greyliliy
- fesworks
- parasite publishing
- duncebot
- bloop
- senshuu
- nbumpercar
- lordmitz
- connected concepts

And the Webcomic & InkBlot 2 beta testers:

- greyliliy
- ravenswood
- autodmc
- gemnoc
- kez
- senshuu
- mirz