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
use FFMpeg;
use FFMpeg\Media\Video;
use FFMpeg\Media\Frame;

class DashboardController extends Controller {

    public function index() {

        $disk = Storage::disk('s3');

        $targetFile1 = "/post/" . time() . "_s3.jpg";
        $image1 = Image::make($sourceFile)->fit(300, 300);
        $disk->put($targetFile1, $image1, 'public');

        $targetFile2 = "/post/thumbnail/" . time() . "_s3.jpg";
        $image2 = Image::make($sourceFile)->fit(300, 300);
        $disk->put($targetFile2, $image2, 'public');

        //$return = $disk->put($targetFile, fopen($sourceFile, 'r+'));
        echo '<pre>';
        print_r($return);
        echo "<br>File Successfully uploaded";
        exit;
    }

    public function uploadImage() {
        return view('uploadImage');
    }

    public function uploadFileToS3(Request $request) {
        $s3 = Storage::disk('s3');

        $image_file_name = time() . '.jpg';
        $file_path1 = '/post/' . $request->name . '/';
        $file_path2 = '/post/thumbnail/' . $request->name . '/';

        $s3->put($file_path1.'original_'.$image_file_name, file_get_contents($request->file('fileimage')), 'public');

        $image_thumb = Image::make($request->file('fileimage'))->fit(100, 100);
        $image_thumb = $image_thumb->stream();
        $s3->put($file_path2.'medium_'.$image_file_name, $image_thumb->__toString(), 'public');
        
        return json_encode(array(
            'filename' => $image_file_name
        ));
    }

    public function upload() {
        return view('uploadfile');
    }

    public function showUploadFile(Request $request) {
        $Outputname = e(Input::get('name'));

        if (Input::file()) {
            $file = Input::file('fileimage');

            if (isset($file) && !empty($file)) {
                $fileName = $file->getClientOriginalName();
                $pathOriginal = asset("images/" . $fileName);
                $destinationPath = 'images';
                $file->move($destinationPath, $file->getClientOriginalName());
                echo "File uploaded successfully";
                // Get cURL resource
                $curl = curl_init();
                // Set some options - we are passing in a useragent too here
                $data = [];
                $data["input"] = $pathOriginal;  //"https://s3.amazonaws.com/myresourcegrant/sample1.flv";
                $data["outputs"] = [];
                $option = [];
                $option["public"] = true;
                $option["url"] = "https://s3.amazonaws.com/testingforresource/" . $Outputname . ".mp4";
                $option["credentials"] = "s3_production";

                /* $thumbnail = [];
                  $thumbnail["label"] = "first";
                  $thumbnail["prefix"] = "thumbs";
                  $thumbnail["interval_in_frames"] = "120";
                  $thumbnail["base_url"] = "https://s3.amazonaws.com/testingforresource/";
                  $thumbnail["size"] = "338*192";
                  $option["thumbnails"][] = $thumbnail; */

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


                return redirect()->away('https://s3.amazonaws.com/testingforresource/' . $Outputname . '.mp4');
            }
        }
    }

    public function transcode() {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        $data = [];
        $data["input"] = "https://s3.amazonaws.com/mye3/sample1.flv";

        $data["outputs"] = [];
        $option = [];
        $option["public"] = true;
        $option["url"] = "https://s3.amazonaws.com/mye3/sample1.mp4";

        //$option["thumbnails"] = [];
        $thumbnail = [];
        $thumbnail["filename"] = "thumb_".time();
        $thumbnail["number"] = 1;
        $thumbnail["base_url"] = "https://s3.amazonaws.com/mye3/";
        $thumbnail["width"] = 338;
        $thumbnail["height"] = 192;
        $thumbnail["public"] = "true";
        $option["thumbnails"] = $thumbnail;

        $data["outputs"][] = $option;

        $data = json_encode($data);

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://app.zencoder.com/api/v2/jobs',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Zencoder-Api-Key:1db2bc9a82f159a82e33e7a4f7854c15')
        ));

        // Send the request & save response to $resp
        $response = curl_exec($curl);

        // Close request to clear up some resources
        curl_close($curl);

        var_dump($response);
        exit;
    }

    public function getThumbnail(Request $request) {

        $movie = Input::file('image');

        $ffmpeg = \FFMpeg\FFMpeg::create([
                    'ffmpeg.binaries' => 'D:\Projects\wamp\www\laraveldemo51\vendor\bin\ffmpeg.exe',
                    'ffprobe.binaries' => 'D:\Projects\wamp\www\laraveldemo51\vendor\bin\ffprobe.exe'
        ]);

        $video = $ffmpeg->open($movie);
        $video
                ->filters()
                ->resize(new FFMpeg\Coordinate\Dimension(50, 50))
                ->synchronize();

        $video
                ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(2))
                ->save('thubnail-10.jpg');

        echo "Thumbnail created successfully";

        $file = public_path('thubnail-10.jpg');
        $path1 = public_path('/images/new_file4.jpg');
        $path2 = public_path('/images/new_file5.jpg');
        Image::make($file)->resize(338, 192)->save($path1);
        Image::make($file)->resize(750, 420)->save($path2);
    }

    public function welcome() {
        return view('welcome');
    }

}
