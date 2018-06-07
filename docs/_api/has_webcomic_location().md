---
title: has_webcomic_location()
permalink: has_webcomic_location()
---

> Does the post have a comic location?

```php
has_webcomic_location( string $scope = '', mixed $term = null, mixed $post = null ) : bool
```

## Parameters

### `string` $scope
Optional scope to check. May be a collection ID (like
webcomic1), a scope keyword (like own or crossover), or empty.

### `mixed` $term
Optional term to check.

### `mixed` $post
Optional post to check.

## Return

`bool`

## Uses
- [has_webcomic_term()](has_webcomic_term())  
The taxonomy argument is always set to
`location` or `{$scope}_location`.
