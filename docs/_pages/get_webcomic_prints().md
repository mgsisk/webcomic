---
title: get_webcomic_prints()
permalink: get_webcomic_prints()
---

> Get comic prints data.

```php
get_webcomic_prints( array $args = [] ) : array
```

## Parameters

### `array` $args
Optional arguments.

- **`mixed` post**  
Optional post to get prints for.
- **`string` order**  
How to sort prints; one of asc or desc.
- **`string` limit**  
Whether to limit the number of prints returned.
- **`string` orderby**  
What to sort prints by; one of name, adjust,
base, left, price, sold, slug, or stock.
- **`bool` hide_empty**  
Whether to include or exclude sold out prints.
- **`bool` hide_infinite**  
Whether to include or exclude prints with
infinite stock.
- **`int` adjust_min**  
Optional minimum price adjustment prints must
have.
- **`int` adjust_max**  
Optional maximum price adjustment prints must
have.
- **`float` base_min**  
Optional minimum base price prints must have.
- **`float` base_max**  
Optional maximum base price prints must have.
- **`int` left_min**  
Optional minimum left count prints must have.
- **`int` left_max**  
Optional maximum left count prints must have.
- **`float` price_min**  
Optional minimum price prints must have.
- **`float` price_max**  
Optional maximum price prints must have.
- **`int` sold_min**  
Optional minimum sold count prints must have.
- **`int` sold_max**  
Optional maximum sold count prints must have.
- **`int` stock_min**  
Optional minimum stock prints must have.
- **`int` stock_max**  
Optional maximum stock prints must have.
- **`array` slug__in**  
Optional slugs the collections must match.
- **`array` slug__not_in**  
Optional slugs the collections must not
match.

## Return

`array`
