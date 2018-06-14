---
title: webcomic_integrate_landing_page_content_templates
permalink: webcomic_integrate_landing_page_content_templates
---

> Alter the content templates to look for.

```php
apply_filters( 'webcomic_integrate_landing_page_content_templates', array $templates, string $collection )
```

This filter allows hooks to alter the possible content templates used when
integrating comic content into a landing page.

## Parameters

### `array` $templates
The list of content templates.

### `string` $collection
The current collection.
