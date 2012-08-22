/**
 * Handle updating the post list (when the collection is changed)
 * and the webcomic preview (when the post is changed).
 */
function webcomic_transcript_meta( url ) {
	jQuery( function( $ ) {
		$( '#webcomic_collection' ).attr( 'disabled', false ).on( 'change', function() {
			$.get( url, {
				collection: $( '#webcomic_collection' ).val(),
				parent: $( '#webcomic_parent' ).val(),
				webcomic_admin_ajax: 'WebcomicTranscripts::ajax_posts'
			}, function( data ) {
				$( '#webcomic_post_list' ).html( data );
				$( '#webcomic_post' ).trigger( 'change' );
			} );
		} );
		
		$( '#webcomic_author_add' ).on( 'click', function() {
			var key = ( new Date() ).getTime();
			
			$( '#webcomic_author_table tbody' ).append( '<tr><td><button class="delete">-</button></td><td><input type="text" name="webcomic_author_new[' + key + '][name]"></td><td><input type="email" name="webcomic_author_new[' + key + '][email]"></td><td><input type="url" name="webcomic_author_new[' + key + '][url]"></td><td><input type="text" name="webcomic_author_new[' + key + '][ip]"></td><td><input type="text" name="webcomic_author_new[' + key + '][time]"></td></tr>' );
			
			return false;
		} );
		
		$( document ).on( 'click', '#webcomic_author_table .delete', function() {
			$( this ).parents( 'tr' ).remove();
			
			return false;
		} );
		
		$( document ).on( 'change', '#webcomic_post', function() {
			$.get( url, {
				parent: $( '#webcomic_post' ).val() ? $( '#webcomic_post' ).val() : 0,
				webcomic_admin_ajax: 'WebcomicTranscripts::ajax_post_transcripts'
			}, function( data ) {
				$( '#webcomic_post_transcripts' ).html( data );
			} );
			
			$.get( url, {
				post: $( '#webcomic_post' ).val() ? $( '#webcomic_post' ).val() : 0,
				webcomic_admin_ajax: 'WebcomicTranscripts::ajax_preview'
			}, function( data ) {
				$( '#webcomic_post_preview' ).html( data );
			} );
		} );
	} );
}