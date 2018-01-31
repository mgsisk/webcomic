---
title: webcomic_integrate_navigation
permalink: webcomic_integrate_navigation
---

> Integrate comic navigation.

```php
do_action( 'webcomic_integrate_navigation' )
```

This action provides a way for hooks to add comic navigation links. It
will display the Webcomic Navigation widget area when using integration.
You can use this to selectively display comic navigation, but it's often
better to use the combined `webcomic_integrate_media_and_navigation`
action.
