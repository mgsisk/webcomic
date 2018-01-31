---
title: \[webcomic_prints_list\]
permalink: webcomic_prints_list__
---

> Display a list of comic prints.

```php
[webcomic_prints_list]
```

## Attributes

### `string` format
Optional list format, like before{{join}}after.
Including `<select>` or `<optgroup>` elements will
convert links to `<option>` elements.

### `string` link
Optional link text, like before{{text}}after.

### `mixed` link_post
Optional reference post for print links.

### `array` link_args
Optional arguments for print links.

### `int` cloud_min
Optional weighted list minimum font size.

### `int` cloud_max
Optional weighted list maximum font size.

### `string` cloud_val
Optional print value to use for cloud
calculations; one of sold, adjust, base, left,
price, or stock.

### `string` walker
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Commerce\Walker\PrintLister.

### `mixed` post
Optional post to get prints for.

### `string` order
How to sort prints; one of asc or desc.

### `string` limit
Whether to limit the number of prints returned.

### `string` orderby
What to sort prints by; one of name, adjust,
base, left, price, sold, slug, or stock.

### `bool` hide_empty
Whether to include or exclude sold out prints.

### `bool` hide_infinite
Whether to include or exclude prints with
infinite stock.

### `int` adjust_min
Optional minimum price adjustment prints must
have.

### `int` adjust_max
Optional maximum price adjustment prints must
have.

### `float` base_min
Optional minimum base price prints must have.

### `float` base_max
Optional maximum base price prints must have.

### `int` left_min
Optional minimum left count prints must have.

### `int` left_max
Optional maximum left count prints must have.

### `float` price_min
Optional minimum price prints must have.

### `float` price_max
Optional maximum price prints must have.

### `int` sold_min
Optional minimum sold count prints must have.

### `int` sold_max
Optional maximum sold count prints must have.

### `int` stock_min
Optional minimum stock prints must have.

### `int` stock_max
Optional maximum stock prints must have.

### `array` slug__in
Optional slugs the collections must match.

### `array` slug__not_in
Optional slugs the collections must not
match.

## Uses
- [get_webcomic_prints_list()](get_webcomic_prints_list())
