---
title: get_webcomic_transcript_authors()
permalink: get_webcomic_transcript_authors()
---

> Get comic transcript authors.

```php
get_webcomic_transcript_authors( array $args = [] ) : array
```

## Parameters

### `array` $args
Optional arguments.

- **`string` hide_duplicates**  
Optional author field to check for
duplicate values; one of name, email,
url, time, ip, or an empty string to not
hide duplicates.
- **`int` limit**  
Optional maximum number of authors to return.
- **`string` order**  
Optional author sort order; one of asc or desc.
- **`string` orderby**  
Optional author sort field; one of name, email,
url, time, or ip.
- **`mixed` post**  
Optional post to get transcript authors for.
- **`string` prefer_duplicate**  
Optional duplicate priority to use when
$hide_duplicates is not empty; one of
first or last.

## Return

`array`
