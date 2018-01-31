---
title: get_webcomics_list()
permalink: get_webcomics_list()
---

> Get a list of comics.

```php
get_webcomics_list( array $args = [] ) : string
```

## Parameters

### `array` $args
Optional arguments.

- **`string` format**  
Optional list format, like before{{join}}after.
Including `<select>` or `<optgroup>` elements will
convert links to `<option>` elements.
- **`string` link**  
Optional link text, like before{{text}}after.
- **`array` link_args**  
Optional link arguments.
- **`int` cloud_min**  
Optional weighted list minimum font size.
- **`int` cloud_max**  
Optional weighted list maximum font size.
- **`int` current**  
Optional post ID of the current comic.
- **`string` walker**  
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Collection\Walker\ComicLister.

## Return

`string`

## Uses
- [get_webcomics()](get_webcomics())  
The fields argument is always set to `ids`.
