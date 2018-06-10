---
title: Twitter
permalink: Twitter
---

[![The settings added to the Collection Settings screen by the Twitter
component.][img-1]][img-1]

The **Twitter** component adds settings for updating your Twitter account status when publishing comics.

## Settings

Twitter settings allow you to send status updates to a Twitter account whenever
you publish a comic.

### Account

The account that will update when you publish a comic. To connect your
collection to a Twitter account, you'll need to create a Twitter Application.
Set the optional Callback URL for your Twitter Application to your site URL.

Once created, go to the Permissions page for your Twitter Application and
select Read and Write for Access. Then, go to the Keys and Access Tokens page,
click Regenerate Consumer Key and Secret (to ensure your app permissions take
effect), and copy the Consumer Key (API Key) and Consumer Secret (API Secret)
into their respective fields on the collection settings page. A Sign in with
Twitter button should appear.

When you've completed the authorization process you'll see the details for your
Twitter account here. Clicking Deauthorize will clear the collection's
authorization information and disable status updates until you've authorized a
new account.

### Update

These settings control status updates for new comics.

**Update status when publishing new comics** will enable status updates to your
authorized Twitter account for any newly-published comics.

**Include comic media with status updates** will attempt to attach comic media
when making a status update.

**Comic media may contain sensitive content** tells Twitter that the media
attached to your update may contain nudity, violence, or other sensitive
content.

You can adjust these settings on a per-comic basis.

### Status

These settings control the status update format for new comics. The status
field accepts [a variety of special tokens][url-1]. The media selected here
will replace any post thumbnails or comic media when making a status update.
You can adjust these settings on a per-comic basis.

### Cards

These settings allow you to add Twitter Card meta data to your comic pages.
Twitter Cards attach rich media to status updates and make tweets with links to
your comic more prominent. Add card meta tags to comic pages will add the
necessary meta tags for basic card support to pages related to this collection.
Include comic media card meta tags will add extra meta tags with links to comic
media.

If you don't see Twitter Cards in your status updates, try running a comic page
URL through the Twitter Card Validator. Twitter requires that you validate one
URL for each card type you want to use with your site; depending on the options
selected here, a page will use Summary Cards or Summary Cards with Large Image.
Once validated, any page with that Twitter Card type should work; you don't
have to validate every page on your site.

## Managing status updates

[![The Add New Comic screen, with Webcomic Twitter Status box
enabled.][img-2]][img-2]

Once you've enabled the Twitter component, head to the Add/Edit Comic screen.
You should notice a new **Webcomic Twitter Status** box.

This box lets you customize Twitter account updates for your comic. You should
see the account authorized for your collection here.

**Update status on publish** will update that accounts Twitter status whenever
you publish your comic, even if it's already published; uncheck this to prevent
status updates.

**Include media with update** will attempt to include either the comic media,
post thumbnail, or status-specific image you select in this box with any status
updates. Check **Media may be sensitive** if the media may contain nudity,
violence, or other sensitive content.

The status field lets you customize the update format for your comic.

[img-1]: srv/Twitter.png
[img-2]: srv/Twitter-Box.png
[url-1]: webcomic_twitter_status_tokens
