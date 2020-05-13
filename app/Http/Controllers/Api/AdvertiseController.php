<?php

namespace App\Http\Controllers\Api;

use App\Advertise;
use App\Http\Controllers\Controller;
use App\Video;
use Illuminate\Http\Request;
use Validator;

class AdvertiseController extends Controller{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(){
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'string|required',
            'video.*' => 'mimes:mkv,mp4',
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

            $path = public_path('video/');
            $video->move($path, $file_name);
            $video_id = Video::create([
                "name" => $file_name,
                "path" => $path,
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        //
    }
}
