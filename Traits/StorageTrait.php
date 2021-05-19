<?php

// version 12

namespace App\Traits;

use App\Classes\ImageFactory;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

trait StorageTrait
{
    /**
     * get storage path
     * example: assignment_attachments/2/attachment
     */
    public function getRelativeStoragePath($field, $public = false)
    {
        // must save first to receive id
        if (!$id = $this->id) {
            return null;
        }

        // is it public or private folder
        $folder = 'private';
        if ($public) {

            // set public
            $folder = 'public';
        }

        // set path and filename
        return $this->getTable() .'/'. $id .'/'. $field;
    }

    /**
     * get storage file path
     * example: assignment_attachments/2/attachment/be0330162f7b.pdf
     */
    public function getRelativeStorageFilePath($field, $public = false)
    {
        // must save first to receive id
        if (!$id = $this->id) {
            return null;
        }
        // guard if field excist
        if (!$value = $this->$field) {
            return null;
        }

        // is it public or private folder
        $folder = 'private';
        if ($public) {

            // set public
            $folder = 'public';
        }

        // set path and filename
        return $this->getTable() .'/'. $id .'/'. $field .'/'. $value;
    }

    /**
     * get storage path
     * example: /home/vagrant/code/site_name/storage/app/public/assignment_attachments/2/attachment
     */
    public function getStoragePath($field, $public = false)
    {
        // must save first to receive id
        if (!$id = $this->id) {
            return null;
        }

        // is it public or private folder
        $folder = 'private';
        if ($public) {

            // set public
            $folder = 'public';
        }

        // set path and filename
        return storage_path('app/'. $folder) .'/'. $this->getTable() .'/'. $id .'/'. $field;
    }

    /**
     * get storage file path
     * example: /home/vagrant/code/site_name/storage/app/public/assignment_attachments/2/attachment/be0330162f7b.pdf
     */
    public function getStorageFilePath($field, $public = false)
    {
        // must save first to receive id
        if (!$id = $this->id) {
            return null;
        }
        // guard if field excist
        if (!$value = $this->$field) {
            return null;
        }

        // is it public or private folder
        $folder = 'private';
        if ($public) {

            // set public
            $folder = 'public';
        }

        // set path and filename
        return storage_path('app/'. $folder) .'/'. $this->getTable() .'/'. $id .'/'. $field .'/'. $value;
    }

    /**
     * get storage file path
     * $identifier = for default: $height _ $with for curstom: hash
     * example: /home/vagrant/code/pixsil_site/storage/app/private/projects/1/background_image/spinub_bg_500_250.jpg
     */
    public function getStorageImageFilePath_2($field, $identifier, $public = false)
    {
        // must save first to receive id
        if (!$id = $this->id) {
            return null;
        }
        // guard if field excist
        if (!$value = $this->$field) {
            return null;
        }
        // guard public path
        if (!$pathinfo = pathinfo($value)) {
            return null;
        }
        // guard filename
        if (!isset($pathinfo['filename'])) {
            return null;
        }
        // guard extension
        if (!isset($pathinfo['extension'])) {
            return null;
        }

        // is it public or private folder
        $folder = 'private';
        if ($public) {

            // set public
            $folder = 'public';
        }



        // generate new image name
        $image_storage_name = $pathinfo['filename'] .'_'. $identifier .'.'. $pathinfo['extension'];

        // set path and filename
        $image_storage_path = storage_path('app/'. $folder) .'/'. $this->getTable() .'/'. $id .'/'. $field .'/'. $image_storage_name;

        return $image_storage_path;
    }

    /*
     * create image
     * 
     * parameters:
     * default = crop / keep-aspect-ratio / no-upscale / optimize
     *
     * f = change crop to fit
     * s = change keep-aspect-ratio to strach
     * u = change no-upscale to upscale
     * r = change optimize to no-optimize
     */
    protected function createImage_2($field, $max_height, $max_width, $image_storage_path, $param = null, $callback = null, $public = false)
    {
        // get storage path
        $filepath = $this->getStorageFilePath($field, $public);

        // allow some more ram
        // ini_set('memory_limit', '384M');

        // get image
        $image = Image::make($filepath);

        // do image stuff
        if (!is_callable($callback)) {

            // if fit
            if (strpos($param,'f')) {
    
                // fit image
                $image->fit($max_width, $max_height, function ($constraint) use ($param) {

                    // if upscale allowed
                    if (strpos($param,'u') === false) {

                        // prevent from upscaling
                        $constraint->upsize();
                    }
                });

            // if normal
            } else {

                // nullable
                $max_height_nullable = $max_height == 0 ? null : $max_height;
                $max_width_nullable = $max_width == 0 ? null : $max_width;

                // default do the resize
                $image->resize($max_height_nullable, $max_width_nullable, function ($constraint) use ($param) {

                    // if stratch allowed
                    if (strpos($param,'s') === false) {

                        // keep aspect, otherwise it get strached
                        $constraint->aspectRatio();
                    }

                    // if upscale allowed
                    if (strpos($param,'u') === false) {

                        // prevent from upscaling
                        $constraint->upsize();
                    }
                });
            }

        // use custom function
        } else {

            // do callback
            call_user_func($callback, $image);
        }

        // save the image
        $image->save($image_storage_path);

        // save the image
        if (!strpos($param,'r')) {

            // optimize
            ImageOptimizer::optimize($image_storage_path);
        }

        return $image;
    }

    /**
     * get back the image object to serve
     * for public images
     */
    public function getImageUrl_2($field, $max_width, $max_height, $param = 0, $callback = null)
    {
        // gaurd must save first to receive id
        if (!$id = $this->id) {
            return null;
        }
        // guard if field excist
        if (!$value = $this->$field) {
            return false;
        }

        //
        $public_path = 'storage/'. $this->getTable() .'/'. $id .'/'. $field .'/'. $value;

        // get image url    
        $url = ImageFactory::getImageUrl($public_path, $max_width, $max_height, $param);

        return $url;
    }

    /**
     * does the file exists
     *
     * @return bool
     */
    public function fileExists($field, $public = false)
    {
        // guard if field excist
        if (!$value = $this->$field) {
            return false;
        }
        // guard if file not excist
        if (!File::exists($this->getStorageFilePath($field, $public))) {
            return false;
        }

        return true;
    }

    /**
     * upload the image
     *
     * @return bool
     */
    public function upload($request, $field, $public = false, $hash = true)
    {
        // guard file exist
        if (!$request->hasFile($field)) {
            return false;
        }

        // delete if already there
        if ($this->fileExists($field, $public)) {
            
            // delete
            $this->fileDelete($field, $public);
        }

        // set filename for later
        if ($hash === true) {

            // split filename
            $filename_info = pathinfo($request->$field->getClientOriginalName());

            // set
            $this->$field = substr(md5('super salty 99'. $filename_info['filename']), 0, 12) .'.'. $filename_info['extension'];

        // no hash
        } else {

            // set
            $this->$field = $request->$field->getClientOriginalName();
        }

        // must save to receive id
        $this->save();

        // is it public or private folder
        $folder = 'private';
        if ($public) {

            // set public
            $folder = 'public';
        }

        // store file
        $request->$field->storeAs($this->getRelativeStoragePath($field), $this->$field, $folder);

        return true;
    }

    /**
     * delete the file
     *
     * @return this
     */
    public function fileDelete($field, $disk = null)
    {
        // guard if field excist
        if (!$value = $this->$field) {
            return $this;
        }
        // guard delete file and folder, if corrent do cleanup in function
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
