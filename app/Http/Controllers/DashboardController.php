<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\Filesystem;
use Storage;
use Illuminate\Http\Request;
use App\Http\Requests;
use Input;
use File;
use Image;

class DashboardController extends Controller {

    public function index() {
        $sourceFile = public_path("images/sample1.flv");
        $targetFile = time() . ".flv";
        $disk = Storage::disk('s3');
        $return = $disk->put($targetFile, fopen($sourceFile, 'r+'));
        echo '<pre>';
        print_r($return);
        echo "<br>Video Successfully uploaded";
        exit;
    }

    public function upload() {
        return view('uploadfile');
    }

    public function showUploadFile(Request $request){
         $Outputname = e(Input::get('name'));

          if (Input::file()) {
            $file = Input::file('fileimage');
             
            if (isset($file) && !empty($file)) {
                $fileName = $file->getClientOriginalName();                 
                $pathOriginal =  asset("images/" . $fileName); 
                $destinationPath = 'images';
                $file->move($destinationPath,$file->getClientOriginalName());
                echo "File uploaded successfully"; 
                // Get cURL resource
                $curl = curl_init();
                // Set some options - we are passing in a useragent too here
                $data = [];
                $data["input"] =  $pathOriginal;  //"https://s3.amazonaws.com/myresourcegrant/sample1.flv";
                $data["outputs"] = [];
                $option = [];
                $option["url"] = "https://s3.amazonaws.com/testingbucketinexture/".$Outputname.".mp4";
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
                return redirect()->away('https://s3.amazonaws.com/testingbucketinexture/'.$Outputname.'.mp4');
            }
        }
    }

    public function transcode() {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        $data = [];
        $data["input"] = "https://s3.amazonaws.com/myresourcegrant/sample1.flv";
        $data["outputs"] = [];
        $option = [];
        $option["url"] = "https://s3.amazonaws.com/testingbucketinexture/sample.mp4";
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
