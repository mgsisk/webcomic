<?php
/**
 * @package InkBlot
 * @since 1.0
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes() ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
	<title><?php wp_title(' &bull; ', true, 'right'); bloginfo('name') ?></title>
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url') ?>" type="text/css" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url') ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />
	<?php if(is_singular()) wp_enqueue_script('comment-reply'); wp_head() ?>
</head>
<body>
<div id="wrap">
	<div id="page">
		<div id="head">
			<div class="pad">
				<div class="title"><a href="<?php echo get_option('home') ?>/" title="<?php bloginfo('description') ?>"><span><?php bloginfo('name') ?></span></a></div>
				<div class="tagline"><span><?php bloginfo('description') ?></span></div>
			</div>
			<ul class="navi">
				<li class="home<?php if(is_home()) echo ' current_page_item'; ?>"><a href="<?php echo get_option('home') ?>/" title="Home"><?php _e('Home','inkblot') ?></a></li>
				<?php wp_list_pages('title_li=&link_before=<span>&link_after=</span>') ?>
				<li class="feed"><a href="<?php bloginfo('rss2_url') ?>" title="Subscribe"><?php _e('Subscribe','inkblot') ?></a></li>
			</ul>
			<br class="clear" />
		</div>
		<div id="body"><?php inkblot_begin_content('i') //Delete this line and terrible things will happen ?>