---
title: get_webcomic_transcript_form()
permalink: get_webcomic_transcript_form()
---

> Get a comic transcript form.

```php
get_webcomic_transcript_form( array $args = [] ) : string
```

## Parameters

### `array` $args
Optional arguments.

- **`string` cancel_link**  
Optional cancel link text. Should not contain
block-level elements, as the link will be
wrapped in a `<span>`.
- **`string` cancel_link_hash**  
Optional cancel link URL hash.
- **`array` fields**  
Optional form fields.
- **`string` form**  
Optional form structure, like before\{\{fields}}after.
Should include the %fields token.
- **`string` format**  
Optional form format, like before\{\{form}}after.
Should include the %cancel-link and %form tokens.
- **`string` logged_in_as**  
Optional message to display to logged-in
users.
- **`string` must_log_in**  
Optional message to display to anonymous
users if they must login to transcribe.
- **`mixed` post_parent**  
Optional comic the form belongs to.
- **`mixed` post**  
Optional comic transcript ID the form is for.
- **`string` transcript_notes_after**  
Optional message to display after
the transcript field.
- **`string` transcript_notes_before**  
Optional message to display
before the the form fields.

## Return

`string`
