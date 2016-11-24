<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\Filesystem;
use Storage;

class DashboardController extends Controller {

    public function index() {
        $sourceFile = public_path("images/s3.jpg");
        $targetFile = time() . ".jpg";
        $disk = Storage::disk('s3');
        $return = $disk->put($targetFile, fopen($sourceFile, 'r+'));
        echo '<pre>';
        print_r($return);
        echo "Image Successfully uploaded";
        exit;
    }

}
