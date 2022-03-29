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
mkdir -p app/Traits
wget -O app/Traits/StorageTrait.php https://raw.githubusercontent.com/pixsil/storage-trait/main/Traits/StorageTrait.php
```

## Usage

Disk:
If no disk is provided, the storage trait is using the private disk by default. Make sure you got an private disk in your filesystem config:

```php
        'private' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
        ],
```

## Additional knowledge

If you like to make use of the automatic secure link functionality "$project->secureLink('file')", you need to add the following to your project:

Route:
```php
Route::get('downloads/{table}/{id}/{field}/{file}', [DownloadController::class, 'download'])->name('admin-downloads');
```

DownloadController:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Download;

class DownloadController extends Controller
{
    public function download($table, $id, $field, $file)
    {
        //
        $download = new Download();
        $download = $download->setTable($table)->where($field, $file)->findOrFail($id);

        $download->streamFile($field);
    }
}

```

Download (Model):

```php
<?php

namespace App\Models;

use App\Traits\StorageTrait;

class Download extends BaseModal
{
    use StorageTrait;

    protected $table = false;
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
