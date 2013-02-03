<?php
/** Contains the WebcomicHelp class.
 * 
 * @package Webcomic
 */

/** Provide contextual assistance.
 * 
 * @package Webcomic
 */
class WebcomicHelp extends Webcomic {
	/** Add contextual help information.
	 * 
	 * @param object $screen Current screen object.
	 * @uses WebcomicHelp::sidebar()
	 * @uses WebcomicHelp::media_sizes()
	 * @uses WebcomicHelp::commerce_overview()
	 * @uses WebcomicHelp::legacy_overview()
	 * @uses WebcomicHelp::attacher_overview()
	 * @uses WebcomicHelp::generator_overview()
	 * @uses WebcomicHelp::term_order_overview()
	 * @uses WebcomicHelp::settings_general()
	 * @uses WebcomicHelp::settings_collections()
	 * @uses WebcomicHelp::transcripts_overview()
	 * @uses WebcomicHelp::transcripts_screen()
	 * @uses WebcomicHelp::transcripts_actions()
	 * @uses WebcomicHelp::transcripts_bulk()
	 * @uses WebcomicHelp::edit_transcript_customize()
	 * @uses WebcomicHelp::edit_transcript_editor()
	 * @uses WebcomicHelp::edit_transcript_publish()
	 * @uses WebcomicHelp::edit_transcript_parent()
	 * @uses WebcomicHelp::webcomics_overview()
	 * @uses WebcomicHelp::webcomics_screen()
	 * @uses WebcomicHelp::webcomics_actions()
	 * @uses WebcomicHelp::webcomics_bulk()
	 * @uses WebcomicHelp::edit_webcomic_customize()
	 * @uses WebcomicHelp::edit_webcomic_media()
	 * @uses WebcomicHelp::edit_webcomic_editor()
	 * @uses WebcomicHelp::edit_webcomic_publish()
	 * @uses WebcomicHelp::edit_webcomic_commerce()
	 * @uses WebcomicHelp::edit_webcomic_transcripts()
	 * @uses WebcomicHelp::edit_webcomic_discussion()
	 * @uses WebcomicHelp::storylines_overview()
	 * @uses WebcomicHelp::storylines_adding()
	 * @uses WebcomicHelp::storylines_moving()
	 * @uses WebcomicHelp::characters_overview()
	 * @uses WebcomicHelp::characters_adding()
	 * @uses WebcomicHelp::languages_overview()
	 * @uses WebcomicHelp::languages_adding()
	 * @uses WebcomicHelp::collection_settings_general()
	 * @uses WebcomicHelp::collection_settings_transcripts()
	 * @uses WebcomicHelp::collection_settings_commerce()
	 * @uses WebcomicHelp::collection_settings_access()
	 * @uses WebcomicHelp::collection_settings_posts()
	 * @uses WebcomicHelp::collection_settings_permalinks()
	 * @uses WebcomicHelp::collection_settings_twitter()
	 */
	public function __construct( $screen ) {
		if ( 'options-media' !== $screen->id and 'page' !== $screen->id ) {
			$screen->set_help_sidebar( $this->sidebar() );
		}
		
		if ( 'page' === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'webcomic-collection',
				'title'   => __( 'Webcomic Collection', 'webcomic' ),
				'content' => $this->page_collection()
			) );
		} elseif ( 'options-media' === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'webcomic-sizes',
				'title'   => __( 'Additional Image Sizes', 'webcomic' ),
				'content' => $this->media_sizes()
			) );
		} elseif ( 'tools_page_webcomic-commerce' === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'webcomic' ),
				'content' => $this->commerce_overview()
			) );
		} elseif ( 'tools_page_webcomic-upgrader' === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'webcomic' ),
				'content' => $this->legacy_overview()
			) );
		} elseif ( 'media_page_webcomic-attacher' === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'webcomic' ),
				'content' => $this->attacher_overview()
			) );
		} elseif ( 'media_page_webcomic-generator' === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'webcomic' ),
				'content' => $this->generator_overview()
			) );
		} elseif( 'admin_page_webcomic-term-sort' === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'webcomic' ),
				'content' => $this->term_order_overview()
			) );
		} elseif ( 'settings_page_webcomic-options' === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'general-settings',
				'title'   => __( 'General Settings', 'webcomic' ),
				'content' => $this->settings_general()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'collections-list',
				'title'   => __( 'Collections', 'webcomic' ),
				'content' => $this->settings_collections()
			) );
		} elseif ( 'edit-webcomic_transcript' === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'webcomic' ),
				'content' => $this->transcripts_overview()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'screen-content',
				'title'   => __( 'Screen Content', 'webcomic' ),
				'content' => $this->transcripts_screen()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'available-actions',
				'title'   => __( 'Available Actions', 'webcomic' ),
				'content' => $this->transcripts_actions()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'bulk-actions',
				'title'   => __( 'Bulk Actions', 'webcomic' ),
				'content' => $this->transcripts_bulk()
			) );
		} else if( 'webcomic_transcript' === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'customizing-this-display',
				'title'   => __( 'Customizing This Display', 'webcomic' ),
				'content' => $this->edit_transcript_customize()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'transcript-editor',
				'title'   => __( 'Transcript Editor', 'webcomic' ),
				'content' => $this->edit_transcript_editor()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'publish-box',
				'title'   => __( 'Publish Box', 'webcomic' ),
				'content' => $this->edit_transcript_publish()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'parent-webcomic',
				'title'   => __( 'Parent Webcomic', 'webcomic' ),
				'content' => $this->edit_transcript_parent()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'transcript-authors',
				'title'   => __( 'Transcript Authors', 'webcomic' ),
				'content' => $this->edit_transcript_authors()
			) );
		} elseif ( "edit-{$screen->post_type}" === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'webcomic' ),
				'content' => $this->webcomics_overview( $screen )
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'screen-content',
				'title'   => __( 'Screen Content', 'webcomic' ),
				'content' => $this->webcomics_screen()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'available-actions',
				'title'   => __( 'Available Actions', 'webcomic' ),
				'content' => $this->webcomics_actions()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'bulk-actions',
				'title'   => __( 'Bulk Actions', 'webcomic' ),
				'content' => $this->webcomics_bulk()
			) );
		} elseif ( $screen->post_type === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'customizing-this-display',
				'title'   => __( 'Customizing This Display', 'webcomic' ),
				'content' => $this->edit_webcomic_customize()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'media-management',
				'title'   => __( 'Media Management', 'webcomic' ),
				'content' => $this->edit_webcomic_media()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'title-and-post-editor',
				'title'   => __( 'Title and Post Editor', 'webcomic' ),
				'content' => $this->edit_webcomic_editor()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'publish-box',
				'title'   => __( 'Publish Box', 'webcomic' ),
				'content' => $this->edit_webcomic_publish()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'comerce-settings',
				'title'   => __( 'Commerce Settings', 'webcomic' ),
				'content' => $this->edit_webcomic_commerce()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'transcripts-settings',
				'title'   => __( 'Transcripts Settings', 'webcomic' ),
				'content' => $this->edit_webcomic_transcripts()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'discussion-settings',
				'title'   => __( 'Discussion Settings', 'webcomic' ),
				'content' => $this->edit_webcomic_discussion()
			) );
		} elseif ( "edit-{$screen->post_type}_storyline" === $screen->id and empty( $_GET[ 'tag_ID' ] ) ) {
			$screen->add_help_tab( array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'webcomic' ),
				'content' => $this->storylines_overview( $screen )
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'adding-storylines',
				'title'   => __( 'Adding Storylines', 'webcomic' ),
				'content' => $this->storylines_adding()
			) );
		} elseif ( "edit-{$screen->post_type}_character" === $screen->id and empty( $_GET[ 'tag_ID' ] ) ) {
			$screen->add_help_tab( array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'webcomic' ),
				'content' => $this->characters_overview()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'adding-characters',
				'title'   => __( 'Adding Characters', 'webcomic' ),
				'content' => $this->characters_adding()
			) );
		} elseif ( 'edit-webcomic_language' === $screen->id and empty( $_GET[ 'tag_ID' ] ) ) {
			$screen->add_help_tab( array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'webcomic' ),
				'content' => $this->languages_overview()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'adding-languages',
				'title'   => __( 'Adding Languages', 'webcomic' ),
				'content' => $this->languages_adding()
			) );
		} elseif ( "{$screen->post_type}_page_{$screen->post_type}-options" === $screen->id ) {
			$screen->add_help_tab( array(
				'id'      => 'general-settings',
				'title'   => __( 'General Settings', 'webcomic' ),
				'content' => $this->collection_settings_general()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'transcript-settings',
				'title'   => __( 'Transcript Settings', 'webcomic' ),
				'content' => $this->collection_settings_transcripts( $screen )
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'commerce-settings',
				'title'   => __( 'Commerce Settings', 'webcomic' ),
				'content' => $this->collection_settings_commerce()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'access-settings',
				'title'   => __( 'Access Settings', 'webcomic' ),
				'content' => $this->collection_settings_access()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'post-settings',
				'title'   => __( 'Post Settings', 'webcomic' ),
				'content' => $this->collection_settings_posts()
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'permalink-settings',
				'title'   => __( 'Permalink Settings', 'webcomic' ),
				'content' => $this->collection_settings_permalinks( $screen )
			) );
			
			$screen->add_help_tab( array(
				'id'      => 'twitter-settings',
				'title'   => __( 'Twitter Settings', 'webcomic' ),
				'content' => $this->collection_settings_twitter( $screen )
			) );
		}
	}
	
	/** Return help sidebar.
	 * 
	 * @return string
	 */
	private function sidebar() {
		return sprintf( '
			<p><strong>%s</strong></p>
			<p><a href="//github.com/mgsisk/webcomic/wiki" target="_blank">%s</a></p>
			<p><a href="//vimeo.com/channels/webcomic" target="_blank">%s</a></p>
			<p><a href="//groups.google.com/d/forum/webcomicnu" target="_blank">%s</a></p>
			<p><a href="//github.com/mgsisk/webcomic/issues" target="_blank">%s</a></p>
			<hr>
			<p><a href="support@webcomic.nu">%s</a></p>',
			__( 'For more information:', 'webcomic' ),
			__( "Beginner's Guide", 'webcomic' ),
			__( 'Video Tutorials', 'webcomic' ),
			__( 'Support Forum', 'webcomic' ),
			__( 'Issue Tracker', 'webcomic' ),
			__( 'Email Support', 'webcomic' )
		);
	}
	
	/** Return webcomic attacher overview help.
	 * 
	 * @return string
	 */
	private function attacher_overview() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>
			<p>%s</p>
			<p>%s</p>
			<p>%s</p>',
			__( 'The Webcomic Attacher can assist you in matching existing media to existing Webcomic posts. First, tell Webcomic how you want to match media with posts by adjusting the settings:', 'webcomic' ),
			__( '<strong>Collection</strong> specifies the collection that Webcomic posts will be matched from.', 'webcomic' ),
			__( '<strong>Match post&hellip;</strong> specifies what post attribute should be used to match posts with media.', 'webcomic' ),
			__( '<strong>With media&hellip;</strong> specifies what media attribute should be use to match media with posts.', 'webcomic' ),
			sprintf( __( '<strong>Date Format</strong> is <a href="%s" target="_blank">the date format</a> to use when using post or media dates for comparison', 'webcomic' ), '//php.net/manual/en/function.date.php' ),
			__( '<strong>Custom Field Key</strong> is the name of the custom field to use when using a post custom field for comparison.', 'webcomic' ),
			__( "After clicking <strong>Find Matches</strong> Webcomic will compare all orphaned posts in the selected collection to all unattached media in your Library and list any matches it finds on the right. You can verify these matches and uncheck the ones you don't want to save.", 'webcomic' ),
			__( "Webcomic may find more than one match between media and posts, and will uncheck and highlight these additional matches. Media can only be attached to one post, so it's a good idea to check these additional matches to see if they're a better fit than the first match Webcomic found.", 'webcomic' ),
			__( "When you're ready click <strong>Attach Media</strong> to attach selected media to their matched post.", 'webcomic' )
		);
	}
	
	/** Return webcomic generator overview help.
	 * 
	 * @return string
	 */
	private function generator_overview() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>
			<p>%s</p>',
			__( 'The Webcomic Generator can assist you in publishing a large backlog of webcomics. The right column lists all of the images in your Media Library that are not attached to a post. Select the files you would like to publish using the checkboxes, then adjust the publish settings on the left:', 'webcomic' ),
			__( '<strong>Collection</strong> specifies the collection that all of the generated webcomics will belong to.', 'webcomic' ),
			__( '<strong>Start on&hellip;</strong> specifies the the date to begin publishing the selected images. The first selected image in the list will be published on this date.', 'webcomic' ),
			__( '<strong>Publish every&hellip;</strong> allows you to select which days of the week to publish subsequent files. The generator will work through the list from top to bottom, publishing selected files based on the days you select. You can reorder the images on the right by dragging and dropping the table rows to ensure they publish in the correct order.', 'webcomic' ),
			__( '<strong>Save posts as drafts</strong> will cause all of the webcomics created by the generator to be drafted. These posts will not appear on your site until you publish them.', 'webcomic' ),
			__( 'Webcomics created by the generator will use the image filename for the webcomic title.', 'webcomic' )
		);
	}
	
	/** Return term order overview help.
	 * 
	 * @return string
	 */
	private function term_order_overview() {
		return sprintf( '
			<p>%s</p>',
			__( 'From here you can change the sorting order of your terms. Drag and drop the terms on the right to change their order. When everything looks good click <strong>Save Changes</strong>.', 'webcomic' )
		);
	}
	
	/** Return transcript overview help.
	 * 
	 * @return string
	 */
	private function transcripts_overview() {
		return sprintf( '<p>%s</p>', __( 'This screen provides access to all of your webcomic transcripts. You can customize the display of this screen to suit your workflow.', 'webcomic' ) );
	}
	
	/** Return transcript screen help.
	 * 
	 * @return string
	 */
	private function transcripts_screen() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>',
			__( "You can customize the display of this screen's contents in a number of ways:", 'webcomic' ),
			__( 'You can hide/display columns based on your needs and decide how many transcripts to list per screen using the Screen Options tab.', 'webcomic' ),
			__( 'You can filter the list of transcripts by status using the text links in the upper left to show All, Published, Draft, Trashed, or Orphaned transcripts. The default view is to show all transcripts.', 'webcomic' ),
			__( 'You can view transcripts in a simple title list or with an excerpt. Choose the view you prefer by clicking on the icons at the top of the list on the right.', 'webcomic' ),
			__( 'You can refine the list to show only transcripts containing a specific language or from a specific month by using the dropdown menus above the transcripts list. Click the Filter button after making your selection. You also can refine the list by clicking on the transcript author or language in the transcripts list.', 'webcomic' )
		);
	}
	
	/** Return transcript actions help.
	 * 
	 * @return string
	 */
	private function transcripts_actions() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>',
			__( 'Hovering over a row in the transcripts list will display action links that allow you to manage your transcript. You can perform the following actions:', 'webcomic' ),
			__( '<strong>Edit</strong> takes you to the editing screen for that transcript. You can also reach that screen by clicking on the transcript title.', 'webcomic' ),
			__( '<strong>Quick Edit</strong> provides inline access to the metadata of your transcript, allowing you to update transcript details without leaving this screen.', 'webcomic' ),
			__( '<strong>Trash</strong> removes your transcript from this list and places it in the trash, from which you can permanently delete it.', 'webcomic' )
		);
	}
	
	/** Return transcript bulk help.
	 * 
	 * @return string
	 */
	private function transcripts_bulk() {
		return sprintf( '
			<p>%s</p>
			<p>%s</p>',
			__( 'You can also edit or move multiple transcripts to the trash at once. Select the transcripts you want to act on using the checkboxes, then select the action you want to take from the Bulk Actions menu and click Apply.', 'webcomic' ),
			__( 'When using Bulk Edit, you can change the metadata (languages, author, etc.) for all selected transcripts at once. To remove a transcript from the grouping, just click the x next to its name in the Bulk Edit area that appears.', 'webcomic' )
		);
	}
	
	/** Return transcript customize help.
	 * 
	 * @return string
	 */
	private function edit_transcript_customize() {
		return sprintf( '
			<p>%s</p>',
			__( 'The title field and the big Transcript Editing Area are fixed in place, but you can reposition all the other boxes using drag and drop, and can minimize or expand them by clicking the title bar of each box. Use the Screen Options tab to unhide more boxes (Slug, Author, Revisions) or to choose a 1- or 2-column layout for this screen.', 'webcomic' )
		);
	}
	
	/** Return transcript editor help.
	 * 
	 * @return string
	 */
	private function edit_transcript_editor() {
		return sprintf( '
			<p>%s</p>',
			__( 'Enter the text for your transcript. There are two modes of editing: Visual and HTML. Choose the mode by clicking on the appropriate tab. Visual mode gives you a WYSIWYG editor. Click the last icon in the row to get a second row of controls. The HTML mode allows you to enter raw HTML along with your transcript text. You can insert media files by clicking the icons above the transcript editor and following the directions. You can go to the distraction-free writing screen via the Fullscreen icon in Visual mode (second to last in the top row) or the Fullscreen button in HTML mode (last in the row). Once there, you can make buttons visible by hovering over the top area. Exit Fullscreen back to the regular transcript editor.', 'webcomic' )
		);
	}
	
	/** Return transcript publish help.
	 * 
	 * @return string
	 */
	private function edit_transcript_publish() {
		return sprintf( '
			<p>%s</p>',
			__( 'You can set the terms of publishing your transcript in the Publish box. For Status, Visibility, and Publish (immediately), click on the Edit link to reveal more options. Visibility includes options for password-protecting a transcript. Publish (immediately) allows you to set a future or past date and time, so you can schedule a transcript to be published in the future or backdate a transcript.', 'webcomic' )
		);
	}
	
	/** Return transcript parent help.
	 * 
	 * @return string
	 */
	private function edit_transcript_parent() {
		return sprintf( '
			<p>%s</p>',
			__( 'You can set the webcomic your transcript is related to in the Parent Webcomic box. First select the collection the webcomic belgons to, then the webcomic in that collection your transcript is related to. Any attached imagery will be loaded so you can view the webcomic as you write your transcript.', 'webcomic' )
		);
	}
	
	/** Return transcript authors help.
	 * 
	 * @return string
	 */
	private function edit_transcript_authors() {
		return sprintf( '
			<p>%s</p>',
			__( 'Identified but unregistered users that submit or improve a transcript will be listed in the Transcript Authors box. These additional authors may be displayed alongside (or instead of) the registered author to provide appropriate transcription credit on your site. Authors may be removed by checking the box next to their name and saving the transcript. Additional authors may be added by clicking the <strong>Add Author</strong> button and filling in the authors name; email, url, IP address, and transcription date are optional.', 'webcomic' )
		);
	}
	
	/** Return webcomic overview help.
	 * 
	 * @param object $screen Current screen object.
	 * @return string
	 * @uses Webcomic::$config
	 */
	private function webcomics_overview( $screen ) {
		return sprintf( '<p>%s</p>', sprintf( __( 'This screen provides access to all of your %s webcomics. You can customize the display of this screen to suit your workflow.', 'webcomic' ), esc_html( self::$config[ 'collections' ][ $screen->post_type ][ 'name' ] ) ) );
	}
	
	/** Return webcomic screen help.
	 * 
	 * @return string
	 */
	private function webcomics_screen() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>',
			__( "You can customize the display of this screen's contents in a number of ways:", 'webcomic' ),
			__( 'You can hide/display columns based on your needs and decide how many webcomics to list per screen using the Screen Options tab.', 'webcomic' ),
			__( 'You can filter the list of webcomics by status using the text links in the upper left to show All, Published, Draft, Trashed, or Orphaned webcomics. The default view is to show all webcomics.', 'webcomic' ),
			__( 'You can view webcomics in a simple title list or with an excerpt. Choose the view you prefer by clicking on the icons at the top of the list on the right.', 'webcomic' ),
			__( 'You can refine the list to show only webcomics in a particular storyline or from a specific month by using the dropdown menus above the webcomics list. Click the Filter button after making your selection. You also can refine the list by clicking on the webcomic author, storyline, or character in the webcomics list.', 'webcomic' )
		);
	}
	
	/** Return webcomic actions help.
	 * 
	 * @return string
	 */
	private function webcomics_actions() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>',
			__( 'Hovering over a row in the webcomics list will display action links that allow you to manage your webcomic. You can perform the following actions:', 'webcomic' ),
			__( '<strong>Edit</strong> takes you to the editing screen for that webcomic. You can also reach that screen by clicking on the webcomic title.', 'webcomic' ),
			__( '<strong>Quick Edit</strong> provides inline access to the metadata of your webcomic, allowing you to update webcomic details without leaving this screen.', 'webcomic' ),
			__( '<strong>Trash</strong> removes your webcomic from this list and places it in the trash, from which you can permanently delete it.', 'webcomic' ),
			__( "<strong>Preview</strong> will show you what your draft webcomic will look like if you publish it. View will take you to your live site to view the webcomic. Which link is available depends on your webcomic's status.", 'webcomic' )
		);
	}
	
	/** Return webcomic bulk help.
	 * 
	 * @return string
	 */
	private function webcomics_bulk() {
		return sprintf( '
			<p>%s</p>
			<p>%s</p>',
			__( 'You can also edit or move multiple webcomics to the trash at once. Select the webcomics you want to act on using the checkboxes, then select the action you want to take from the Bulk Actions menu and click Apply.', 'webcomic' ),
			__( 'When using Bulk Edit, you can change the metadata (storylines, author, etc.) for all selected webcomics at once. To remove a webcomic from the grouping, just click the x next to its name in the Bulk Edit area that appears.', 'webcomic' )
		);
	}
	
	/** Return webcomic customize help.
	 * 
	 * @return string
	 */
	private function edit_webcomic_customize() {
		return sprintf( '
			<p>%s</p>',
			__( 'The title field and the big Webcomic Editing Area are fixed in place, but you can reposition all the other boxes using drag and drop, and can minimize or expand them by clicking the title bar of each box. Use the Screen Options tab to unhide more boxes (Excerpt, Send Trackbacks, Custom Fields, Discussion, Slug, Author) or to choose a 1- or 2-column layout for this screen.', 'webcomic' )
		);
	}
	
	/** Return webcomic media help.
	 * 
	 * @return string
	 */
	private function edit_webcomic_media() {
		return sprintf( '
			<p>%s</p>
			<p>%s</p>',
			__( "To attach a file to your webcomic, click <strong>Add Media</strong> in the Webcomic Media box or above the webcomic editor and follow the instructions. Webcomic will automatically recognize any images uploaded to this webcomic, so as long as you've enabled the <strong>Integrate</strong> option, are using a Webcomic-ready theme, or have added Webcomic's template tags to your theme you do not need to insert the images into your post. The Webcomic Media box will show previews of all the images attached to your webcomic after the media popup has been dismissed.", 'webcomic' ),
			__( "Once one or more images have been attached to a webcomic you can view them in the <strong>Webcomic Media</strong> tab in the media popup. From here you can rearrange the images to change the order Webcomic will display them in, regenerate the alternate image sizes (useful if you've updated the dimensions for your thumbnail, medium, or large images or added new alternate sizes), or detach them from the current webcomic.", 'webcomic' )
		);
	}
	
	/** Return webcomic editor help.
	 * 
	 * @return string
	 */
	private function edit_webcomic_editor() {
		return sprintf( '
			<p>%s</p>
			<p>%s</p>',
			__( "<strong>Title</strong> - Enter a title for your webcomic. After you enter a title, you'll see the permalink below, which you can edit.", 'webcomic' ),
			__( '<strong>Post Editor</strong> - Enter any accompanying text for your webcomic. There are two modes of editing: Visual and HTML. Choose the mode by clicking on the appropriate tab. Visual mode gives you a WYSIWYG editor. Click the last icon in the row to get a second row of controls. The HTML mode allows you to enter raw HTML along with your webcomic text. You can go to the distraction-free writing screen via the Fullscreen icon in Visual mode (second to last in the top row) or the Fullscreen button in HTML mode (last in the row). Once there, you can make buttons visible by hovering over the top area. Exit Fullscreen back to the regular webcomic editor.', 'webcomic' )
		);
	}
	
	/** Return webcomic publish help.
	 * 
	 * @return string
	 */
	private function edit_webcomic_publish() {
		return sprintf( '
			<p>%s</p>',
			__( '<strong>Publish</strong> - You can set the terms of publishing your webcomic in the Publish box. For Status, Visibility, and Publish (immediately), click on the Edit link to reveal more options. Visibility includes options for password-protecting a webcomic. Publish (immediately) allows you to set a future or past date and time, so you can schedule a webcomic to be published in the future or backdate a webcomic.', 'webcomic' )
		);
	}
	
	/** Return webcomic commerce help.
	 * 
	 * @return string
	 */
	private function edit_webcomic_commerce() {
		return sprintf( '
			<p>%s</p>
			<p>%s</p>',
			__( "<strong>Sell Prints</strong> - Check this option to sell prints of this webcomic using your collection commerce settings. This option is unavailable if you haven't specified a business on the collection settings page.", 'webcomic' ),
			__( 'The table in the commerce box displays the default prices and shipping for domestic, international, and original prints, as well as the total cost of each. You can adjust these prices by setting a premium or discount, and the total will adjust to show what the new price will be when you update the webcomic. Uncheck the <strong>Original</strong> print option if the original print (for traditional media webcomics) has been sold or is otherwise unavailable.', 'webcomic' )
		);
	}
	
	/** Return webcomic transcripts help.
	 * 
	 * @return string
	 */
	private function edit_webcomic_transcripts() {
		return sprintf( '
			<p>%s</p>
			<p>%s</p>',
			__( '<strong>Allow Transcribing</strong> - Check this option to allow user transcribing of this webcomic.', 'webcomic' ),
			__( 'The list in the transcripts box displays the transcripts submitted for this webcomic, along with the transcript status, who it was submitted by, when it was submitted, what languages it contains, and a preview of the transcript. Click a transcript title to edit that transcript.', 'webcomic' )
		);
	}
	
	/** Return webcomic discussion help.
	 * 
	 * @return string
	 */
	private function edit_webcomic_discussion() {
		return sprintf( '
			<p>%s</p>
			<p>%s</p>',
			__( "<strong>Send Trackbacks</strong> - Trackbacks are a way to notify legacy blog systems that you've linked to them. Enter the URL(s) you want to send trackbacks to. If you link to other WordPress sites they'll be notified automatically using pingbacks, and this field is unnecessary.", 'webcomic' ),
			__( '<strong>Discussion</strong> - You can turn comments and pings on or off, and if there are comments on the webcomic, you can see them here and moderate them.', 'webcomic' )
		);
	}
	
	/** Return storyline overview help.
	 * 
	 * @return string
	 */
	private function storylines_overview( $screen ) {
		$taxonomy = get_taxonomy( $screen->taxonomy );
		
		return sprintf( '
			<p>%s</p>',
			sprintf( __( 'You can organize your webcomics into story arcs using <strong>storylines</strong>. Click <strong>Sort %s</strong> to reorganize storylines.', 'webcomic' ), $taxonomy->label )
		);
	}
	
	/** Return storyline adding help.
	 * 
	 * @return string
	 */
	private function storylines_adding() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>
			<p>%s</p>',
			__( "When adding a new storyline on this screen, you'll fill in the following fields:", 'webcomic' ),
			__( '<strong>Name</strong> - The name is how it appears on your site.', 'webcomic' ),
			__( '<strong>Slug</strong> - The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'webcomic' ),
			__( '<strong>Parent</strong> - Storylines can have a hierarchy. You might have a Chapter 1 storyline, and under that have children storylines for each scene within that storyline. Totally optional. To create a sub-storyline, just choose another storyline from the Parent dropdown.', 'webcomic' ),
			__( '<strong>Description</strong> - The description is not prominent by default; it may be used in various ways, however.', 'webcomic' ),
			__( '<strong>Cover</strong> - The cover is a representative image that can be displayed on your site.', 'webcomic' ),
			__( 'You can change the display of this screen using the Screen Options tab to set how many items are displayed per screen and to display/hide columns in the table.', 'webcomic' )
		);
	}
	
	/** Return characters overview help.
	 * 
	 * @return string
	 */
	private function characters_overview() {
		return sprintf( '
			<p>%s</p>',
			__( 'You can specify the characrters that appear in your webcomics using <strong>characters</strong>.', 'webcomic' )
			
		);
	}
	
	/** Return characters adding help.
	 * 
	 * @return string
	 */
	private function characters_adding() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>
			<p>%s</p>',
			__( "When adding a new character on this screen, you'll fill in the following fields:", 'webcomic' ),
			__( '<strong>Name</strong> - The name is how it appears on your site.', 'webcomic' ),
			__( '<strong>Slug</strong> - The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'webcomic' ),
			__( '<strong>Description</strong> - The description is not prominent by default; it may be used in various ways, however.', 'webcomic' ),
			__( '<strong>Avatar</strong> - The avatar is a representative image that can be displayed on your site.', 'webcomic' ),
			__( 'You can change the display of this screen using the Screen Options tab to set how many items are displayed per screen and to display/hide columns in the table.', 'webcomic' )
		);
	}
	
	/** Return languages overview help.
	 * 
	 * @return string
	 */
	private function languages_overview() {
		return sprintf( '
			<p>%s</p>',
			__( 'You can specify the language used in your webcomic transcripts using <strong>languages</strong>.', 'webcomic' )
			
		);
	}
	
	/** Return languages adding help.
	 * 
	 * @return string
	 */
	private function languages_adding() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>
			<p>%s</p>',
			__( "When adding a new language on this screen, you'll fill in the following fields:", 'webcomic' ),
			__( '<strong>Name</strong> - The name is how it appears on your site.', 'webcomic' ),
			sprintf( __( '<strong>Slug</strong> - The "slug" is the URL-friendly version of the name. It should be an appropriate <a href="%s" target="_blank">language subtag</a>.', 'webcomic' ), '//iana.org/assignments/language-subtag-registry' ),
			__( '<strong>Description</strong> - The description is not prominent by default; it may be used in various ways, however.', 'webcomic' ),
			__( 'You can change the display of this screen using the Screen Options tab to set how many items are displayed per screen and to display/hide columns in the table.', 'webcomic' )
		);
	}
	
	/** Return collection general settings help.
	 * 
	 * @return string
	 */
	private function collection_settings_general() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>',
			__( 'These settings allow you to change some of the basic information and features related to your collection:', 'webcomic' ),
			__( '<strong>Name</strong> - The name is how it appears on your site.', 'webcomic' ),
			__( '<strong>Description</strong> - The description is not prominent by default; it may be used in various ways, however.', 'webcomic' ),
			__( '<strong>Poster</strong> - The poster is a representative image that can be displayed on your site.', 'webcomic' ),
			__( '<strong>Theme</strong> - Determines what theme will be used when a user is viewing a page related to this collection.', 'webcomic' ),
			__( '<strong>Buffers</strong> - Sends daily email reminders to the specified address starting however many days prior to the buffer expiration you specify.', 'webcomic' ),
			__( '<strong>Feeds</strong> - Integrates webcomic posts into the main syndication feed and shows a webcomic preview of the specified size in syndication feeds.', 'webcomic' )
		);
	}
	
	/** Return collection general settings help.
	 * 
	 * @return string
	 */
	private function collection_settings_transcripts() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>',
			__( 'These settings allow you to control who can submit webcomic transcripts for this collection:', 'webcomic' ),
			__( '<strong>Default</strong> - Whether to allow people to transcribe new webcomics. Transcription can also be handled on a webcomicy-by-webcomic basis. Changing this setting does not affect existing webcomics.', 'webcomic' ),
			__( '<strong>Permission</strong> - Who has permission to transcribe webcomics. Self-identified users must provide a name and valid email address.', 'webcomic' ),
			__( '<strong>Notification</strong> - Sends a notification whenever a transcript is submitted to the email you specify.', 'webcomic' ),
			__( '<strong>Languages</strong> - Specifies what languages users may submit transcripts in.', 'webcomic' )
		);
	}
	
	/** Return collection commerce settings help.
	 * 
	 * @return string
	 */
	private function collection_settings_commerce() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>',
			__( 'These settings allow you to accept print purchases and donations via PayPal:', 'webcomic' ),
			__( '<strong>Business Email</strong> - The email address associated with the PayPal account to use for transactions. All commerce-related features will be unavailable without a valid email address.', 'webcomic' ),
			__( '<strong>Prints</strong> - Enables print sales and original, traditional-media print sales for new webcomics (print sales can also be handled on a webcomic-by-webcomic basis). These options are disabled without a valid Business Email.', 'webcomic' ),
			__( '<strong>Sales</strong> -  How to handle print sales. The single item method is faster, but a shopping cart makes it easier for users to purchase multiple prints at once.', 'webcomic' ),
			__( '<strong>Prices</strong> - The default prices for domestic, international, and original (if you work in traditional media) prints. These prices can be adjusted on a webcomic-by-webcomic basis.', 'webcomic' ),
			__( '<strong>Shipping</strong> - The cost to ship domestic, international, and original prints. These prices can be adjusted on a webcomic-by-webcomic basis.', 'webcomic' ),
			__( '<strong>Donation</strong> - Set a specific amount donors must contribute when donating, or leave at zero to allow donors to specify their own donation amount.', 'webcomic' ),
			__( '<strong>Currency</strong> -  The currency your transactions will use. Note that the Brazilian Real and the Malaysian Ringgit are only supported for in-country PayPal accounts.', 'webcomic' )
		);
	}
	
	/** Return collection access settings help.
	 * 
	 * @return string
	 */
	private function collection_settings_access() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
			</ul>',
			__( 'These settings control who can view pages related to this collection:', 'webcomic' ),
			__( '<strong>Age</strong> - Requires that users be at least as many years old as you specify to view webcomics in this collection. Unverified users will be redirected to an age verification form when attempting to view webcomics in this collection.', 'webcomic' ),
			__( '<strong>Role</strong> - Requires that users be registered and logged in to view webcomics in this collection. You may optionally select one or more specific roles a user must belong to.', 'webcomic' )
		);
	}
	
	/** Return collection post settings help.
	 * 
	 * @return string
	 */
	private function collection_settings_posts() {
		return sprintf( '
			<p>%s</p>',
			__( "These settings control some of the basic features of your webcomic posts. Disabling a feature will completely remove it's associated box from the Add New Webcomic and Edit Webcomic screens. When titles are disabled Webcomic will automatically set the title of new webcomics to their post ID. Featured images require an active theme with featured image support.", 'webcomic' )
		);
	}
	
	/** Return collection permalink settings help.
	 * 
	 * @param object $screen Current screen object.
	 * @return string
	 */
	private function collection_settings_permalinks( $screen ) {
		return sprintf( '
			<p>%s</p>
			<p>%s</p>',
			sprintf( __( 'These settings affect the URL structure of the collection archive, webcomics, storylines, and characters. To the right of each input is an example URL that will update when the slug options are changed. The slug for single webcomics accepts <a href="%s" target="_blank">a number of tags</a>, including %%year%%, %%monthnum%%, %%day%%, %%hour%%, %%minute%%, %%second%%, %%post_id%%, %%author%%, and %%%s%%.', 'webcomic' ), '//github.com/mgsisk/webcomic/wiki/Permalink-Settings', "{$screen->post_type}_storyline" ),
			sprintf( __( 'Be careful when modifying permalinks: incorrect settings may result in broken pages. <a href="%s" target="_blank">Read more about permalinks at the WordPress Codex &raquo;</a>', 'webcomic' ), 'http://codex.wordpress.org/Using_Permalinks' )
		);
		/*
		
		*/
	}
	
	/** Return collection twitter settings help.
	 * 
	 * @param object $screen Current screen object.
	 * @return string
	 * @uses Webcomic::$config
	 */
	private function collection_settings_twitter( $screen ) {
		$posts = get_posts( array(
			'post_type'      => array_keys( self::$config[ 'collections' ] ),
			'posts_per_page' => 1,
			'orderby'        => 'rand'
		) );
		
		$cards = $posts ? sprintf( '
			<p>%s</p>
			<p><strong>%s</strong></p>
			<p>%s</p>
			<p><strong>%s</strong></p>',
			__( 'For the <strong>Example Summary Card URL</strong> you should use a collection archive page, like:', 'webcomic' ),
			get_post_type_archive_link( $posts[ 0 ]->post_type ),
			__( 'For the <strong>Example Photo Card URL</strong> you should use a single webcomic page, like:', 'webcomic' ),
			get_permalink( $posts[ 0 ]->ID )
		) : sprintf( '<p>%s</p>',
			__( "No published webcomics could be found in any collection. You should publish at least one webcomic before trying to opt-in to Twitter Cards. URL's to use for the <strong>Example Summary Card URL</strong> and <strong>Example Photo Card URL</strong> will be provided here once you have published at least one webcomic.", 'webcomic' )
		);
		
		return sprintf( '
			<p>%s</p>
			<p><strong>%s</strong></p>
			<p>%s</p>
			<p><strong>%s</strong></p>
			<p>%s</p>%s',
			sprintf( __( 'These settings allow you to connect a Twitter account with the collection via a Twitter Application. Anytime a new webcomic is published in this collection a status update will be made to the account you authorize in the format you specify using your Twitter Application. Visit <a href="%s" target="_blank">Twitter Developers</a> to create a new Twitter Application. The optional <strong>Callback URL</strong> setting of your Twitter Application must be set to your site URL:', 'webcomic' ), '//dev.twitter.com/apps/new' ),
			home_url(),
			sprintf( __( "Once created, go to the <strong>Settings</strong> tab for your Twitter Application and ensure that <strong>Read and Write</strong> is selected for <strong>Application Type</strong>. Then return to the <strong>Details</strong> tab and copy the <strong>Consumer Key</strong> and <strong>Consumer Secret</strong> values into their respective fields on this page. If the keys are entered correctly a <strong>Sign in with Twitter</strong> option will appear in the <strong>Authorized Account</strong> area. Please refer to the <a href='%s' target='_blank'>Beginner's Guide</a> for a complete list of tags accepted by <strong>Tweet Format</strong>.", 'webcomic' ), '//github.com/mgsisk/webcomic/wiki/Twitter-Settings' ),
			__( 'Twitter Cards', 'webcomic' ),
			sprintf( __( 'Twitter Cards do not require a Twitter Application. Webcomic automatially generates the necessary information for Twitter Cards on all Webcomic-related pages, but you must opt-in to have these cards displayed and Twitter must approve the integration. This process only needs to be done once for your entire site. Visit <a href="%s" target="_blank">Participate in Twitter Cards</a> to opt-in.', 'webcomic' ), '//dev.twitter.com/docs/cards', '//dev.twitter.com/form/participate-twitter-cards' ),
			$cards
		);
	}
	
	/** Return collection general settings help.
	 * 
	 * @return string
	 */
	private function settings_general() {
		return sprintf( '
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>',
			__( 'The settings on this page affect your entire site:', 'webcomic' ),
			__( "<strong>Integrate</strong> - Webcomic will attempt to automatically integrate basic functionality into your site. Integration may not work with certain WordPress themes and plugins.", 'webcomic' ),
			sprintf( __( '<strong>Navigate</strong> - Changes how users browse through webcomics on your site. <a href="%1$s" target="_blank">Dynamic navigation</a> will attempt to load webcomics without refreshing the page. This makes browsing significantly faster, but may not work in all situations and will affect cost per impression advertising. When <a href="%2$s" target="_blank">touch gestures</a> are enabled readers can use various touch gestures to navigate your webcomics. When <a href="%3$s" target="_blank">keyboard shortcuts</a> are enabled users can press certain button combinations to navigate through webcomics.', 'webcomic' ), '//github.com/mgsisk/webcomic/wiki/Dynamic-Navigation', '//github.com/mgsisk/webcomic/wiki/Touch-Gestures', '//github.com/mgsisk/webcomic/wiki/Keyboard-Shortcuts' ),
			__( '<strong>Uninstall</strong> - Deletes all data associated with Webcomic when the plugin is deactivated, including settings, webcomics, storylines, characters, transcripts, and languages (this cannot be undone; uploaded media will not be deleted). You may optionally choose to convert webcomics and transcripts into posts, storylines into categories, and characters and languages into tags.', 'webcomic' )
		);
	}
	
	/** Return collection settings help.
	 * 
	 * @return string
	 */
	private function settings_collections() {
		return sprintf( '
			<p>%s</p>
			<p>%s</p>',
			__( 'The Collections section provides a general overview of all of the collections on your site. To add a new collection enter a name in the box above the list and click <strong>Add Collection</strong>.', 'webcomic' ),
			__( 'To delete a collection, check the box next to it and select either <strong>Delete Permanently</strong> or <strong>Delete and Save</strong> from the Bulk Actions dropdown at the bottom of the list. If <strong>Delete and Save</strong> is selected the checked collections will have their webcomics and transcripts converted into posts, storylines converted into categories, and characters converted into tags before being deleted.', 'webcomic' )
		);
	}
	
	/** Return additional sizes help.
	 * 
	 * @return string
	 */
	private function media_sizes() {
		return sprintf( ' 
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>
			<p>%s</p>',
			__( "The Additional Image Sizes section lists the image sizes that have been added to your site beyond the WordPress defaults of thumbnail, medium, and large. To add a new size, you'll fill in the following fields at the top of the list and click <strong>Save Changes</strong>:", 'webcomic' ),
			__( '<strong>Name</strong> - How the size is identified. Should be a "slug" containing only letters, numbers, and hyphens.', 'webcomic' ),
			__( '<strong>Dimensions</strong> - The next two fields are for the maximum width and height of the new image size.', 'webcomic' ),
			__( '<strong>Crop</strong> - Whether images should be hard cropped to the specified dimensions or proportionally resized.', 'webcomic' ),
			__( 'Sizes may be adjusted by editing the width, height, and crop of the size within the list and clicking <strong>Save Changes</strong>. Sizes my be deleted by checking the box next to one or more sizes name, selecting <strong>Delete</strong> from the Bulk Actions dropdown below the list, and clicking <strong>Save Changes</strong>. Sizes added outside of this section cannot be edited, but are listed for informational purposes.', 'webcomic' )
		);
	}
	
	/** Return commerce overview help.
	 * 
	 * @return string
	 */
	private function commerce_overview() {
		global $blog_id;
		
		return sprintf( ' 
			<p>%s</p>
			<ul>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ul>
			<p>%s</p>',
			__( "The Webcomic Commerce tool displays the PayPal Instant Payment Notification (IPN) data that Webcomic has recorded, including:", 'webcomic' ),
			__( '<strong>Transaction</strong> - The transaction ID provided by PayPal.', 'webcomic' ),
			__( "<strong>Item</strong> - The item ID generated by Webcomic and sent to PayPal. Item ID's may appear as a collection ID for donations (like <strong>webcomic42</strong>) or as a post ID, collection ID, and type string for print sales (like <strong>101-webcomic42-domestic</strong>).", 'webcomic' ),
			__( '<strong>Message</strong> - A transaction message generated by Webcomic.', 'webcomic' ),
			__( '<strong>Date</strong> - The date the transaction took place. Hover over the date to see the full timestamp provided by PayPal.', 'webcomic' ),
			__( 'Webcomic records this data for informational purposes only, such as noting transaction errors (which will appear as bold red entries in the list). Similar information should be available through your PayPal account. You may remove all of the IPN data Webcomic has recorded by clicking <strong>Empty Log</strong> at the bottom of the list.', 'webcomic' )
		);
	}
	
	/** Return upgrade webcomic help.
	 * 
	 * @return string
	 */
	private function legacy_overview() {
		return sprintf( ' 
			<p>%s</p>
			<ol>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
				<li>%s</li>
			</ol>',
			sprintf( __( 'The Upgrade Webcomic tool attempts to automatically convert your existing Webcomic %1$s data to Webcomic %2$s. Depending on the size of your site the upgrade may require multiple steps. If you do not want to upgrade click <strong>Not Interested</strong> to uninstall Webcomic %2$s. If you\'re upgrading to Webcomic 4 from Webcomic 1, 2, or 3 please read the following carefully:', 'webcomic' ), self::$config[ 'legacy' ], self::$version ),
			sprintf( __( 'Upgrades are not reversible and, once begun, should not be stopped. You should <a href="%1$s">backup your site</a> before starting the <strong>irreversible</strong>, <strong>unstoppable</strong> upgrade, or click <strong>Not Interested</strong> to uninstall Webcomic %2$s.', 'webcomic' ), esc_url( admin_url( 'export.php' ) ), self::$version ),
			sprintf( __( 'Webcomic %s uses <a href="%1$s" target="_blank">the WordPress Media Library</a> for file management. All of your existing webcomic files must be imported into the media library during the upgrade. Existing files will not be moved or deleted; they will be <strong>copied</strong> into the media library. You may want to <a href="%2$s">adjust your image size settings</a> prior to upgrading.', 'webcomic' ), 'http://codex.wordpress.org/Media_Library_Screen', admin_url( 'options-media.php' ) ),
			sprintf( __( "The permalink URL's to your webcomics may change after upgrading. Permalinks <a href='%s' target=''>may be customized</a> for each of your collections once the upgrade is complete.", 'webcomic' ), '//github.com/mgsisk/webcomic/wiki/Permalink-Settings' ),
			sprintf( __( 'Webcomic 1, 2, and 3 themes are <strong>not</strong> compatible with Webcomic 4, but a selection of <a href="%1$s" target="_blank">new Webcomic-ready themes</a>. are available for Webcomic %2$s.', 'webcomic' ), 'http://webcomic.nu', self::$version ),
			sprintf( __( "Webcomic %s includes an array of new <a href='//github.com/mgsisk/webcomic/wiki/Template-Tags-and-Shortcodes' target='_blank'>template tags, shortcodes</a>, and <a href='//github.com/mgsisk/webcomic/wiki/Widgets' target='_blank'>widgets</a> that function differently from previous versions. If you have customized an existing WordPress theme or built your own using Webcomic's template tags, shortcodes, and/or widgets your theme may need to be updated.", 'webcomic' ), self::$version )
		);
	}
	
	/** Return additional page collection help.
	 * 
	 * @return string
	 */
	private function page_collection() {
		return sprintf( ' 
			<p>%s</p>',
			__( "The <strong>Webcomic Collection</strong> let's you associate your pages with a specific Webcomic Collection. Pages associated with a collection will use the theme defined in the collection settings.", 'webcomic' )
		);
	}
}