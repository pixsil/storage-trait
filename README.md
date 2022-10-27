# storage-trait for Laravel

[THIS IS WORK IN PROGRSS]

## What is it?

This trait can be used on a model and makes storing het file handling very easy.

All files uploaded correspondent to a column in the database of the model. For example if I have a books table with an file upload column book_attachment the filename of the uploaded file will be directly stored in the book_attachment column. The file will be stored in the defined storage with the following path structure:

/table-name/field-name/id/filename.pdf

## Donate

Find this project useful? You can support me on Patreon

https://www.patreon.com/pixsil

## Installation

For a quick install, run this from your project root:
```bash
mkdir -p app/Traits
wget -O app/Traits/StorageTrait.php https://raw.githubusercontent.com/pixsil/storage-trait/main/Traits/StorageTrait.php
```

Disk:
If no disk is provided, the storage trait is using the private disk by default. Make sure you got an private disk in your filesystem config:

```php
        'private' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
        ],
```

## Usage

### Preperation

Add the following to your model where you like to use the storage trait.

Include at top:
```php
use App\Traits\StorageTrait;
```

In your class:
```php
use StorageTrait;
```

### Upload a file

To upload a file accepts the following parameter:

```php
$book->upload(request, field, [disk], [hashed]);
```

The first two parameters are necessary, the first is the Laravel request and the second is the database field that correspondent to the file. 

```php
$book = Book::first();
$book->upload($request, ‘book_attachment’);

# The file will be automatically uploaded in the private folder with the following path
# /storage/private/books/book_attachment/14/disney-attachment.pdf
```



If you would like to upload the file to a specific disk you can send a third parameter with the disk to use

```php
$book = Book::first();
$book->upload($request, ‘book_attachment’, ‘public');


# The file will be automatically uploaded in the private folder with the following path
# /public/books/book_attachment/14/disney-attachment.pdf
```


Last but not least, you can also use the trait to gives the filename a md5 hash. The new filename will be saved in the database.

```php
$book = Book::first();
$book->upload($request, ‘book_attachment’, null, true);

# The file will be automatically uploaded in the private folder with the following path
# /storage/private/books/book_attachment/14/5bc956936ec627b276793.pdf
```



If a file already exists it will be replaced automatically.

Get the file location:

With the following function you can get the location of the file. The first parameter is the field and the second is if it is in the public folder or not.

```php
$book = Book::first();
$book->getRelativeStoragePath(‘book_attachment’);
# /storage/private/books/book_attachment/14/5bc956936ec627b276793.pdf
```



If you would like to use a public directory linked to your public folder you can use the following:

```php
$book = Book::first();
$book->getRelativeStoragePath(‘book_attachment’, true);

# /books/book_attachment/14/5bc956936ec627b276793.pdf
```

### Delete a file

```php
$book = Book::first();
$book->fileDelete(‘book_attachment’);
```

## Other functions

getRelativeStoragePath
getRelativeStorageFilePath
getStorageFilePath
getStorageFilePath
getStorageImageFilePath_2
createImage_2
getImageUrl_2
getImageUrl_2
fileExists

## Bonus

Use with image-render-implementation. (Coming soon later!)






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
