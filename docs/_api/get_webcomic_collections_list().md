---
title: get_webcomic_collections_list()
permalink: get_webcomic_collections_list()
---

> Get a list of collections.

```php
get_webcomic_collections_list( array $args = [] ) : string
```

## Parameters

### `array` $args
Optional arguments.

- **`string` format**  
Optional list format, like before{{join}}after.
Including `<select>` or `<optgroup>` elements will
convert links to `<option>` elements. Using
'webcomics_optgroup' as the join will replace
collection links with a list of comic `<option>`
elements wrapped in an `<optgroup>`.
- **`string` link**  
Optional link text, like before{{text}}after.
- **`mixed` link_post**  
Optional reference post for collection links.
- **`array` link_args**  
Optional link arguments.
- **`string` feed**  
Optional collection feed link text.
- **`string` feed_type**  
Optional collection feed type; one of atom,
rss, or rss2.
- **`int` cloud_min**  
Optional weighted list minimum font size.
- **`int` cloud_max**  
Optional weighted list maximum font size.
- **`string` current**  
Optional collection ID of the current collection.
- **`string` walker**  
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Collection\Walker\CollectionLister.
- **`array` webcomics**  
Optional get_webcomics_list() arguments.

## Return

`string`

## Uses
- [get_webcomic_collections()](get_webcomic_collections())  
The fields argument is always set to `ids`.
