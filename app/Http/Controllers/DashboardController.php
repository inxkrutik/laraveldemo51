<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\Filesystem;
use Storage;

class DashboardController extends Controller {

    public function index() {
        $sourceFile = public_path("images/sample1.flv");
        $targetFile = time() . ".flv";
        $disk = Storage::disk('s3');
        $return = $disk->put($targetFile, fopen($sourceFile, 'r+'));
        echo '<pre>';
        print_r($return);
        echo "Image Successfully uploaded";
        exit;
    }

    public function transcode() {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        $data = [];
        $data["input"] = "s3://zencodertesting/test.mov";
        $data["outputs"] = [];
        $option = [];
        $option["url"] = "https://s3.amazonaws.com/testingbucketinexture/Samplevideo_".time().".mp4";
        $data["outputs"][] = $option;
        $data = json_encode($data);
        
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://app.zencoder.com/api/v2/jobs',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Zencoder-Api-Key:5531c5a2ed32b130498079ffb1e07943')
        ));
        
        // Send the request & save response to $resp
        $response = curl_exec($curl);
        
        // Close request to clear up some resources
        curl_close($curl);
        
        var_dump($response);
        exit;
    }

}
