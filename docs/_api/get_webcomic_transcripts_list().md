---
title: get_webcomic_transcripts_list()
permalink: get_webcomic_transcripts_list()
---

> Get a list of comic transcripts.

```php
get_webcomic_transcripts_list( array $args = [] ) : string
```

## Parameters

### `array` $args
Optional arguments.

- **`array` authors_list**  
Optional arguments for
get_webcomic_transcript_authors_list(); only
used when $item contains the %authors token.
- **`string` edit_link**  
Optional edit link format, like
before{{text}}after.
- **`string` format**  
Optional list format, like before{{join}}after.
- **`string` item**  
Optional item format, like before{{item}}after. The
before text should include two sprintf() tokens,
which will be replaced with the transcript ID and
CSS class names, respectively.
- **`array` languages_list**  
Optional arguments for
get_webcomic_transcript_languages_list();
only used when $item contains the %languages
token.
- **`string` parent_link**  
Optional parent link format, like
before{{text}}after.
- **`string` walker**  
Optional custom Walker class to use instead of
Mgsisk\Webcomic\Transcribe\Walker\TranscriptLister.

## Return

`string`

## Uses
- [get_webcomic_transcripts()](get_webcomic_transcripts())  
The fields argument is always set to 'ids'.
