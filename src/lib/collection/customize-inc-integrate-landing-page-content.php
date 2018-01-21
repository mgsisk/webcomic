<?php
/**
 * Comic content integration template
 *
 * Based on the contenet template from the Twenty Seventeen theme.
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'webcomic-content' ); ?>>
	<header class="entry-header">
		<?php
		$title = '<h2 class="entry-title">' . get_the_title() . '</h2>';

		if ( is_single() ) {
			$title = '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . get_the_title() . '</a></h1>';
		}

		echo $title; // WPCS: xss ok.
		?>
		<div class="entry-meta">
			<?php
			printf(
				'<span class="posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark"><time class="entry-date published" datetime="%3$s">%4$s</time></a></span>',
				esc_html__( 'Posted on', 'webcomic' ),
				esc_url( get_permalink() ),
				get_the_date( DATE_W3C ),
				get_the_date()
			);
			?>
			<span class="byline"><?php the_author_posts_link(); ?></span>
			<?php
			if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
				comments_popup_link();
			}
			?>
		</div>
	</header>
	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				// Translators: Post title.
				esc_html__( 'Continue reading%s', 'webcomic' ),
				'<span class="screen-reader-text"> ' . get_the_title() . '</span>'
			)
		);

		wp_link_pages(
			[
				'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'webcomic' ),
				'after'       => '</div>',
				'link_before' => '<span class="page-number">',
				'link_after'  => '</span>',
			]
		);
		?>
	</div>
	<footer class="entry-footer">
		<?php
		the_terms(
			get_the_ID(),
			'category',
			'<span class="cat-links">' . esc_html__( 'Categories: ', 'webcomic' ),
			esc_html__( ', ', 'webcomic' ),
			'</span>'
		);

		the_tags(
			'<span class="tags-links">' . esc_html__( 'Tags: ', 'webcomic' ),
			esc_html__( ', ', 'webcomic' ),
			'</span>'
		);

		edit_post_link(
			// Translators: Post title.
			sprintf( __( 'Edit%s', 'webcomic' ), '<span class="screen-reader-text"> ' . get_the_title() . '</span>' ),
			'<span class="edit-link">',
			'</span>'
		);
		?>
	</footer>
</article>
