---
title: get_webcomic_transcript_terms()
permalink: get_webcomic_transcript_terms()
---

> Get comic transcript terms.

```php
get_webcomic_transcript_terms( array $args = [] ) : array
```

## Parameters

### `array` $args
Optional arguments.

- **`array` related**  
Optional arguments to use when retrieving post
transcripts to determine what terms are related
to a post.
- **`string` type**  
Optional taxonomy type, like character or storyline.
Specifying a $type overrides any specified $taxonomy;
it will be combined with the $collection argument to
produce a taxonomy.

## Return

`array`

## Uses
- [get_terms()](https://developer.wordpress.org/reference/functions/get_terms/)  
Accepts
get_terms() arguments as well.
