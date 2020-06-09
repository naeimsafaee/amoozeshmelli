<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Image;
use App\Options;
use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOption\Option;
use Validator;

class OptionController extends Controller{
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
            'title' => 'string',
            'image' => 'image',
            'question_id' => 'required|integer|exists:questions,id',
            'is_correct' => 'required|integer',
        ], [
            "title.required" => "title is required!",
            "image.image" => "image is not an image!",
            "question_id.required" => "question_id is required!",
            "question_id.exists" => "question_id does not exist!",
            "is_correct.required" => "is_correct is required!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        if($request->is_correct == 1){
            $options = Options::where(["question_id" => $request->question_id, "is_correct" => 1])->get();

            if($options->count() > 0){
                return response()->json([
                    "responseCode" => 401,
                    'message' => "this question have a correct option now!",
                ], 401);
            }
        }


        $file = $request->file('image');
        $image_id = null;
        if($file != null){
            $ext = $file->extension();
            $file_name = time() . mt_rand() . "." . $ext;

            Storage::disk('ftp')->put("options/images/" . $file_name, fopen($file, 'r+'));

            $image_id = Image::create([
                "name" => $file_name,
                "path" => "http://easyno.ir/options/images",
            ])->id;
        }

        $options = Options::create([
            "title" => $request->title,
            "image_id" => $image_id,
            "question_id" => $request->question_id,
            "is_correct" => $request->is_correct,
        ]);

        return response()->json(["success" => ["message" => "option created successfully!"]], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $options = Options::where("question_id", $id)->get();

        $main_option = [];
        $question = null;
        foreach($options as $option){
            $option->image;
            $option->question;
            $question = $option["question"];

            unset($option["image_id"]);
            unset($option["question_id"]);
            unset($option["question"]);
            $main_option[] = $option;
        }

        if($question != null){
            $question->quiz;
            $question->lesson;
            $question->subject;
            $question->image;

            $question["image_url"] = $question["image"]["url"];
            $question["quiz_title"] = $question["quiz"]["title"];

            unset($question["image_id"]);
            unset($question["image"]);
            unset($question["quiz_id"]);
            unset($question["quiz"]);
            unset($question["lesson_id"]);
            unset($question["subject_id"]);
        }

        return response()->json([
            "data_count" => $options->count(),
            "data" => ["options" => $main_option, "question" => $question],
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
            'title' => 'string',
            'image' => 'image',
            'need_delete_image' => 'integer',
            'question_id' => 'integer|exists:questions,id',
            'is_correct' => 'required|integer',
        ], [
            "title.required" => "title is required!",
            "image.image" => "image is not an image!",
            "question_id.required" => "question_id is required!",
            "question_id.exists" => "question_id does not exist!",
            "is_correct.required" => "is_correct is required!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $option = Options::find($id);
        if($option == null){
            return response()->json(["error" => ["message" => "option not found!"]], 404);
        }

        if($request->is_correct == 1){
            $options = Options::where(["question_id" => $request->question_id, "is_correct" => 1])->get();

            if($options->count() > 0){
                return response()->json([
                    "responseCode" => 401,
                    'message' => "this question have a correct option now!",
                ], 401);
            }
        }

        if($request->has("need_delete_image")){
            $option->image_id = null;
        } else {
            if($request->has("image")){
                $file = $request->file('image');
                $image_id = null;
                if($file != null){
                    $ext = $file->extension();
                    $file_name = time() . mt_rand() . "." . $ext;

                    Storage::disk('ftp')->put("options/images/" . $file_name, fopen($file, 'r+'));

                    $image_id = Image::create([
                        "name" => $file_name,
                        "path" => "http://easyno.ir/options/images",
                    ])->id;
                }
                $option->image_id = $image_id;
            }
        }

        if($request->has("title"))
            $option->title = $request->title;
        if($request->has("question_id"))
            $option->question_id = $request->question_id;
        $option->is_correct = $request->is_correct;
        $option->save();

        return response()->json(["success" => ["message" => "option successfully edited!"]], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){

        $option = Options::find($id);
        if($option == null)
            return response()->json(["error" => ["message" => "option not found!"]], 404);
        $option->delete();
        return response()->json(["success" => ["message" => "option deleted successfully!"]], 200);
    }
}
