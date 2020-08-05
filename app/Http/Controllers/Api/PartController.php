<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Image;
use App\Options;
use App\Part;
use App\Rules\persian_date;
use App\Section;
use App\UserToQuizzesBoughts;
use App\UserToSections;
use App\Video;
use DateTime;
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
            //            'video.*' => 'mimes:mkv,mp4',
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

        if(!isset($request->video_name))
            if(!isset($request->question_id))
                return response()->json(["error" => ["message" => "video and question id can't be null!"]], 401);

        if(isset($request->video_name) && isset($request->question_id))
            return response()->json(["error" => ["message" => "video and question id can't be set together!"]], 401);

        $parts = Part::where(["section_id" => $request->section_id, "order" => $request->order])->get();
        if($parts->count() > 0){
            return response()->json([
                "responseCode" => 401,
                'message' => "this part with this order exists!",
            ], 401);
        }

        /*$video = $request->file('video');
        $video_id = null;
        if($video != null){
            $ext = $video->extension();
            $file_name = time() . mt_rand() . "." . $ext;

            Storage::disk('ftp')->put("parts/videos/" . $file_name, fopen($video, 'r+'));


        }*/

        $video_id = null;
        if(isset($request->video_name))
            $video_id = Video::create([
                "name" => $request->video_name,
                "path" => $request->video_path,
            ])->id;

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

    public function show_user_part(Request $request , $id){

        $parts = Part::where("section_id", $id)->orderby("order", "ASC")->get();

        $user = $request->user();

        $i = 0;
        foreach($parts as $part){
            $part->question;
            $part->video;

            if($part["question"] != null){
                $question = $part["question"];
                $question->image;
                $question->options;

                $part["is_video"] = false;

                $options = $question["options"];

                foreach($options as $option){
                    $option->image;
                    $option["image_url"] = $option["image"]["url"];

                    unset($option["image"]);
                    unset($option["image_id"]);
                    unset($option["question_id"]);

                    if($option["is_correct"] == "0")
                        $option["is_correct"] = false; else
                        $option["is_correct"] = true;
                }


                $question["image_url"] = $question["image"]["url"];

                unset($question["image_id"]);
                unset($question["image"]);
            } else {

                $part["video_url"] = $part["video"]["url"];
                $part["is_video"] = true;
                unset($part["video"]);
            }

            if($i == $parts->count() - 1){
                $part["has_ended"] = true;
            } else {
                $part["has_ended"] = false;
            }


            $i++;
            unset($part["video_id"]);
            unset($part["question_id"]);
            unset($part["section_id"]);
        }

        $section = Section::find($id);
        $section->subject->lesson;
        $section->quiz;

        $quiz = $section["quiz"];

        $now = new DateTime();
        $now = strtotime($now->format('Y-m-d'));

        if($section["quiz"]["questions"] != null){
            $quiz->questions;
            $questions = $section["quiz"]["questions"];

            $expire_time = strtotime($section["quiz"]["quiz_date"]);

            if($expire_time <= $now)
                $section["quiz"]["is_locked"] = false;
            else
                $section["quiz"]["is_locked"] = true;

            $ser_to_quizzes = UserToQuizzesBoughts::where(["quiz_id" => $section["quiz"]["id"], "user_id" => $user["id"]])->get();
            if($ser_to_quizzes->count() == 0)
                $section["quiz"]["has_paid"] = false;
            else
                $section["quiz"]["has_paid"] = true;

            foreach($questions as $question){
                $question->image;
                $question->options;

                $question["image_url"] = $question["image"]["url"];

                $options = $question["options"];
                foreach($options as $option){
                    $option->image;
                    $option["image_url"] = $option["image"]["url"];

                    unset($option["image_id"]);
                    unset($option["image"]);
                }
                unset($question["image_id"]);
                unset($question["image"]);
            }

        }

        if($section["quiz"] == null)
            $section["quiz"] = [];

        $user = $request->user();
        $user_to_section = UserToSections::where(["section_id" => $section["id"], "user_id" => $user["id"]])->get();
        if($user_to_section->count() == 0)
            $section["has_paid"] = false;
        else
            $section["has_paid"] = true;

        return response()->json([
            "data_count" => $parts->count(),
            "data" => $parts,
            "section_name" => $section["title"],
            "subject_name" => $section["subject"]["title"],
            "lesson_name" => $section["subject"]["lesson"]["title"],
            "lesson_id" => $section["subject"]["lesson"]["id"],
            "quiz" => $section["quiz"],
            "has_paid" => $section["has_paid"]
        ], 200);
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

        if(!isset($request->video_name))
            if(!isset($request->question_id))
                return response()->json(["error" => ["message" => "video and question id can't be null!"]], 401);

        if(isset($request->video_name) && isset($request->question_id))
            return response()->json(["error" => ["message" => "video and question id can't be set together!"]], 401);

        $parts = Part::where(["section_id" => $request->section_id, "order" => $request->order])->first();
        if($id != $parts->id)
            if($parts->count() > 0){
                return response()->json([
                    "responseCode" => 401,
                    'message' => "this part with this order exists!",
                ], 401);
            }

        $video_id = Video::create([
            "name" => $request->video_name,
            "path" => $request->video_path,
        ])->id;

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
