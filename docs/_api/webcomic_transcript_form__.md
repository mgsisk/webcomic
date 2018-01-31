---
title: \[webcomic_transcript_form\]
permalink: webcomic_transcript_form__
---

> Display a comic transcript form.

```php
[webcomic_transcript_form]
```

NOTE Because of the complexity of this functions arguments, we set all of the
defaults to null and then remove arguments from the list that the user didn't
set themselves, allowing the function defaults to take over without having to
repeat them here.

Shortcode content overrides the `item` attribute.

## Attributes

### `array` fields
Optional form fields.

### `string` form
Optional form structure, like before{{fields}}after.
Should include the %fields token.

### `string` format
Optional form format, like before{{form}}after.
Should include the %cancel-link and %form tokens.

### `string` logged_in_as
Optional message to display to logged-in
users.

### `string` must_log_in
Optional message to display to anonymous
users if they must login to transcribe.

### `mixed` post
Optional comic transcript ID the form is for.

### `string` transcript_notes_after
Optional message to display after
the transcript field.

### `string` transcript_notes_before
Optional message to display
before the the form fields.

## Uses
- [get_webcomic_transcript_form()](get_webcomic_transcript_form())
