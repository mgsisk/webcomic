<?php
/**
 * Automagic integration for loop_start.
 * 
 * @package Webcomic
 * @uses first_webcomic_link()
 * @uses previous_webcomic_link()
 * @uses random_webcomic_link()
 * @uses next_webcomic_link()
 * @uses last_webcomic_link()
 * @uses the_webcomic()
 * @uses the_webcomic_collection()
 * @uses the_webcomic_storylines()
 * @uses the_webcomic_characters()
 * @uses is_webcomic()
 * @uses is_webcomic_archive()
 * @uses webcomic_collection_title()
 * @uses webcomic_collection_poster()
 * @uses webcomic_collection_description()
 * @uses is_webcomic_storyline()
 * @uses webcomic_storyline_title()
 * @uses webcomic_storyline_cover()
 * @uses webcomic_storyline_description()
 * @uses is_webcomic_character()
 * @uses webcomic_character_title()
 * @uses webcomic_character_avatar()
 * @uses webcomic_character_description()
 */
?>

<?php if ( !is_feed() ) { ?>
	<style scoped>.webcomic-img img{height:auto;max-width:100%}</style>
<?php } ?>

<?php if ( is_front_page() ) { ?>
	<div data-webcomic-container="integrated" data-webcomic-shortcuts data-webcomic-gestures>
		<div class="integrated-webcomic">
			<nav class="webcomic-above"><?php first_webcomic_link(); previous_webcomic_link(); random_webcomic_link(); next_webcomic_link(); last_webcomic_link(); ?></nav><!-- .webcomic-above -->
			<div class="webcomic-img"><?php the_webcomic( 'full', 'next' ); ?></div><!-- .webcomic-img -->
			<nav class="webcomic-below"><?php first_webcomic_link(); previous_webcomic_link(); random_webcomic_link(); next_webcomic_link(); last_webcomic_link(); ?></nav><!-- .webcomic-below -->
		</div><!-- .integrated-webcomic -->
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'webcomic' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1><!-- .entry-title -->
				
				<?php if ( comments_open() and !post_password_required() ) { ?>
				<div class="comments-link">
					<?php comments_popup_link( 0, 1, '%' ); ?>
				</div><!-- .comments-link -->
				<?php } ?>
			</header><!-- .entry-header -->
			
			<div class="entry-content">
				<?php the_content(); wp_link_pages(); ?>
			</div><!-- .entry-content -->
			
			<footer class="entry-meta">
			<?php
				the_webcomic_collection();
				the_webcomic_storylines( '<span class="sep"> | </span>' );
				the_webcomic_characters( '<span class="sep"> | </span>' );
				
				if ( comments_open() ) { ?>
				<span class="sep"> | </span><span class="comments-link"><?php comments_popup_link(); ?></span>
				<?php }
				
				edit_post_link( __( 'Edit', 'webcomic' ), '<span class="edit-link">', '</span>' );
			?>
			</footer><!-- .entry-meta -->
		</article><!-- #post-<?php the_ID(); ?> -->
	</div>
<?php } elseif ( is_webcomic() ) { ?>
	<div class="integrated-webcomic">
		<nav class="webcomic-above"><?php first_webcomic_link(); previous_webcomic_link(); random_webcomic_link(); next_webcomic_link(); last_webcomic_link(); ?></nav><!-- .webcomic-above -->
		<div class="webcomic-img"><?php the_webcomic( 'full', 'next' ); ?></div><!-- .webcomic-img -->
		<nav class="webcomic-below"><?php first_webcomic_link(); previous_webcomic_link(); random_webcomic_link(); next_webcomic_link(); last_webcomic_link(); ?></nav><!-- .webcomic-below -->
	</div><!-- .integrated-webcomic -->
<?php } elseif ( is_webcomic_archive() ) { ?>
	<h2 class="webcomic-collection-title"><?php webcomic_collection_title(); ?></h2><!-- .webcomic-collection-title -->
	<div class="webcomic-img webcomic-collection-poster"><?php webcomic_collection_poster(); ?></div><!-- .webcomic-collection-poster -->
	<div class="webcomic-collection-description"><?php webcomic_collection_description(); ?></div><!-- .webcomic-collection-description -->
<?php } elseif ( is_webcomic_storyline() ) { ?>
	<h2 class="webcomic-storyline-title"><?php webcomic_storyline_title(); ?></h2><!-- .webcomic-storyline-title -->
	<div class="webcomic-img webcomic-storyline-cover"><?php webcomic_storyline_cover(); ?></div><!-- .webcomic-storyline-cover -->
	<div class="webcomic-storyline-description"><?php webcomic_storyline_description(); ?></div><!-- .webcomic-storyline-description -->
	<nav class="webcomic-storyline"><?php first_webcomic_storyline_link(); previous_webcomic_storyline_link(); random_webcomic_storyline_link(); next_webcomic_storyline_link(); last_webcomic_storyline_link(); ?></nav><!-- .webcomic-storyline -->
<?php } elseif ( is_webcomic_character() ) { ?>
	<h2 class="webcomic-character-title"><?php webcomic_character_title(); ?></h2><!-- .webcomic-character-title -->
	<div class="webcomic-img webcomic-character-avatar"><?php webcomic_character_avatar(); ?></div><!-- .webcomic-character-avatar -->
	<div class="webcomic-character-description"><?php webcomic_character_description(); ?></div><!-- .webcomic-character-description -->
	<nav class="webcomic-character"><?php first_webcomic_character_link(); previous_webcomic_character_link(); random_webcomic_character_link(); next_webcomic_character_link(); last_webcomic_character_link(); ?></nav><!-- .webcomic-character -->
<?php }