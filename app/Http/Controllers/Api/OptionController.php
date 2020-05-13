<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Image;
use App\Options;
use App\Question;
use Illuminate\Http\Request;
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

            $path = public_path('images/options/');
            $file->move($path, $file_name);
            $image_id = Image::create([
                "name" => $file_name,
                "path" => url('/images/options/'),
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
        $question = [];
        foreach($options as $option){
            $option->image;
            $option->question;
            $question = $option["question"];

            unset($option["image_id"]);
            unset($option["question_id"]);
            unset($option["question"]);
            $main_option[] = $option;
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        //
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
