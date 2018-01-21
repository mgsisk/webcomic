---
title: get_webcomic_url()
permalink: get_webcomic_url()
---

> Get a comic URL.

```php
get_webcomic_url( mixed $post = null, array $args = [] ) : string
```

## Parameters

### `mixed` $post
Optional post to get a URL for.

### `array` $args
Optional arguments.

- **`bool` query_url**  
Whether to use query parameters for the URL.
- **`bool` confirm_age**  
Whether to set an age confirmation parameter
(Restrict component only).
- **`string` print**  
Optinal print slug to use for a purchase comic print
url (Commerce component only).
- **`string` transcribe**  
Optional transcript ID to edit (Transcribe
component only).
- **`string` transcripts**  
Optional transcripts list ID (Transcribe
component only).

## Return

`string`
