---
title: Restrict
permalink: Restrict
---

[![The settings added to the Collection Settings screen by the Restrict
component.][img-1]][img-1]

The **Restrict** component adds features for restricting access to comics based
on age or user role.

## Settings

Restrict settings allow you to set the default restriction settings for new
comics in your collection. You can adjust these settings on a per-comic basis.

### Minimum Age

The minimum age required to view the post content and comic media of newly
created comics. Users will have to confirm their age before viewing the post
content and comic media of comics with a minimum age greater than zero.

### Accessible Roles

The user roles allowed to view the post content and comic media of newly created
comics. Selecting Any registered user will allow any registered user to view the
post content and comic media once they've logged in, overriding any other role
selections.

### Age-Restricted Media

Alternative media to display to age-restricted users in place of the normal
comic media.

### Role-Restricted Media

Alternative media to display to role-restricted users in place of the normal
comic media.

### Password-Restricted Media

Alternative media to display to password-restricted users in place of the
normal comic media.

## Managing restrictions

[![The Add New Comic screen, with Restrict boxes enabled.][img-2]][img-2]

Once you've enabled the Restrict component, head to the Add/Edit Comic screen.
You should notice threw new boxes: **Webcomic Age Restrictions**, **Webcomic
Password Restrictions**, and **Webcomic Role Restrictions**.

### Webcomic Age Restrictions

This box lets you set a minimum age required to view your comic. When set, users
will have to confirm their age before viewing your comic's content, comments,
and media. You can also select alternative media to display when users can't
view the normal comic media.

### Webcomic Password Restrictions

This box lets you select alternate media to display when your comic is password
protected. You can set a post password by selecting Password protected under
Visibility using the standard Publish meta box.

### Webcomic Role Restrictions

This box lets you specify what roles users must have to view your comic. Users
will not be able to view your comic's content, comments, and media unless they
login and have one of the selected roles. You can also select alternative media
to display when users can't view the normal comic media.

Selecting Any registered user will allow any registered user to view your comic
content, comments, and media once they've logged in, overriding any other role
selections. Also note that any user that has permission to edit your comic will
be able to see your comic content, comments, and media in the dashboard,
regardless of the selected role restrictions.

[img-1]: srv/Restrict.png
[img-2]: srv/Restrict-Box.png
