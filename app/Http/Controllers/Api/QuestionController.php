<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Image;
use App\Question;
use App\Rules\persian_date;
use App\Section;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Validator;

class QuestionController extends Controller{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(){

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
            'quiz_id' => 'integer|exists:quizzes,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'answer_file' => 'file',
        ], [
            "title.required" => "title is required!",
            "image.image" => "image is not an image!",
            "quiz_id.required" => "quiz_id is required!",
            "quiz_id.exists" => "quiz_id does not exist!",
            "lesson_id.required" => "lesson_id is required!",
            "lesson_id.exists" => "lesson_id does not exist!",
            "subject_id.required" => "subject_id is required!",
            "subject_id.exists" => "subject_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        if(!isset($request->title))
            if(!isset($request->image))
                return response()->json(["error" => ["message" => "title and image can't be null!"]], 401);


        $image = $request->file('image');
        $image_id = null;
        if($image != null){
            $ext = $image->extension();
            $file_name = time() . mt_rand() . "." . $ext;

            Storage::disk('ftp')->put("questions/images/" . $file_name, fopen($image, 'r+'));

            $image_id = Image::create([
                "name" => $file_name,
                "path" => "http://easyno.ir/questions/images",
            ])->id;
        }


        $answer_file_path = null;
        $file = $request->file('answer_file');
        if($file != null){
            $ext = $file->extension();
            $file_name = time() . mt_rand() . "." . $ext;

            Storage::disk('ftp')->put("questions/files/" . $file_name, fopen($file, 'r+'));

            $answer_file_path = "http://easyno.ir/questions/files/" . $file_name;
        }

        $question = Question::create([
            "title" => $request->title,
            "image_id" => $image_id,
            "quiz_id" => $request->quiz_id,
            "lesson_id" => $request->lesson_id,
            "subject_id" => $request->subject_id,
            "answer_file" => $answer_file_path,
        ]);

        return response()->json([
            "success" => [
                "message" => "question created successfully!",
                "question_id" => $question->id,
            ],
        ], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $questions = Question::where("subject_id", $id)->get();

        foreach($questions as $question){
            //            $question->quiz;
            $question->lesson;
            $question->subject;
            $question->image;
            //            unset($question->quiz_id);
            unset($question->lesson_id);
            unset($question->subject_id);
            unset($question->image_id);
        }

        return response()->json(["data_count" => $questions->count(), "data" => $questions], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'title' => 'string|nullable',
            'image' => 'image',
            'need_delete_image' => 'integer',
            'quiz_id' => 'integer|exists:quizzes,id',
            'lesson_id' => 'integer|exists:lessons,id',
            'subject_id' => 'integer|exists:subjects,id',
            'answer_file' => 'file',
        ], [
            "title.required" => "title is required!",
            "image.image" => "image is not an image!",
            "quiz_id.required" => "quiz_id is required!",
            "quiz_id.exists" => "quiz_id does not exist!",
            "lesson_id.required" => "lesson_id is required!",
            "lesson_id.exists" => "lesson_id does not exist!",
            "subject_id.required" => "subject_id is required!",
            "subject_id.exists" => "subject_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $question = Question::find($id);
        if($question == null){
            return response()->json(["error" => ["message" => "question not found!"]], 404);
        }

        if($request->has("need_delete_image")){
            $question->image_id = null;
        } else {
            if($request->has("image")){
                $image_id = null;

                $image = $request->file('image');
                if($image != null){
                    $ext = $image->extension();
                    $file_name = time() . mt_rand() . "." . $ext;

                    Storage::disk('ftp')->put("questions/images/" . $file_name, fopen($image, 'r+'));

                    $image_id = Image::create([
                        "name" => $file_name,
                        "path" => "http://easyno.ir/questions/images",
                    ])->id;
                }
                $question->image_id = $image_id;
            }
        }

        if($request->has("answer_file")){
            $answer_file_path = null;
            $file = $request->file('answer_file');
            if($file != null){
                $ext = $file->extension();
                $file_name = time() . mt_rand() . "." . $ext;

                Storage::disk('ftp')->put("questions/files/" . $file_name, fopen($file, 'r+'));

                $answer_file_path = "http://easyno.ir/questions/files/" . $file_name;
            }
            $question->answer_file = $answer_file_path;
        }

        if($request->has("title"))
            $question->title = $request->title;
        if($request->has("quiz_id"))
            $question->quiz_id = $request->quiz_id;
        if($request->has("lesson_id"))
            $question->lesson_id = $request->lesson_id;
        if($request->has("subject_id"))
            $question->subject_id = $request->subject_id;

        $question->save();

        return response()->json(["success" => ["message" => "question successfully edited!"]], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){

        $question = Question::find($id);
        if($question == null)
            return response()->json(["error" => ["message" => "question not found!"]], 404);
        $question->delete();
        return response()->json(["success" => ["message" => "question deleted successfully!"]], 200);
    }
}
