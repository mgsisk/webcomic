---
title: get_webcomic_terms_list()
permalink: get_webcomic_terms_list()
---

> Get a list of comic terms.

```php
get_webcomic_terms_list( array $args = [] ) : string
```

## Parameters

### `array` $args
Optional arguments.

- **`int` cloud_max**  
Optional weighted list maximum font size.
- **`int` cloud_min**  
Optional weighted list minimum font size.
- **`array` current**  
Optional term ID of the current term or terms.
- **`string` feed_type**  
Optional term feed type; one of atom, rss, or
rss2.
- **`string` feed**  
Optional term feed link text.
- **`string` format**  
Optional flat list format, like before{{join}}after.
Including `<select>` or `<optgroup>` elements will
convert links to `<option>` elements. Using
webcomics_optgroup as a join will replace collection
links with a list of comic `<option>` elements
wrapped in an `<optgroup>`. When $hierarchical is
true, before and after are mapped to the $start and
$end arguments.
- **`string` end_el**  
Optional text to append to list items when
$hierarchical is true.
- **`string` end_lvl**  
Optional text to append to a list level when
$hierarchical is true.
- **`string` end**  
Optional text to append to the list when $hierarchical
is true.
- **`array` link_args**  
Optional arguments for term links.
- **`mixed` link_post**  
Optional reference post for term links.
- **`array` link_post_args**  
Optional post arguments for term links.
- **`string` link**  
Optional link text, like before{{text}}after.
- **`string` start_el**  
Optional text to prepend to list items when
$hierarchical is true.
- **`string` start_lvl**  
Optional text to prepend to a list level when
$hierarchical is true.
- **`string` start**  
Optional text to prepend to the list when
$hierarchical is true.
- **`string` walker**  
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Taxonomy\Walker\TermLister.
- **`array` webcomics**  
Optional get_webcomics_list() arguments.
- **`int` webcomics_depth**  
Optional depth to list comics at.

## Return

`string`

## Uses
- [get_webcomic_terms()](get_webcomic_terms())  
The fields argument is always set to `all`.
