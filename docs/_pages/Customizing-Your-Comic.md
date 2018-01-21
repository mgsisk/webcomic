---
title: Customizing Your Comic
permalink: Customizing-Your-Comic
---

[![The standard settings for comic collections.][img-1]][img-1]

This is the **Collection Settings** screen, where you can customize your
comic's name, permalinks, features, and a variety of other options. Every comic
collection has a settings screen like this, with collapsable sections
containing options for fine-tuning how your comic works. This is also where
you'll go to create new collections and delete existing collections.

## General

These settings allow you to change some of the basic information and features
related to your collection:

### Name

The name of your comic, as it will appear on your site.

### Slug

The slug is the URL-friendly text used for archives. It can contain lowercase
letters, numbers, and hyphens. This setting has no effect with Plain Permalinks.

### Permalink

The permalink is the URL-friendly text used to build links to your comics. It's
best to keep permalinks simple, but [a variety of special tokens][url-1] are
available for customizing your comic permalinks. Be careful with this setting;
comics may be unavailable if you use a setting that is not unique to your
collection. This setting has no effect with Plain Permalinks.

### Description

The description is not prominent by default; some themes may show it, though.

### Image

A representative image to display on your site.

### Archives

These settings control the WordPress archive page options for your collection.
Most WordPress archive pages will include comics by default, but date archives
don't include comics by default. _Include in standard date archives_ will add
comics from this collection to the standard WordPress date archives. _Sort
comics in chronological order_ will force WordPress archive pages for this
collection to sort in chronological order, instead of the standard
reverse-chronological order.

### Syndication

These settings control the syndication options for your collection. _Include in
the main syndication feed_ will add comics from your collection to the main site
syndication feed, which doesn't include comics by default. _Include comic
previews in syndication feeds_ will include a small comic preview along with
comics in all syndication feeds; you can customize this preview using an
appropriate widget area on the widgets management page.

### Supports

These settings control the basic features and taxonomies of your collection.
Disabling a feature or taxonomy will remove it's associated box from the Add
New Comic and Edit Comic screens.

Widget Areas allow you to enable collection-specific widget areas for
customizable comic features. For example, you can customize syndication
previews using the Webcomic Syndication widget area. If you want this
collection to have a unique appearance in syndication feeds, you can enable the
Syndication widget area and add widgets to this collection's Syndication widget
area, which Webcomic will use instead of the generic Webcomic Syndication widget
area.

## Theme

This section allows you to select a theme to use for pages related to this
collection. You can customize collection themes, but WordPress doesn't support
more than one active theme so some customizations – changing widgets, for
example – may not work or may affect the active site theme.

Keep in mind that Webcomic can't always determine which collection a page
belongs to, in which case the active site theme will take over. A good example
is the standard, non-static front page; even if you choose to display your
comic or comics on this page, Webcomic can't determine the current collection
for this page and will default to using the active site theme.

## Managing collections

You probably won't spend a lot of time on the collection settings page once you
get everything set how you like, but there are some important tasks that you
can perform here.

### Adding a collection

If you'd like to create a new comic collection, click the **New Collection**
link at the top of the page. Webcomic will copy _most_ of the settings from the
collection you click the New Collection link on, so you can create similar
collections by setting up one collection the way you like and then creating new
collections from that one.

### Deleting a collection

[![The standard settings for comic collections.][img-2]][img-2]

You can't delete the first collection Webcomic creates for you, but if you want
to delete another collection you've created expand the **Delete** section, enter
the name of the collection, and click Save Changes. Make sure you actually want
to delete a collection before you do this; Webcomic will delete **everything**
related to the collection – except uploaded media.

### Collections are unique

It's important to understand that comic collections are unique. Each represents
a set of distinct posts, tags, settings, and other data that define an entire
comic series. This is most important when talking about how readers navigate
through your comic; because of their distinct nature, collections don't "cross
paths" in the sense that a reader can be browsing through one collection and
end up in a different collection.

If you plan to manage a variety of distinct comics on your site, collections
should make perfect sense. If you want an interconnected set of stories,
collections may not be the best option. Webcomic has other ways to organize and
distinguish sets of comics, which we'll talk about in a moment.

## Beyond the basics

If you've been following along from the start of this guide we've now covered
almost all the basic features Webcomic provides. You should now know about the
All Comics page, how to add a new comic or edit existing comics, and how to
integrate those comics into your theme. Here we've explained Webcomic's basic
collection settings and how they can make your comic unique, as well as how to
expand your site with new collections or delete old ones.

There's still more that Webcomic can do for you, though. When you're ready,
head to the **Settings > Webcomic** screen to see everything Webcomic offers.

**[Take me to the global settings ⇝](Doing-More-with-Your-Comic)**

[img-1]: srv/Collection-Settings.png
[img-2]: srv/Collection-Settings-Delete.png
[url-1]: webcomic_permalink_tokens
