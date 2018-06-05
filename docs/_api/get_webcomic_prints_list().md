---
title: get_webcomic_prints_list()
permalink: get_webcomic_prints_list()
---

> Get a list of comic prints.

```php
get_webcomic_prints_list( array $args = [] ) : string
```

## Parameters

### `array` $args
Optional arguments.

- **`string` format**  
Optional list format, like before\{\{join}}after.
Including `<select>` or `<optgroup>` elements will
convert links to `<option>` elements.
- **`string` link**  
Optional link text, like before\{\{text}}after.
- **`mixed` link_post**  
Optional reference post for print links.
- **`array` link_args**  
Optional arguments for print links.
- **`int` cloud_min**  
Optional weighted list minimum font size.
- **`int` cloud_max**  
Optional weighted list maximum font size.
- **`string` cloud_val**  
Optional print value to use for cloud
calculations; one of sold, adjust, base, left,
price, or stock.
- **`string` walker**  
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Commerce\Walker\PrintLister.

## Return

`string`

## Uses
- [get_webcomic_prints()](get_webcomic_prints())
