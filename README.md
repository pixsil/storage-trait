# storage-trait for Laravel

[THIS IS WORK IN PROGRSS]

## Features / What is it?

* -
* -

## Donate

Find this project useful? You can support me on Patreon

https://www.patreon.com/pixsil

## Installation

For a quick install, run this from your project root:
```bash

```

## Usage



## Additional knowledge

If you like to make use of the automatic secure link functionality "$project->secureLink('file')", you need to add the following to your project:

Route:
```php
Route::get('downloads', [DownloadController::class, 'download'])->name('downloads');
```

DownloadController:
```php
<?php

namespace App\Http\Controllers;

class DownloadController extends Controller
{
    public function download($table, $id, $field)
    {
        //
    }
}
```

You can use a different route name with the second secureLink parameter:

$project->secureLink('file', 'a_different_route_name');

If you want protect some tables downloads with rights you can define routes with specific tables (or id's/fieldnames) specifically for the right group. For example:

```php
Route::get('downloads/table-name/{id}/{field}', [DownloadController::class, 'download'])->name('admin-downloads');
```

## Example

Check the example folder for a Vue component example
