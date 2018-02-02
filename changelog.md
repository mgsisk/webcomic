# Changelog

## TODO

- Finish this change log
- Finish unit testing
- Add API usage examples to documentation
- Investigate Gutenberg support
- Investigate dynamic comic functionality
- Investigate transitioning from Comic Easel
- Investigate always-first comic option for landing pages
- Investigate setting a primary site collection, is_* rules for non-primary
  collection (per-landing page? Front page theme resolution?)

## [5.0.2] (2018-02-01)

### Changed
- PHP and WordPress dependencies now fail gracefully

### Fixed
- Overly-restrictive argument type on
  `Mgsisk\Webcomic\Restrict\hook_comic_comments()`
- v3 upgrades not using collection term details
- v1, v2, and v3 upgrades not converting storylines and characters

## [5.0.1] (2018-01-27)

### Changed
- Nginx fastcgi_read_timeout increased to 999 in test vagrant box
- PHP post_max_size increased to 999M in test vagrant box
- PHP memory_limit increased to 512M in test vagrant box
- Adminer updated to 4.4.0 in test vagrant box

### Fixed
- Double counting of saved Webcomic Matcher matches
- Recursive call to `Mgsisk\Webcomic\Compat\hook_get_post_prints_v4()`

## [5.0.0] – Phoenix Down (2018-01-21)

> Refactored everything into a functional component-based architecture.

### Added
- Assets for the WordPress plugin directory
- Changelog to provide a curated, chronologically ordered list of notable
  changes for each version
- Code of conduct for project participants
- Contributing guidelines, issue template, and pull request template for
  project contributors
- Details view on plugin and collection settings pages
- Development configurations for Atom, Babel, Composer, Eslint, Git, Homebrew,
  MarkdownLint, Node, Phan, PHP Code Sniffer, PHPMD, PHPUnit, PostCSS, Rollup,
  Stylelint, and Vagrant
- Support document for users

### Changed
- Alert functionality refactored into the `alert` component
- Character functionality refactored into the `character` and `taxonomy`
  components
- Commerce functionality refactored into the `commerce` component
- Contextual help for all administrative screens
- Core functionality refactored into the `plugin` and `collection` components
- CSS assets refactored based on component, processed with PostCSS
- Deprecated functionality refactored into the `compat` component
- Integration is now configurable in a new Customizer section
- JavaScript assets refactored based on component, processed with Babel, Rollup,
  and UglifyJS
- Plugin license is now GPL-2.0+
- Plugin versions now follow Semantic Versioning
- Readme now contains more descriptive, useful information
- Restrict functionality refactored into the `restrict` component
- Storyline functionality refactored into the `storyline` and `taxonomy`
  components
- Transcript functionality refactored into the `transcribe` component
- Twitter functionality refactored into the `twitter` component
- User experience improved for all administrative screens
- Webcomic Attacher is now the Webcomic Matcher, lives in the Tools menu
- Webcomic Commerce is now the Webcomic IPN Log
- Webcomic Generator now lives in the Tools menu
- Webcomic now requires PHP 7+
- Webcomic now requires WordPress 4.7+

### Deprecated
- `WebcomicTag` class
- All [Webcomic]-prefixed widgets
- All plugin-specific theme templates
- Custom media sizes and related features
- Data attributes for gestures and keyboard shortcuts
- The classic behavior of all shortcodes and template tags
- These shortcodes are fully deprecated:
  - `[purchase_webcomic_link]`
  - `[the_related_webcomics]`
  - `[the_webcomic_characters]`
  - `[the_webcomic_collections]`
  - `[the_webcomic_storylines]`
  - `[the_webcomic]`
  - `[verify_webcomic_age]`
  - `[verify_webcomic_role]`
  - `[webcomic_character_avatar]`
  - `[webcomic_character_cloud]`
  - `[webcomic_character_crossovers]`
  - `[webcomic_collection_cloud]`
  - `[webcomic_collection_crossovers]`
  - `[webcomic_collection_poster]`
  - `[webcomic_collection_print_amount]`
  - `[webcomic_count]`
  - `[webcomic_crossover_description]`
  - `[webcomic_crossover_poster]`
  - `[webcomic_crossover_title]`
  - `[webcomic_donation_amount]`
  - `[webcomic_donation_form]`
  - `[webcomic_dropdown_characters]`
  - `[webcomic_dropdown_collections]`
  - `[webcomic_dropdown_storylines]`
  - `[webcomic_list_characters]`
  - `[webcomic_list_collections]`
  - `[webcomic_list_storylines]`
  - `[webcomic_print_adjustment]`
  - `[webcomic_print_amount]`
  - `[webcomic_print_form]`
  - `[webcomic_storyline_cloud]`
  - `[webcomic_storyline_cover]`
  - `[webcomic_storyline_crossovers]`
  - `[webcomic_transcripts_link]`
- These template tags are fully deprecated:
  - `has_webcomic_attachments`
  - `has_webcomic_crossover`
  - `have_webcomic_transcripts`
  - `is_a_webcomic_attachment`
  - `is_webcomic_archive`
  - `is_webcomic_attachment`
  - `is_webcomic_crossover`
  - `purchase_webcomic_link`
  - `the_related_webcomics`
  - `the_verify_webcomic_age`
  - `the_webcomic_characters`
  - `the_webcomic_collections`
  - `the_webcomic_storylines`
  - `the_webcomic_transcript_authors`
  - `the_webcomic_transcript_languages`
  - `the_webcomic`
  - `verify_webcomic_age`
  - `verify_webcomic_role`
  - `webcomic_character_avatar`
  - `webcomic_character_cloud`
  - `webcomic_character_crossovers`
  - `webcomic_collection_cloud`
  - `webcomic_collection_crossovers`
  - `webcomic_collection_poster`
  - `webcomic_collection_print_amount`
  - `webcomic_count`
  - `webcomic_crossover_description`
  - `webcomic_crossover_poster`
  - `webcomic_crossover_title`
  - `webcomic_donation_amount`
  - `webcomic_donation_fields`
  - `webcomic_donation_form`
  - `webcomic_dropdown_characters`
  - `webcomic_dropdown_collections`
  - `webcomic_dropdown_storylines`
  - `webcomic_dropdown_transcript_languages`
  - `webcomic_list_characters`
  - `webcomic_list_collections`
  - `webcomic_list_storylines`
  - `webcomic_list_transcript_languages`
  - `webcomic_print_adjustment`
  - `webcomic_print_amount`
  - `webcomic_print_fields`
  - `webcomic_print_form`
  - `webcomic_prints_available`
  - `webcomic_storyline_cloud`
  - `webcomic_storyline_cover`
  - `webcomic_storyline_crossovers`
  - `webcomic_transcript_fields`
  - `webcomic_transcripts_link`
  - `webcomic_transcripts_template`

### Removed
- `<meta>` tags for Generator and OpenGraph data
- `tmhOAuth` library
- `webcomic_image` property on WP_Term objects
- `webcomic-attachment-##` class for `post_class()`
- `Webcomic` theme header
- All component classes
- Collection sorting on the Pages screen
- Conversion options and related features
- Custom media sizes and related features
- Data attributes for dynamic comic loading
- Legacy upgrade tool and related features
- Outdated media contexts for the Media administrative screen
- Outdated plugin files and assets
- Transcript language setting for collections

### Fixed
- Bugs found in previous releases

## [4.4.1] (2017-12-05)
## [4.4] (2017-12-04)
## [4.3.3] (2017-09-22)
## [4.3.2] (2015-05-01)
## [4.3.1] (2015-04-29)
## [4.3] (2015-01-22)
## [4.2] (2015-01-20)
## [4.1.1] (2015-01-19)
## [4.1.0.4] (2014-02-09)
## [4.1.0.3] (2013-12-23)
## [4.1.0.2] (2013-12-05)
## [4.1.0.1] (2013-12-05)
## [4.1] (2013-03-18)
## [4.0.9] (2013-03-03)
## [4.0.8] (2013-02-17)
## [4.0.7] (2013-02-02)
## [4.0.6] (2013-01-25)
## [4.0.5] (2013-01-24)
## [4.0.4] (2013-01-24)
## [4.0.3] (2013-01-21)
## [4.0.2] (2012-10-17)
## [4.0.1] (2012-10-06)
## [4] (2012-10-03)
## [3.0.10] (2012-03-25)
## [3.0.9] (2011-12-24)
## [3.0.8] (2011-08-16)
## [3.0.7] (2011-07-05)
## [3.0.6] (2011-05-21)
## [3.0.5] (2011-03-18)
## [3.0.4] (2010-07-04)
## [3.0.3] (2010-06-30)
## [3.0.2] (2010-06-29)
## [3.0.1] (2010-06-28)
## [3] (2010-06-27)

## [2.1.1] (2009-09-09)

- Fixed "arrar" error
- Fixed "This is a comic category" message showing up for non-comic categories
  on the Edit Category page.
- *New* Thanks to improvements in WordPress core and the removal of some
  now-unnecessary validations Chapter names no longer have to be unique across
  all series. You can now have chapters and volumes with the same names in the
  same series or different series.

## [2.1] (2009-07-19)

- Settings page updates:
  - **New** Buffer Alert. When enabled, Webcomic will send an e-mail reminder
    the specified number of days prior to any comic buffer running out.
  - **New** Keyboard Shortcuts. When enabled, users can quickly browser a comic
    series using the left and right arrow keys to see the previous or next
    comic. The key combinations shift+left, shift+right, and shift+down take
    users to the first, last, or a random comic in the series, respectively.
  - A small donate link has been added next to the Webcomic version information.
  - Slight reorganization for easier use. Save posts as drafts, Fallback
    Matching, and ComicPress Compatibility options have been removed.
- Library page updates:
  - **New** Screen Options. Users can now toggle displaying the collection,
    comments, and date columns, as well as set the number of comics listed per
    page.
  - **New** Grid View for Orphan Files. When Thumbnail view is selected
    orphaned files are now displayed in a more practical grid view.
  - **New** Bulk Actions for Orphan Files. Delete, Rename, and Generate Post
    functions have been merged into a new Bulk Actions dropdown for orphan
    files.
  - Post options now provide a Publish dropdown that allows you to save posts
    as drafts on a case-by-case basis. Date and time options are now hidden
    unless an appropriate publish option is selected.
  - Slight reorganization for easier use. Author information has been merged
    into the Post column and replaced with a Comments column.
- Chapters page updates:
  - "How to Use Chapters" instructions have been replaced with a link to the
    Chapters documentation.
- Metabox updates:
  - **New** Orphan File Selector. The metabox now provides a list of orphan
    files that can be easily assigned to a comic post without any associated
    comic file.
- Core updates:
  - **New** Embed Formats. A new 'format' parameter on the_comic_embed()
    allows you to specify whether embed code should be output in standard
    html or bbcode.
  - **New** Navigation Bookends. A new 'bookend' parameter on the comic
    navigation template tags allows you to define a beginning and ending static
    page (or post) that users will be sent to when the click first/previous on
    the first comic or next/last on the last comic.
  - **New** Transcript Backups. Webcomic now saves a transcript backup when an
    "improved" transcript is submitted by a user that can be restored if the
    original transcript is preferred.
  - **New** Template tags: get_comic_buffer, the_comic_buffer,
    the_comic_series. the_comic_embed has a new format option which allows you
    to specify HTML or BBCode output. The parameter order of the chapter
    navigation template tags has changed, and the shortcut nav functions now
    accept only a single $args parameter. get_the_comic() no longer returns
    false when a comic file can't be found, but the 'file' property is set to
    false.
  - **New** Comic Buffer Widget. Allows you to display the total number of
    buffer comics for a particular series, or the date or date and time the
    buffer for a series will run out.
  - **New** Widget Updates. All widgets have been updated to use the WP_Widgets
    class, enabling multi-widget use on all Webcomic widgets.
  - **New** CSS Filters. Webcomic now adds comic-specific CSS classes to both
    the body_class and post_class template tags.
  - Webcomic now automatically performs Fallback Matching based on post dates,
    slugs, and custom fields as necessary. Custom date formats have been
    removed; the format is always YYYY-MM-DD unless an alternate format can be
    found from a previous version of Webcomic, ComicPress Manager, or stripShow.
  - Internal 1.x to 2 upgrade functions have been removed. Users may still be
    able to upgrade directly from Webcomic 1.x to 2.1, though certain settings
    will be reset to their defaults.
  - Contextual help links now point to the new documentation at Google Code.
  - Various other feature enhancementsa nd bug fixes.

## [2.0.10] (2009-06-21)

- Includes "\" fix.

## [2.0.9] (2009-06-20)

- Includes a fix for the category.php error experienced by some users after
  upgrading to WordPress 2.8

## [2.0.8] (2009-06-15)

- Includes a minor fix to the Library related to Fallback comics.

## [2.0.7] (2009-06-13)

- Includes minor fixes to Library and Core functions.

## [2.0.6] (2009-06-10)

- Includes minor fixes upload functions related to getting the correct filename
  in older versions of PHP.

## [2.0.5] (2009-06-03)

- Includes minor fixes to the Library page related to automatic post generation
  and variable names.

## [2.0.4] (2009-06-03)

- Includes a fix that should address the Secure URL's broken image issue some
  users experienced.

## [2.0.3] (2009-05-29)

- Includes bug fixes that prevented random_comic_link() from functioning
  properly on certain pages
- Includes fixes for the "Property name must be a string" error

## [2.0.2] (2009-05-28)

- Includes bug fixes for the WebComic Add Post metabox which should address
  upload errors in 2.0.0 and 2.0.1

## [2.0.1] (2009-05-27)

- Includes a fix to address a Comic Archive widget bug that prevented comic
  organization by chapter.
- Includes bug fix to address an array keys error in wc-core.php.

## [2.0.0] (2009-05-26)

- Rewritten for the 2 release to add new features, address bugs, and optimize
  performance.
- Settings page updates:
  - Reorganized structure for easier configuration.
  - Automatic post generation options have been removed.
  - **New** Secure URL's option. When enabled, the URL for comic images is
    obscured to hide both the name and location of comic files.
  - **New** Post Drafting option. When enabled, Webcomic will save
    automatically generated posts as drafts instead of publishing them.
  - **New** Transcript options. You can now enable or disable user-submitted
    transcripts, as well as require a name and e-mail address for user
    submitted transcripts.
  - **New** Fallback Matching options. These options are actually the old
    method Webcomic used to match comic files with posts, remaining as a
    fallback option when a comic isn't already linked to a post. A new ID
    option is available.
  - **New** ComicPress Compatibility. A new option for ComicPress users testing
    Webcomic & InkBlot on their existing WordPress site that addresses
    incompatibilities between the directory structures of Webcomic 2 and
    ComicPress. Most users can permanently disable this option.
- Library page updates:
  - Reorganized structure for easier use.
  - Thumbnail view now works with the Orphan Comics list.
  - The Publish On options are now always visible.
  - Regenerate All Thumbnails has been removed.
  - The Edit Comic page has been removed in favor of the Webcomic post metabox.
  - **New** Fallback update option. The Library now recognizes comics being
    matched with a post using the Fallback Method and will update them for you.
  - **New** Bulk Actions. This replaces the Update Collection dropdown and
    provides actions for regenerating thumbnails, deleting comics, posts, or
    comics and posts, and reassigning chapters for selected comics.
  - **New** Orphan Post Generator options. The Orphan Post Generator has been
    completely reworked to handle almost any update schedule.
  - **New** Single Post Generator. These options allow you to generate a post
    for only one orphaned comic instead of doing it en masse.
- Chapters page updates:
  - Reorganized structure for easier use.
  - The Using Chapters instructions have been updated.
- Core updates:
  - Webcomic now always stores comics in subdirectories of the root comic
    directory, named after comic category slugs.
  - Webcomic now links comic files to their associated post, eliminating the
    need to scan comic directories for comic files. The old matching methods
    remain as the Fallback Matching options.
  - Numerous changes have been made to various Webcomic template tags to add
    new features, address bugs, and optimize performance.
  - **New** Multi-Comic Themes. Webcomic can now load entirely new WordPress
    themes based on the current comic series a user is viewing.
  - **New** Bookmark Comic widget. Allows users to save their place for a
    particular comic series. Supports more than on comic on the same site,
    using a unique ID to differentiate series to allow users to save one
    bookmark for each series running on your site. This widget uses the new
    bookmark_comic() template tag.
  - **New** jQuery functions. Webcomic now includes the javascrpt necessary for
    certain functions, particularly dropdown_comics() to function properly.
  - **New** WordPress MU fixes.
  - **New** Contextual Help. The contextual help menu on Webcomic pages now
    includes links to Webcomic documentation.
  - **New** Code Breaker. All Webcomic powered sites can now enable a super
    secret feature by entering a special code on their website.

## [1.9] (2009-05-08)

- Includes a critical bug fix that caused Series to become their own parent
  when editing them from the Chapter's page, resulting in a lot of
  Chapter-related problems.
- Includes fixes related to I18n functions (internationalization support) to
  correct strings that were untranslatable.
- `random_comic()` is now deprecated; use `random_comic_link()` instead.
- Other minor bug fixes and feature enhancements.

## [1.8] (2009-04-04)

- Includes Multi-Comic support. Webcomic can now manage any number of webcomics
  on a single WordPress installation, each with it's own Library and Chapter
  hierarchy. All Webcomic features and functions now fully support more than
  one webcomic.
- Includes User-Submittable Transcripts. Webcomic has a new option (transcript
  e-mail) that, when provided, allows users to submit individual comic
  transcripts to the e-mail address you provide.
- Includes Enhanced Chapter System. Chapters now take full advantage of the
  WordPress Taxonomy API, allowing for Chapter archive pages and chapter feeds.
- Includes Enhanced Template Tags. All core Webcomic template tags have
  enhanced performance and multi-comic support.
- Includes Enhanced Navigation Options. Webcomic can now make comic images
  clickable next or previous comic links, limit first/back/next/last comic
  navigation to the current storyline (chapter or volume), and more.
- Includes new template tags: in_comic_category(), get_post_comic_category(),
  get_post_chapters(), single_chapter_title(), chapter_description(),
  chapters_nav_link(), first_chapter_link(), previous_chapter_link(),
  next_chapter_link(), last_chapter_link(), the_chapter_link() (replases
  the_chapter() and the_volume()).

## [1.7] (2009-02-19)

- Includes the Edit Comic option. You can now rename comics that are already
  associated with a post from the Library, as well as get a quick overview of
  what files comprise a single comic (the master file and any related thumbnail
    images).
- All core functions have been rewritten to improve performance and add new
  features. Please check the Webcomic Codex for fully updated documentation.
  - get_the_comic() now returns an array of comic related information instead
    of specifically formatted output (like get_the_chapter()).
  - get_the_chapter() can now provide both chapter and volume information
    (deprecated get_the_volume(); use get_the_chapter('volume') instead).
  - get_the_collection() now accepts an array argument which takes any
    key/value pairs that the WordPress function get_terms() will accept (see
    wp-includes/taxonomy.php).
- Includes new template tags get_comic_image(), the_current_chapter(), and
  the_current_volume().
- All plugin files now include inline documentation.
- Other bug fixes and feature enhancements.

## [1.6] (2009-01-07)

- Includes Meta Box. Webcomic now adds a new meta box to the add/edit post
  pages, which allows you to upload a comic directly from the add/edit post
  page and add custom descriptions, transcripts, and filenames. Thanks to
  Andrew Naylor for inspiring this addition with his original modifications.
- Includes new permission scheme. Webcomic permissions now check for specific
  user capabilities instead of limiting all plugin access to site
  administrators. WordPress Author's now have access to a limited Comic Library
  and WordPress Editor's now have access to the full Comic Library and Comic
  Chapters.
- Includes enhanced comic library. The Comic Library now offers the option to
  regenerate individual comic thumbnails, delete comic posts, and now compares
  filenames during upload to prevent accidental overwrites (with a new option
    to force overwriting an existing file).
- Includes enhanced auto post. Automatic post creation is now compatible with
  all file name options. When enabled, new options to set (or override for the
  Date naming option) the publish datetime for the generated comic post are
  available.
- Includes enhanced orphan post generation. Orphaned post generation is now
  compatible with all file name options. New options to set (or override for
  the Date naming option) the publish datetime and interval (Post every week
  starting January 1, 2009?, for example) for the generated comic posts are now
  available.
- Includes internationalization support. Webcomic now makes full use of
  WordPress's I18n features to allow for localization.
- The library view option is now set per-user instead of globally. If you're
  using the thumbnail view when you upgrade your view will initially be reset
  to the list view.
- Corrected a flaw in the search functions that prevented transcripts and
  custom descriptions from showing up when searching for more than one term.
- Other minor bug fixes and feature enhancements

## [1.5] (2008-12-31)

- Added Search Integration. Comic transcripts and custom descriptions are now
  seamlessly integrated into the WordPress search function and will show up in
  searches.
- Added custom column to the Media Library. This will display the custom field
  value of comic_filename (when using custom filenames).
- Minor bug fixes and feature enhancements.

## [1.4] (2008-12-25)

- Added Thumbnail Options. Webcomic now has an independent set of media options
  for generating comic thumbnails.
- Added Feed Options. You can now select the size of the comic image that
  appears in site feeds.
- Includes new template tag: get_the_collection.
- Most of the code base has been rewritten to improve performance, add
  features, and fix bugs.

## [1.3] (2008-12-22)

- Corrected secure filenames bug that prevented thumbnail retrieval.
- Corrected comic_archive() and dropdown_comics() bug that displayed post
  revisions, autosaves, etc.
- Fixed code to set the total page count for Volumes.

## [1.2] (2008-12-19)

- Includes Automatic Post Creation. When enabled, Webcomic will attempt to
  create a new comic post during upload. This option is available when using
  the Date name format with comics that have date information (and nothing
  else) in their filename.
- Added Generate Missing Posts option to the Library page. Webcomic will
  attempt to create comic posts for orphaned comics when activated. This option
  is available when using the Date name format with comics that have date
  information (and nothing else) in their filename.
- Added a validation check to custom date names. Webcomic now checks to make
  sure you have (at least) a year, month, and day or week PHP date string
  identifier and resets to the default date format if one or more of these is
  missing.
- Rewrote most of the Webcomic functions to add features and improve
  performance.
- Includes new template tags: get_the_chapter and get_the_volume.

## [1.1] (2008-12-11)

- Includes Secure option for filenames. When enabled, Webcomic appends a secure
  hash to comic filenames during upload to prevent read-ahead and archive
  scraping.
- Corrected the Markdown plugin error that prevented WordPress from
  automatically activating Webcomic.

## [1] (2008-12-06)

> Initial public release.

## [0.1.0] (2008-12-05)

> Initial public commit.

[5.0.2]: https://github.com/mgsisk/webcomic/compare/v5.0.1...v5.0.2
[5.0.1]: https://github.com/mgsisk/webcomic/compare/v5.0.0...v5.0.1
[5.0.0]: https://github.com/mgsisk/webcomic/compare/4.4.1...v5.0.0
[4.4.1]: https://github.com/mgsisk/webcomic/compare/4.4...4.4.1
[4.4]: https://github.com/mgsisk/webcomic/compare/4.3.3...4.4
[4.3.3]: https://github.com/mgsisk/webcomic/compare/4.3.2...4.3.3
[4.3.2]: https://github.com/mgsisk/webcomic/compare/4.3.1...4.3.2
[4.3.1]: https://github.com/mgsisk/webcomic/compare/4.3...4.3.1
[4.3]: https://github.com/mgsisk/webcomic/compare/4.2...4.3
[4.2]: https://github.com/mgsisk/webcomic/compare/4.1.1...4.2
[4.1.1]: https://github.com/mgsisk/webcomic/compare/4.1.0.4...4.1.1
[4.1.0.4]: https://github.com/mgsisk/webcomic/compare/4.1.0.3...4.1.0.4
[4.1.0.3]: https://github.com/mgsisk/webcomic/compare/4.1.0.2...4.1.0.3
[4.1.0.2]: https://github.com/mgsisk/webcomic/compare/4.1.0.1...4.1.0.2
[4.1.0.1]: https://github.com/mgsisk/webcomic/compare/4.1...4.1.0.1
[4.1]: https://github.com/mgsisk/webcomic/compare/4.0.9...4.1
[4.0.9]: https://github.com/mgsisk/webcomic/compare/4.0.8...4.0.9
[4.0.8]: https://github.com/mgsisk/webcomic/compare/4.0.7...4.0.8
[4.0.7]: https://github.com/mgsisk/webcomic/compare/4.0.6...4.0.7
[4.0.6]: https://github.com/mgsisk/webcomic/compare/4.0.5...4.0.6
[4.0.5]: https://github.com/mgsisk/webcomic/compare/4.0.4...4.0.5
[4.0.4]: https://github.com/mgsisk/webcomic/compare/4.0.3...4.0.4
[4.0.3]: https://github.com/mgsisk/webcomic/compare/4.0.2...4.0.3
[4.0.2]: https://github.com/mgsisk/webcomic/compare/4.0.1...4.0.2
[4.0.1]: https://github.com/mgsisk/webcomic/compare/4...4.0.1
[4]: https://github.com/mgsisk/webcomic/compare/3.0.10...4
[3.0.10]: https://github.com/mgsisk/webcomic/compare/3.0.9...3.0.10
[3.0.9]: https://github.com/mgsisk/webcomic/compare/3.0.8...3.0.9
[3.0.8]: https://github.com/mgsisk/webcomic/compare/3.0.7...3.0.8
[3.0.7]: https://github.com/mgsisk/webcomic/compare/3.0.6...3.0.7
[3.0.6]: https://github.com/mgsisk/webcomic/compare/3.0.5...3.0.6
[3.0.5]: https://github.com/mgsisk/webcomic/compare/3.0.4...3.0.5
[3.0.4]: https://github.com/mgsisk/webcomic/compare/3.0.3...3.0.4
[3.0.3]: https://github.com/mgsisk/webcomic/compare/3.0.2...3.0.3
[3.0.2]: https://github.com/mgsisk/webcomic/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/mgsisk/webcomic/compare/3...3.0.1
[3]: https://github.com/mgsisk/webcomic/compare/2.1.1...3
[2.1.1]: https://github.com/mgsisk/webcomic/compare/2.1...2.1.1
[2.1]: https://github.com/mgsisk/webcomic/compare/2.0.10...2.1
[2.0.10]: https://github.com/mgsisk/webcomic/compare/2.0.9...2.0.10
[2.0.9]: https://github.com/mgsisk/webcomic/compare/2.0.8...2.0.9
[2.0.8]: https://github.com/mgsisk/webcomic/compare/2.0.7...2.0.8
[2.0.7]: https://github.com/mgsisk/webcomic/compare/2.0.6...2.0.7
[2.0.6]: https://github.com/mgsisk/webcomic/compare/2.0.5...2.0.6
[2.0.5]: https://github.com/mgsisk/webcomic/compare/2.0.4...2.0.5
[2.0.4]: https://github.com/mgsisk/webcomic/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/mgsisk/webcomic/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/mgsisk/webcomic/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/mgsisk/webcomic/compare/2...2.0.1
[2.0.0]: https://github.com/mgsisk/webcomic/compare/1.9...2
[1.9]: https://github.com/mgsisk/webcomic/compare/1.8...1.9
[1.8]: https://github.com/mgsisk/webcomic/compare/1.7...1.8
[1.7]: https://github.com/mgsisk/webcomic/compare/1.6...1.7
[1.6]: https://github.com/mgsisk/webcomic/compare/1.5...1.6
[1.5]: https://github.com/mgsisk/webcomic/compare/1.4...1.5
[1.4]: https://github.com/mgsisk/webcomic/compare/1.3...1.4
[1.3]: https://github.com/mgsisk/webcomic/compare/1.2...1.3
[1.2]: https://github.com/mgsisk/webcomic/compare/1.1...1.2
[1.1]: https://github.com/mgsisk/webcomic/compare/1...1.1
[1]: https://github.com/mgsisk/webcomic/compare/0.1.0...1
[0.1.0]: https://github.com/mgsisk/webcomic/tree/0.1.0
