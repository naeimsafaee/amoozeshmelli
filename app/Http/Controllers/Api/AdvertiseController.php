<?php

namespace App\Http\Controllers\Api;

use App\Advertise;
use App\Http\Controllers\Controller;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class AdvertiseController extends Controller{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){

        $addvertise = Advertise::all();

        foreach($addvertise as $item) {
            $item->video;
            unset($item["video_id"]);
        }

        return response()->json(["data_count" => $addvertise->count(), "data" => $addvertise], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'string|required',
            'video.*' => 'mimes:mkv,mp4|max:1024000',
            'gift' => 'integer|required',
            'count' => 'integer|required',
            'price' => 'integer|required',
        ], [
            "title.string" => "title is not a string!",
            "title.required" => "title is required!",
            "gift.required" => "gift is required!",
            "count.required" => "count is required!",
            "price.required" => "price is required!",
            "video.mimes" => "unsupported video format",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $video = $request->file('video');
        $video_id = null;
        if($video != null){
            $ext = $video->extension();
            $file_name = time() . mt_rand() . "." . $ext;

            Storage::disk('ftp')->put("advertises/videos/" . $file_name, fopen($video, 'r+'));

            $video_id = Video::create([
                "name" => $file_name,
                "path" => "http://easyno.ir/advertises/videos",
            ])->id;
        }

        Advertise::create([
            "title" => $request->title,
            "video_id" => $video_id,
            "gift" => $request->gift,
            "count" => $request->count,
            "price" => $request->price,
        ]);

        return response()->json(["success" => ["message" => "advertise created successfully"]], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        //
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'title' => 'string|required',
            'video.*' => 'mimes:mkv,mp4|max:1024000',
            'gift' => 'integer|required',
            'count' => 'integer|required',
            'price' => 'integer|required',
        ], [
            "title.string" => "title is not a string!",
            "title.required" => "title is required!",
            "gift.required" => "gift is required!",
            "count.required" => "count is required!",
            "price.required" => "price is required!",
            "video.mimes" => "unsupported video format",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $advertise = Advertise::find($id);
        if($advertise == null)
            return response()->json(["error" => ["message" => "advertise not found!"]], 404);

        if(isset($request->video)){
            $video = $request->file('video');
            $video_id = null;
            if($video != null){
                $ext = $video->extension();
                $file_name = time() . mt_rand() . "." . $ext;

                Storage::disk('ftp')->put("advertises/videos/" . $file_name, fopen($video, 'r+'));

                $video_id = Video::create([
                    "name" => $file_name,
                    "path" => "http://easyno.ir/advertises/videos",
                ])->id;
            } else {
                return response()->json(["error" => ["message" => "advertise video can't be null!"]], 401);
            }
            $advertise->video_id = $video_id;
        }

        $advertise->title = $request->title;
        $advertise->gift = $request->gift;
        $advertise->count = $request->count;
        $advertise->price = $request->price;
        $advertise->save();

        return response()->json(["success" => ["message" => "advertise edited successfully!"]], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id){

        $advertise = Advertise::find($id);
        if($advertise == null)
            return response()->json(["error" => ["message" => "advertise not found!"]], 404);
        $advertise->delete();
        return response()->json(["success" => ["message" => "advertise deleted successfully!"]], 200);
    }
}
