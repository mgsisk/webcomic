<?php
/**
 * Transcripts metabox row
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\MetaBox;

use function Mgsisk\Webcomic\Transcribe\hook_display_authors_column;
use function Mgsisk\Webcomic\Transcribe\hook_display_content_column;

if ( ! isset( $transcript ) ) {
	return;
}

$languages     = wp_get_object_terms( $transcript->ID, 'webcomic_transcript_language' );
$language_list = [ '&mdash;' ];

foreach ( $languages as $language ) {
	$url             = add_query_arg(
		[
			'post_type'                    => $transcript->post_type,
			'webcomic_transcript_language' => $language->slug,
		], admin_url( 'edit.php' )
	);
	$language_list[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $language->name ) . '</a>';
}

if ( 1 < count( $language_list ) ) {
	unset( $language_list[0] );
}

?>

<tr class="<?php echo esc_attr( $transcript->post_status ); ?>" data-id="<?php echo esc_attr( $transcript->ID ); ?>">
	<td class="webcomic_transcript_authors">
		<?php hook_display_authors_column( 'webcomic_transcript_authors', $transcript->ID ); ?>
	</td>
	<td class="webcomic_transcript">
		<?php hook_display_content_column( 'webcomic_transcript', $transcript->ID ); ?>
		<div class="js-error"></div>
		<div class="row-actions">
			<?php if ( current_user_can( 'edit_post', $transcript->ID ) ) : ?>
				<span><a href="<?php echo esc_url( get_edit_post_link( $transcript->ID ) ); ?>" aria-label="<?php esc_attr_e( 'Edit Transcript', 'webcomic' ); ?>"><?php esc_html_e( 'Edit', 'webcomic' ); ?></a> | </span>
				<span class="quickedit hide-if-no-js"><a href="#" class="editinline" aria-label="<?php esc_attr_e( 'Quick edit transcript inline', 'webcomic' ); ?>"><?php esc_html_e( 'Quick Edit', 'webcomic' ); ?></a> | </span>
			<?php endif; ?>

			<?php if ( current_user_can( 'delete_post', $transcript->ID ) ) : ?>
			<span class="trash"><a href="<?php echo esc_url( get_delete_post_link( $transcript->ID ) ); ?>" aria-label="<?php esc_attr_e( 'Move transcript to the Trash', 'webcomic' ); ?>"><?php esc_html_e( 'Trash', 'webcomic' ); ?></a></span>
			<?php endif; ?>
		</div>
	</td>
	<td class="webcomic_transcript_languages">
		<?php
		echo implode( ', ', $language_list ); // WPCS: xss ok.
		?>
	</td>
</tr>
