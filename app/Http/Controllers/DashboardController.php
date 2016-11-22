<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\Filesystem;
use Storage;

class DashboardController extends Controller {

    public function index() {
        $sourceFile = "D:\Projects\wamp\www\laraveldemo51\public\images\s3.jpg";
        $targetFile = time() . ".jpg";
        $disk = Storage::disk('s3');
        $disk->put($targetFile, fopen($sourceFile, 'r+'));
    }

}
