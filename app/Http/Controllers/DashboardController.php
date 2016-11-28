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
Use FFMpeg;
use FFMpeg\Media\Video;
use FFMpeg\Media\Frame;

 

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
                $option["public"] = "true";
                $option["url"] = "https://s3.amazonaws.com/testingforresource/".$Outputname.".mp4";
 
                $option["thumbnails"]["label"] = "first";
                $option["thumbnails"]["prefix"] = "thumbs";
                $option["thumbnails"]["interval_in_frames"] = "120";
                $option["thumbnails"]["base_url"] = "https://s3.amazonaws.com/testingforresource/";
                $option["thumbnails"]["size"] = "338*192";
 
                $option["credentials"] = "s3_production";
 
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

                 
                return redirect()->away('https://s3.amazonaws.com/testingforresource/'.$Outputname.'.mp4');
            }
        }
    }

    public function transcode() {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        $data = [];
        $data["input"] = "https://s3.amazonaws.com/testingforresource/sample1.flv";
        $data["outputs"] = [];
        $option = [];
        $option["url"] = "https://s3.amazonaws.com/testingforresource/sample.mp4";
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
    public function getThumbnail(Request $request){
       
        $movie = Input::file('image');
        
        $ffmpeg = \FFMpeg\FFMpeg::create([
            'ffmpeg.binaries'  => 'D:\Projects\wamp\www\laraveldemo51\vendor\bin\ffmpeg.exe',
            'ffprobe.binaries' => 'D:\Projects\wamp\www\laraveldemo51\vendor\bin\ffprobe.exe' 
        ]);
        
        $video = $ffmpeg->open($movie);
        $video
            ->filters()
            ->resize(new FFMpeg\Coordinate\Dimension(50,50))
            ->synchronize();
        
        $video
        ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(2))
        ->save('thubnail-10.jpg');
        
        echo "Thumbnail created successfully";
      
        $file =  public_path('thubnail-10.jpg'); 
        $path1 = public_path('/images/new_file4.jpg');
        $path2 = public_path('/images/new_file5.jpg');
        Image::make($file)->resize(338, 192)->save($path1);
        Image::make($file)->resize(750, 420)->save($path2);
         
    }

    public function welcome(){
        return view('welcome');
    }

    
}

 