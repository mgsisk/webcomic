---
title: get_webcomic_transcript_form_fields
permalink: get_webcomic_transcript_form_fields
---

> Alter the get_webcomic_transcript_form() fields.

```php
apply_filters( 'get_webcomic_transcript_form_fields', array $fields, array $args, array $commenter )
```

This filter allows hooks to alter the fields passed to
get_webcomic_transcript_form().

## Parameters

### `array` $fields
The fields to filter.

### `array` $args
The get_webcomic_transcript_form() arguments.

### `array` $commenter
The current commenter.
