=== Webcomic ===
Stable tag: 5.0.5
Requires at least: 4.7
Tested up to: 4.9.6
Requires PHP: 7.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://mgsisk.com/#support
Contributors: mgsisk
Tags: comic, media, shortcode, webcomic, widget

Comic publishing power for the web. Turn your WordPress-powered site into a comic publishing platform with Webcomic.

== Description ==

Webcomic provides a host of features related to creating, managing, and sharing comics on the web.

= Comic Management =
Webcomic organizes comics into collections – custom post types dedicated to a specific comic series. Publish any number of distinct comic series on a single WordPress site, each with it's own unique settings. Manage comic media using WordPress' own media management features.

= Theme Integration =
Webcomic adds a new section to the Customizer that's all about integrating comics into your site's theme. The Universal integration method supports most themes, but methods for integrating with themes built specifically for Webcomic and themes built for other comic plugins are also available. If none of the standard integration methods work with your theme you can [use custom actions] to add comic features right where you want them.

= Powerful Tools =
Webcomic includes a variety of tools for working with comics and getting comics up and running on your WordPress-powered site. Use the Generator to automatically create and publish comics for any number of uploaded media items, or use the Matcher to match media items with existing comics. Use collection-specific settings to organize your comics into categories and tags, customize your comic URL's, and more.

= Unlimited Potential =
Those are some of Webcomic's standard features, but there are other components included with Webcomic that provide a variety of new settings, shortcodes, template tags, and widgets. Take your online comic publishing even further with:

- **Alert**, which adds settings for creating buffer and hiatus email alerts. Never forget an update with customizable email alerts.
- **Character**, which allows you to tag character appearances and use character-based archives.
- **Commerce**, which adds PayPal-based print selling and donation features.
- **Restrict**, which adds features for restricting access to comics based on age or user role.
- **Storyline**, which allows you to organize comics by storyline and use storyline-based archives.
- **Transcribe**, which adds comic transcription features for SEO-enhancing text alternatives to your comics.
- **Twitter**, which adds settings for updating your Twitter account status when publishing comics.

[use custom actions]: https://github.com/mgsisk/webcomic/wiki/Actions#integration-actions

== Installation ==

Install Webcomic from the [Plugins > Add New] screen by searching for `webcomic` (the first result should be the one you're looking for) or by [downloading the plugin archive][stable] and uploading it to your site.

One activated, you can choose which components to use on the Settings > Webcomic screen. If this is a new installation you should see your first comic collection, Untitled Comic, in the sidebar. You can adjust collection-specific settings on the Untitled Comic > Settings screen. Most components will add settings to this screen.

You'll want to visit the Webcomic section of the Customizer to adjust integration settings for your site theme. The Universal integration method supports most themes, but methods for integrating with themes built specifically for Webcomic and themes built for other comic plugins are also available. If none of the standard integration methods work with your theme you can [use custom actions] to add comic features right where you want them.

[stable]: https://wordpress.org/plugins/webcomic
[Plugins > Add New]: https://codex.wordpress.org/Plugins_Add_New_Screen
[use custom actions]: https://github.com/mgsisk/webcomic/wiki/Actions#integration-actions

== Frequently Asked Questions ==

= Need help? =
All administration screens related to Webcomic include [contextual help] with [support links]. If you're new to Webcomic you may want to start by reading [the User Guide].

[contextual help]: https://codex.wordpress.org/Administration_Screens#Help
[support links]: https://github.com/mgsisk/webcomic/blob/master/support.md
[the User Guide]: https://github.com/mgsisk/webcomic/wiki

= Want to help? =
If you're a friendly and knowledgable Webcomic user, please chime in and help others out on [the Support Forum], [Discord Server], or [Issue Tracker]. If you're more technically-inclined, [pull requests] are always welcome.

[the Support Forum]: https://wordpress.org/support/plugin/webcomic
[Discord Server]: https://discord.gg/TNTfzzg
[Issue Tracker]: https://github.com/mgsisk/webcomic/issues
[pull requests]: https://github.com/mgsisk/webcomic/blob/master/contributing.md

== Screenshots ==

1. Enable or disable components on the Settings > Webcomic screen.
2. Each comic collection has an array of customizable settings.
3. Components often have their own collection-specific settings.
4. Manage comics like you manage other post types.
5. Theme integration options are available in the Customizer.

== Changelog ==

[Complete changelog](https://github.com/mgsisk/webcomic/blob/master/changelog.md)

= 5.0.5 (2018-02-14) =

**Changed**
- Log viewer plugin is no longer activated by default in the test vagrant box
- Media tokens are now replaced with an empty string if no media exists

**Fixed**
- Empty Twitter status saving
- Meta box ID for Webcomic Role Restrictions
- Term object check for media states

= 5.0.0 - Phoenix Down (2018-01-21) =
> Refactored everything into a functional component-based architecture.

**Added**
- Assets for the WordPress plugin directory
- Changelog to provide a curated, chronologically ordered list of notable changes for each version
- Code of conduct for project participants
- Contributing guidelines, issue template, and pull request template for project contributors
- Details view on plugin and collection settings pages
- Development configurations for Atom, Babel, Composer, Eslint, Git, Homebrew, MarkdownLint, Node, Phan, PHP Code Sniffer, PHPMD, PHPUnit, PostCSS, Rollup, Stylelint, and Vagrant
- Support document for users

**Changed**
- Alert functionality refactored into the `alert` component
- Character functionality refactored into the `character` and `taxonomy` components
- Commerce functionality refactored into the `commerce` component
- Contextual help for all administrative screens
- Core functionality refactored into the `plugin` and `collection` components
- CSS assets refactored based on component, processed with PostCSS
- Deprecated functionality refactored into the `compat` component
- Integration is now configurable in a new Customizer section
- JavaScript assets refactored based on component, processed with Babel, Rollup, and UglifyJS
- Plugin license is now GPL-2.0+
- Plugin versions now follow Semantic Versioning
- Readme now contains more descriptive, useful information
- Restrict functionality refactored into the `restrict` component
- Storyline functionality refactored into the `storyline` and `taxonomy` components
- Transcript functionality refactored into the `transcribe` component
- Twitter functionality refactored into the `twitter` component
- User experience improved for all administrative screens
- Webcomic Attacher is now the Webcomic Matcher, lives in the Tools menu
- Webcomic Commerce is now the Webcomic IPN Log
- Webcomic Generator now lives in the Tools menu

**Deprecated**
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

**Removed**
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

**Fixed**
- Bugs found in previous releases

== Upgrade Notice ==

= 5.0.5 =
Visit https://mgsisk.com/webcomic/Upgrading-to-Webcomic-5 for important information about this version.

== Special Thanks ==

To everyone that continues to use and [support Webcomic].

[support Webcomic]: https://mgsisk.com/#support
