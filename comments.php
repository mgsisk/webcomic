<?php
/**
 * @package InkBlot
 * @since 1.0
 */
	if(!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) die(__('Please do not load this page directly. Thanks!','inkblot'));
	if(post_password_required()): ?><p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.','inkblot') ?></p><?php return; endif;
?>

<?php if(have_comments()): ?>
	<h2 id="comments"><?php comments_number() ?></h2>
	<ol class="commentlist"><?php wp_list_comments() ?></ol>
	<div class="page-navi">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
<?php endif; ?>

<?php if('open' == $post->comment_status): ?>
<div id="respond">
	<h3><?php comment_form_title() ?></h3>
	<?php if(get_option('comment_registration') && !$user_ID): ?>
		<p><?php printf(__('You must be <a href="%s">logged in</a> to comment.','inkblot'),get_option('siteurl').'/wp-login.php?redirect_to='.urlencode(get_permalink())) ?></p>
	<?php else: ?>
	<form action="<?php echo get_option('siteurl') ?>/wp-comments-post.php" method="post" id="commentform">
	<?php if($user_ID): ?>
		<p><?php printf(__('Commenting as <a href="%s">%s</a>','inkblot'),get_option('siteurl').'/wp-admin/profile.php',$user_identity) ?> &bull; <a href="<?php echo wp_logout_url(get_permalink()) ?>"><?php _e('Log Out &raquo;','inkblot') ?></a></p>
	<?php else: ?>
		<p><input type="text" name="author" id="author" value="<?php echo $comment_author ?>" />&emsp;<label for="author" class="soft"><?php _e('Name','inkblot'); if($req): printf(__('(required; <a href="%1$s">get an avatar</a>)','inkblot'),'http://gravatar.com/'); else: echo '(<a href="http://gravatar.com/">'.__('get an avatar','inkblot').'</a>)'; endif; ?></label></p>
		<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email ?>" />&emsp;<label for="email" class="soft"><?php _e('Mail','inkblot'); if($req): _e('required; will not be published)','inkblot'); else: _e('(will not be published)','inkblot'); endif; ?></label></p>
		<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url ?>" />&emsp;<label for="url" class="soft"><?php _e('Website','inkblot') ?></label></p>
	<?php endif; ?>
		<!--<p class="meta"><strong>XHTML:</strong><code><?php echo allowed_tags(); ?></code></p>-->
		<p><textarea name="comment" id="comment" cols="15" rows="10"></textarea></p>
		<p class="align-right"><span class="cancel-comment-reply"><?php cancel_comment_reply_link() ?>&emsp; </span><input name="submit" type="submit" id="submit" value="Comment" /><?php comment_id_fields() ?></p>
		<?php do_action('comment_form', $post->ID) ?>
	</form>
</div>
<?php endif; endif; ?>
