<?php

// version 26 added base64 function
// version 25 changed names of variables
// version 24 fixed filesystem paramter for get
// version 23 Added save by file delete
// version 22 Added save by upload
// version 21 Replaced ltrim for basepath function (some strange bug with slash appeared in some cases)
// version 20 Fixed some bugs with deling files
// version 19 Changed behavior it will always default upload it to 'db' storage (if no disk is set)
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
        if (!$id = $this->getRouteKey()) {
            return null;
        }

        // set path and filename
        return $this->getTable() . '/' . $id . '/' . $field;
    }

    /**
     * get storage file path
     * example: assignment_attachments/2/attachment/be0330162f7b.pdf
     */
    public function getDiskFile($field)
    {
        // guard must save first to receive id
        if (!$id = $this->getRouteKey()) {
            return null;
        }
        // guard if field excist
        if (!$value = $this->$field) {
            return null;
        }

        // set path and filename
        return $this->getTable() . '/' . $id . '/' . $field . '/' . $value;
    }

    /**
     * get storage path
     * example: assignment_attachments/2/attachment
     */
    public function getPublicFile($field, $disk = 'db')
    {
        var_dump('add this');
        exit;
    }

    /**
     * get storage path
     * example: /home/vagrant/code/site_name/storage/app/public/assignment_attachments/2/attachment
     */
    public function getSystemPath($field, $disk = 'db')
    {
        // guard must save first to receive id
        if (!$id = $this->getRouteKey()) {
            return null;
        }

        // fall back to default disk
        // $disk = $disk ?? 'db';

        // get the path
        $path = Storage::disk($disk)->path('');

        // set path and filename
        return $path . $this->getTable() . '/' . $id . '/' . $field;
    }

    /**
     * get storage system file path
     * example: /home/vagrant/code/site_name/storage/app/public/assignment_attachments/2/attachment/be0330162f7b.pdf
     */
    public function getSystemFile($field, $disk = 'db')
    {
        // guard must save first to receive id
        if (!$id = $this->getRouteKey()) {
            return null;
        }
        // guard if field excist
        if (!$value = $this->$field) {
            return null;
        }

        // fall back to default disk
        // $disk = $disk ?? 'db';

        // get the path
        $path = Storage::disk($disk)->path('');

        // set path and filename
        return $path . $this->getTable() . '/' . $id . '/' . $field . '/' . $value;
    }

    /**
     * get a protected download link
     */
    public function secureLink($field, $download_route_name = 'downloads')
    {
        // guard must save first to receive id
        if (!$id = $this->getRouteKey()) {
            return null;
        }
        // guard if field exists
        if (!$value = $this->$field) {
            return null;
        }

        $route = route($download_route_name, explode('/', $this->getDiskFile($field)));

        return $route;
    }

    /**
     * deprecated
     */
    public function streamFile($field, $download_name = null, $disk = 'db', $headers = [])
    {
        $this->download($field, $disk, $headers);
    }

    /**
     * download the file
     */
    public function download($field, $download_name = null, $disk = 'db', $headers = [])
    {
        // guard must save first to receive id
        if (!$id = $this->getRouteKey()) {
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
        // $disk = $disk ?? 'db';
        $path = $this->getDiskFile($field);

        return Storage::disk($disk)->download($path, $download_name, $headers);
    }

    /**
     * download the file
     */
    public function file($field, $disk = 'db', $headers = [])
    {
        // guard must save first to receive id
        if (!$id = $this->getRouteKey()) {
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
        // $disk = $disk ?? 'db';
        $path = $this->getSystemFile($field, $disk);

        return Storage::disk($disk)->response($path);
    }

    /**
     * does the file exists
     *
     * @return bool
     */
    public function fileExists($field, $disk = 'db')
    {
        if (!$value = $this->$field) {
            return false;
        }
        if (!Storage::disk($disk)->exists($this->getDiskFile($field))) {
            return false;
        }

        return true;
    }

    /**
     * upload the image
     * This function gives an error if the file is not in the request
     *
     * @return $this
     */
    public function upload($request, $field, $request_field = null, $disk = 'db', $hash = null)
    {
        // fill request field with normal field when empty
        $request_field = $request_field ?? $field;

        // guard file exist
        if (!$request->hasFile($request_field)) {
            abort(503, 'The file does not exists in the request');
        }

        $this->uploadIfInRequest($request, $field, $request_field, $disk, $hash);
    }

    /**
     * upload the image
     * This function ignores the upload by no file
     *
     * @return $this
     */
    public function uploadIfInRequest($request, $field, $request_field = null, $disk = 'db', $hash = null)
    {
        // fill request field with normal field when empty
        $request_field = $request_field ?? $field;

        // guard file exist
        if (!$request->hasFile($request_field)) {
            return $this;
        }
        // guard no id, cannot save
        if (!$this->getRouteKey()) {
            abort(503, 'First save record before saving file.');
        }

        $file = $request->$request_field;

        // delete if already there
        $this->putFile($field, $file, $disk);

        return $this;
    }

    /**
     * Add file from stream etc
     *
     * @return $this
     */
    public function put($filename, $db_field, $file, $disk = 'db')
    {
        // guard no id, cannot save
        if (!$this->getRouteKey()) {
            abort(503, 'First save record before saving file.');
        }

        // delete if already there
        $this->fileDelete($db_field, $disk);

        //
        $path_to_save = $this->getDiskPath($db_field);

        // store file
        $saved_filename_path = Storage::disk($disk)->put($path_to_save . '/' . $filename, $file);
        $saved_filename = $filename;

        // set the filename to the database
        $this->$db_field = $saved_filename;

        // save
        $this->save();

        return $this;
    }

    /**
     * Add file from request
     *
     * @return $this
     */
    public function putFile($field, $file, $disk = 'db')
    {
        // guard no id, cannot save
        if (!$this->getRouteKey()) {
            abort(503, 'First save record before saving file.');
        }

        // delete if already there
        $this->fileDelete($field, $disk);

        //
        $path_to_save = $this->getDiskPath($field);

        // store file
        $saved_filename_path = Storage::disk($disk)->putFile($path_to_save, $file);
        $saved_filename = basename($saved_filename_path);

        // set the filename to the database
        $this->$field = $saved_filename;

        // save
        $this->save();

        return $this;
    }

    /**
     * delete the file
     *
     * @return self
     */
    public function fileDelete($field, $disk = 'db')
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
        if (!Storage::disk($disk)->allFiles($this->getTable() . '/' . $this->getRouteKey())) {

            // delete
            Storage::disk($disk)->deleteDirectory($this->getTable() . '/' . $this->getRouteKey());
        }

        // check if there are other uploaded files otherwise delete table folder
        if (!Storage::disk($disk)->allFiles($this->getTable())) {

            // delete
            Storage::disk($disk)->deleteDirectory($this->getTable());
        }

        // save
        $this->save();

        return $this;
    }

    // only way to get images without needed an url (and be public)
    public function getImageAsBase64($field, $disk = 'db')
    {
        // guard if field exists
        if (!$value = $this->$field) {
            return $this;
        }

        $disk_file = $this->getDiskFile($field);

        $file = Storage::disk($disk)->get($disk_file);

        $file = base64_encode($file);

        return $file;
    }
}
