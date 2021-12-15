<?php

// version 19 Changed behavior it will always default upload it to 'private' storage (if no disk is set)
// version 18 Added a function to upload with and without fail if not existing in request
// version 17 Added secure download file function (see docs)
// version 16 (recreated the upload part in the new way)

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait StorageTrait
{
    /**
     * get storage path
     * example: assignment_attachments/2/attachment
     */
    public function getDiskPath($field)
    {
        // guard must save first to receive id
        if (!$id = $this->id) {
            return null;
        }

        // set path and filename
        return $this->getTable() .'/'. $id .'/'. $field;
    }

    /**
     * get storage file path
     * example: assignment_attachments/2/attachment/be0330162f7b.pdf
     */
    public function getDiskFile($field)
    {
        // guard must save first to receive id
        if (!$id = $this->id) {
            return null;
        }
        // guard if field excist
        if (!$value = $this->$field) {
            return null;
        }

        // set path and filename
        return $this->getTable() .'/'. $id .'/'. $field .'/'. $value;
    }

    //    /**
    //     * get storage path
    //     * example: assignment_attachments/2/attachment
    //     */
    //    public function getProjectPath($field, $disk = 'private')
    //    {
    //        // guard must save first to receive id
    //        if (!$id = $this->id) {
    //            return null;
    //        }
    //
    //        // fall back to default disk
    // //        $disk = $disk ?? 'private';
    //
    //        // get the path
    //        $path = Storage::disk($disk)->url('');
    //
    //        // set path and filename
    //        return $path . $this->getTable() .'/'. $id .'/'. $field;
    //    }

    //    /**
    //     * get storage file path
    //     * example: assignment_attachments/2/attachment/be0330162f7b.pdf
    //     */
    //    public function getProjectFile($field, $disk = 'private')
    //    {
    //        // guard must save first to receive id
    //        if (!$id = $this->id) {
    //            return null;
    //        }
    //        // guard if field excist
    //        if (!$value = $this->$field) {
    //            return null;
    //        }
    //
    //        // fall back to default disk
    // //        $disk = $disk ?? 'private';
    //
    //        // get the path
    //        $path = Storage::disk($disk)->path('');
    //
    //        // set path and filename
    //        return $path . $this->getTable() .'/'. $id .'/'. $field .'/'. $value;
    //    }

    /**
     * get storage path
     * example: assignment_attachments/2/attachment
     */
    public function getPublicFile($field, $disk = 'private')
    {
        var_dump('add this');exit;
    }

    /**
     * get storage path
     * example: /home/vagrant/code/site_name/storage/app/public/assignment_attachments/2/attachment
     */
    public function getSystemPath($field, $disk = 'private')
    {
        // guard must save first to receive id
        if (!$id = $this->id) {
            return null;
        }

        // fall back to default disk
        // $disk = $disk ?? 'private';

        // get the path
        $path = Storage::disk($disk)->path('');

        // set path and filename
        return $path . $this->getTable() .'/'. $id .'/'. $field;
    }

    /**
     * get storage system file path
     * example: /home/vagrant/code/site_name/storage/app/public/assignment_attachments/2/attachment/be0330162f7b.pdf
     */
    public function getSystemFile($field, $disk = 'private')
    {
        // guard must save first to receive id
        if (!$id = $this->id) {
            return null;
        }
        // guard if field excist
        if (!$value = $this->$field) {
            return null;
        }

        // fall back to default disk
        // $disk = $disk ?? 'private';

        // get the path
        $path = Storage::disk($disk)->path('');

        // set path and filename
        return $path . $this->getTable() .'/'. $id .'/'. $field .'/'. $value;
    }

    /**
     * get a protected download link
     */
    public function secureLink($field, $download_route_name = 'downloads')
    {
        // guard must save first to receive id
        if (!$id = $this->id) {
            return null;
        }
        // guard if field excist
        if (!$value = $this->$field) {
            return null;
        }

        $route = route($download_route_name, explode('/', $this->getDiskFile($field)));

        return $route;
    }

    /**
     * get a protected download link
     */
    public function streamFile($field, $download_name = null, $disk = 'private', $headers = [])
    {
        // guard must save first to receive id
        if (!$id = $this->id) {
            abort('404', 'Cannot find the download');
        }
        // guard if field excist
        if (!$value = $this->$field) {
            abort('503', 'Cannot find this download field');
        }
        // cannot find the file
        if (!$this->fileExists($field, $disk)) {
            abort('404', 'Cannot find the download');
        }

        // fall back to default disk
        // $disk = $disk ?? 'private';
        $path = $this->getDiskFile($field);
vd($path);

        return Storage::disk($disk)->download($path, $download_name, $headers);
    }

    //    /**
    //     * get storage file path
    //     * $identifier = for default: $height _ $with for curstom: hash
    //     * example: /home/vagrant/code/site_name/storage/app/private/projects/1/background_image/bg_500_250.jpg
    //     */
    //    public function getStorageImageFilePath_2($field, $identifier, $public = false)
    //    {
    //        // must save first to receive id
    //        if (!$id = $this->id) {
    //            return null;
    //        }
    //        // guard if field excist
    //        if (!$value = $this->$field) {
    //            return null;
    //        }
    //        // guard public path
    //        if (!$pathinfo = pathinfo($value)) {
    //            return null;
    //        }
    //        // guard filename
    //        if (!isset($pathinfo['filename'])) {
    //            return null;
    //        }
    //        // guard extension
    //        if (!isset($pathinfo['extension'])) {
    //            return null;
    //        }
    //
    //        // is it public or private folder
    //        $folder = 'private';
    //        if ($public) {
    //
    //            // set public
    //            $folder = 'public';
    //        }
    //
    //
    //
    //        // generate new image name
    //        $image_storage_name = $pathinfo['filename'] .'_'. $identifier .'.'. $pathinfo['extension'];
    //
    //        // set path and filename
    //        $image_storage_path = storage_path('app/'. $folder) .'/'. $this->getTable() .'/'. $id .'/'. $field .'/'. $image_storage_name;
    //
    //        return $image_storage_path;
    //    }
    //
    //    /*
    //     * create image
    //     *
    //     * parameters:
    //     * default = crop / keep-aspect-ratio / no-upscale / optimize
    //     *
    //     * f = change crop to fit
    //     * s = change keep-aspect-ratio to strach
    //     * u = change no-upscale to upscale
    //     * r = change optimize to no-optimize
    //     */
    //    protected function createImage_2($field, $max_height, $max_width, $image_storage_path, $param = null, $callback = null, $public = false)
    //    {
    //        // get storage path
    //        $filepath = $this->getStorageFilePath($field, $public);
    //
    //        // allow some more ram
    //        // ini_set('memory_limit', '384M');
    //
    //        // get image
    //        $image = Image::make($filepath);
    //
    //        // do image stuff
    //        if (!is_callable($callback)) {
    //
    //            // if fit
    //            if (strpos($param,'f')) {
    //
    //                // fit image
    //                $image->fit($max_width, $max_height, function ($constraint) use ($param) {
    //
    //                    // if upscale allowed
    //                    if (strpos($param,'u') === false) {
    //
    //                        // prevent from upscaling
    //                        $constraint->upsize();
    //                    }
    //                });
    //
    //            // if normal
    //            } else {
    //
    //                // nullable
    //                $max_height_nullable = $max_height == 0 ? null : $max_height;
    //                $max_width_nullable = $max_width == 0 ? null : $max_width;
    //
    //                // default do the resize
    //                $image->resize($max_height_nullable, $max_width_nullable, function ($constraint) use ($param) {
    //
    //                    // if stratch allowed
    //                    if (strpos($param,'s') === false) {
    //
    //                        // keep aspect, otherwise it get strached
    //                        $constraint->aspectRatio();
    //                    }
    //
    //                    // if upscale allowed
    //                    if (strpos($param,'u') === false) {
    //
    //                        // prevent from upscaling
    //                        $constraint->upsize();
    //                    }
    //                });
    //            }
    //
    //        // use custom function
    //        } else {
    //
    //            // do callback
    //            call_user_func($callback, $image);
    //        }
    //
    //        // save the image
    //        $image->save($image_storage_path);
    //
    //        // save the image
    //        if (!strpos($param,'r')) {
    //
    //            // optimize
    //            ImageOptimizer::optimize($image_storage_path);
    //        }
    //
    //        return $image;
    //    }
    //
    //    /**
    //     * get back the image object to serve
    //     * for public images
    //     */
    //    public function getImageUrl_2($field, $max_width, $max_height, $param = 0, $callback = null)
    //    {
    //        // gaurd must save first to receive id
    //        if (!$id = $this->id) {
    //            return null;
    //        }
    //        // guard if field excist
    //        if (!$value = $this->$field) {
    //            return false;
    //        }
    //
    //        //
    //        $public_path = 'storage/'. $this->getTable() .'/'. $id .'/'. $field .'/'. $value;
    //
    //        // get image url
    //        $url = ImageFactory::getImageUrl($public_path, $max_width, $max_height, $param);
    //
    //        return $url;
    //    }

    /**
     * does the file exists
     *
     * @return bool
     */
    public function fileExists($field, $disk = 'private')
    {
        if (!$value = $this->$field) {
            return false;
        }
        if (!Storage::disk($disk)->exists($this->getDiskPath($field)) .'/'. $value) {
            return false;
        }

        return true;
    }

    /**
     * upload the image
     *
     * @return $this
     */
    public function upload($request, $field, $request_field = null, $disk = 'private', $hash = null)
    {
        // fill request field with normal field when empty
        $request_field = $request_field ?? $field;

        // guard file exist
        if (!$request->hasFile($request_field)) {
            abort(503, 'The file does not exists in the request');
        }

        $this->uploadIfExists($request, $field, $request_field, $disk, $hash);
    }

    /**
     * upload the image
     *
     * @return $this
     */
    public function uploadIfExists($request, $field, $request_field = null, $disk = 'private', $hash = null)
    {
        // fill request field with normal field when empty
        $request_field = $request_field ?? $field;

        // guard file exist
        if (!$request->hasFile($request_field)) {
            return $this;
        }
        // guard no id, cannot save
        if (!$this->id) {
            abort(503, 'First save record before saving file.');
        }

       // fall back to default disk
       // $disk = $disk ?? 'private';

        $file = $request->$request_field;

        // delete if already there
        if ($this->fileExists($field, $disk)) {

            // delete
            $this->fileDelete($field, $disk);
        }

        //
        $path_to_save = $this->getDiskPath($field);

        // store file
        $saved_filename_path = Storage::disk($disk)->putFile($path_to_save, $file);
        $saved_filename = ltrim($saved_filename_path, $path_to_save);

        // set the filename to the database
        $this->$field = $saved_filename;

        return $this;
    }

    /**
     * delete the file
     *
     * @return self
     */
    public function fileDelete($field, $disk = 'private')
    {
        // guard if field exists
        if (!$value = $this->$field) {
            return $this;
        }
        // guard delete file and folder, if correct do cleanup in function
        if (!Storage::disk($disk)->deleteDirectory($this->getDiskPath($field))) {
            return $this;
        }

        // reset field
        $this->$field = '';

        // check if there are other uploaded files otherwise delete record folder
        if (!Storage::disk($disk)->allFiles($this->getTable() .'/'. $this->id)) {

            // delete
            Storage::disk($disk)->deleteDirectory($this->getTable() .'/'. $this->id);
        }

        // check if there are other uploaded files otherwise delete table folder
        if (!Storage::disk($disk)->allFiles($this->getTable())) {

            // delete
            Storage::disk($disk)->deleteDirectory($this->getTable());
        }

        return $this;
    }
}
