<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Image;
use App\Options;
use App\Part;
use App\Rules\persian_date;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class PartController extends Controller{
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
            'section_id' => 'required|integer|exists:sections,id',
            'video.*' => 'mimes:mkv,mp4',
            'question_id' => 'exists:questions,id',
            'order' => 'required|integer',
        ], [
            "section_id.required" => "section_id is required!",
            "section_id.exists" => "section_id does not exist!",
            "video.mimes" => "unsupported video format",
            "question_id.exists" => "question_id does not exist!",
            "order.required" => "order is required!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        if(!isset($request->video))
            if(!isset($request->question_id))
                return response()->json(["error" => ["message" => "video and question id can't be null!"]], 401);

        if(isset($request->video) && isset($request->question_id))
            return response()->json(["error" => ["message" => "video and question id can't be set together!"]], 401);

        $parts = Part::where(["section_id" => $request->section_id, "order" => $request->order])->get();
        if($parts->count() > 0){
            return response()->json([
                "responseCode" => 401,
                'message' => "this part with this order exists!",
            ], 401);
        }

        $video = $request->file('video');
        $video_id = null;
        if($video != null){
            $ext = $video->extension();
            $file_name = time() . mt_rand() . "." . $ext;

            Storage::disk('ftp')->put("parts/videos/" . $file_name, fopen($video, 'r+'));

            $video_id = Video::create([
                "name" => $file_name,
                "path" => "http://easyno.ir/parts/videos",
            ])->id;
        }

        $question_id = null;
        if(isset($request->question_id))
            $question_id = $request->question_id;

        Part::create([
            "section_id" => $request->section_id,
            "video_id" => $video_id,
            "question_id" => $question_id,
            "order" => $request->order,
        ]);

        return response()->json(["success" => ["message" => "part created successfully!"]], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $parts = Part::where("section_id", $id)->get();

        foreach($parts as $part){
            $part->question;
            $part->video;
            $part->section;
            unset($part["video_id"]);
            unset($part["question_id"]);
            unset($part["section_id"]);
        }

        return response()->json(["data_count" => $parts->count(), "data" => $parts], 200);
    }

    public function show_user_part($id){

        $parts = Part::where("section_id", $id)->get();

        foreach($parts as $part){
            $part->question;
            $part->video;
            unset($part["video_id"]);
            unset($part["question_id"]);
            unset($part["section_id"]);
        }

        return response()->json(["data_count" => $parts->count(), "data" => $parts], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'section_id' => 'required|integer|exists:sections,id',
            'video.*' => 'mimes:mkv,mp4',
            'question_id' => 'exists:questions,id',
            'order' => 'required|integer',
        ], [
            "section_id.required" => "section_id is required!",
            "section_id.exists" => "section_id does not exist!",
            "video.mimes" => "unsupported video format",
            "question_id.exists" => "question_id does not exist!",
            "order.required" => "order is required!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        if(!isset($request->video))
            if(!isset($request->question_id))
                return response()->json(["error" => ["message" => "video and question id can't be null!"]], 401);

        if(isset($request->video) && isset($request->question_id))
            return response()->json(["error" => ["message" => "video and question id can't be set together!"]], 401);

        $parts = Part::where(["section_id" => $request->section_id, "order" => $request->order])->first();
        if($id != $parts->id)
            if($parts->count() > 0){
                return response()->json([
                    "responseCode" => 401,
                    'message' => "this part with this order exists!",
                ], 401);
            }

        $video = $request->file('video');
        $video_id = null;
        if($video != null){
            $ext = $video->extension();
            $file_name = time() . mt_rand() . "." . $ext;

            Storage::disk('ftp')->put("parts/videos/" . $file_name, fopen($video, 'r+'));

            $video_id = Video::create([
                "name" => $file_name,
                "path" => "http://easyno.ir/parts/videos/",
            ])->id;
        }

        $question_id = null;
        if(isset($request->question_id))
            $question_id = $request->question_id;

        $part = Part::find($id);
        $part->section_id = $request->section_id;
        $part->video_id = $video_id;
        $part->question_id = $question_id;
        $part->order = $request->order;
        $part->save();

        return response()->json(["success" => ["message" => "part successfully edited!"]], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id){
        $part = Part::find($id);
        if($part == null)
            return response()->json(["error" => ["message" => "part not found!"]], 404);
        $part->delete();
        return response()->json(["success" => ["message" => "part deleted successfully!"]], 200);
    }
}
