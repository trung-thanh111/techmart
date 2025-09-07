<?php
namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface ImageServiceInterface {
    public function upload($files, $folder, $pipeline, $overrideOptions);
}
