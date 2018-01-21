---
title: get_webcomic_transcript_terms_list()
permalink: get_webcomic_transcript_terms_list()
---

> Get a list of comic transcript terms.

```php
get_webcomic_transcript_terms_list( array $args = [] ) : string
```

## Parameters

### `array` $args
Optional arguments.

- **`array` current**  
Optional term ID of the current term or terms.
- **`string` format**  
Optional flat list format, like before{{join}}after.
Including `<select>` or `<optgroup>` elements will
convert links to `<option>` elements. Using
webcomics_optgroup as a join will replace collection
links with a list of comic `<option>` elements
wrapped in an `<optgroup>`. When $hierarchical is
true, before and after are mapped to the $start and
$end arguments.
- **`string` start**  
Optional text to prepend to the list when
$hierarchical is true.
- **`string` start_lvl**  
Optional text to prepend to a list level when
$hierarchical is true.
- **`string` start_el**  
Optional text to prepend to list items when
$hierarchical is true.
- **`string` end_el**  
Optional text to append to list items when
$hierarchical is true.
- **`string` end_lvl**  
Optional text to append to a list level when
$hierarchical is true.
- **`string` end**  
Optional text to append to the list when $hierarchical
is true.
- **`string` walker**  
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Transcribe\TermLister.

## Return

`string`

## Uses
- [get_webcomic_transcript_terms()](get_webcomic_transcript_terms())  
The fields argument is always set to
`all`.
