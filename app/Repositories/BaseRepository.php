<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

abstract class BaseRepository
{
    /**
     * @var string $modelClass
     */
    protected $modelClass = null;
    /**
     * @var Model $model
     */
    protected $model = null;

    /**
     * @param Model $model
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function all()
    {
        if ($this->modelClass) {
            return $this->modelClass::all();
        }
        return collect([]);
    }

    /**
     * @param Request $request
     * @param string $key
     * @param string $filePath
     * @param bool $public
     * @return false|null|string
     */
    protected function saveFileFromRequest(Request $request, string $key, string $filePath, bool $public = true)
    {
        $path = null;
        if ($request->hasFile($key)) {
            $file = $request->file($key);
            $path = Storage::disk('public')->putFile($filePath, $file, $public);
        }
        return $path;
    }

    /**
     * @param UploadedFile $file
     * @param string $filePath
     * @param bool $public
     * @return mixed
     */
    protected function saveFile(UploadedFile $file, string $filePath, bool $public = true) {
        $path = Storage::disk('public')->putFile($filePath, $file, $public);
        return $path;
    }

}