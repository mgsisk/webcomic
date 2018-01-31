---
title: webcomic_delete_collection
permalink: webcomic_delete_collection
---

> Delete a collection.

```php
do_action( 'webcomic_delete_collection', string $id )
```

This action provides a way for hooks to perform collection-specific cleanup
and other actions before deleting the collection settings.

## Parameters

### `string` $id
The ID of the deleted collection.
