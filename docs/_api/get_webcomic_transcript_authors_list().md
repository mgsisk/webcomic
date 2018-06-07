---
title: get_webcomic_transcript_authors_list()
permalink: get_webcomic_transcript_authors_list()
---

> Get a list of comic transcript authors.

```php
get_webcomic_transcript_authors_list( array $args = [] ) : string
```

## Parameters

### `array` $args
Optional arguments.

- **`string` format**  
Optional list format, like before\{\{join}}after.
Including `<select>` or `<optgroup>` elements will
convert links to `<option>` elements.
- **`string` link_rel**  
Optional link rel attribute value; may be a
space-separated list of valid link types or an
empty string to remove the href attribute.
- **`string` link**  
Optional link text, like before\{\{text}}after.
- **`string` walker**  
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Transcribe\Walker\AuthorLister.

## Return

`string`

## Uses
- [get_webcomic_transcript_authors()](get_webcomic_transcript_authors())
