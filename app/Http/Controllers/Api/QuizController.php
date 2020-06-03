<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lesson;
use App\Question;
use App\Quiz;
use App\Rules\persian_date;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;


class QuizController extends Controller{

    public function show_quiz(Request $request){
        $quizzes = Quiz::Paginate(10);

        $collection = $quizzes->getCollection();

        return response()->json([
            "data_count" => count($collection),
            "current_page" => (int)$request->page,
            "total_count" => $quizzes->total(),
            "total_pages" => ceil($quizzes->total() / 10),
            "data" => $quizzes,
        ], 200);
    }

    public function search_quiz(Request $request){
        $quizzes = Quiz::where("title", "like", "%" . $request->title . "%")->get();

        //        $collection = $quizzes->getCollection();

        return response()->json([
            "data_count" => $quizzes->count(),
            "data" => $quizzes,
        ], 200);
    }

    public function make_quiz(Request $request){
        $validator = Validator::make($request->all(), [
            'lesson_id' => "integer|required|exists:lessons,id",
            'how_many' => "integer|required",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),
            ], 401);
        }

        if($request->has("subject_id")){
            $question = Question::where(["lesson_id" => $request->lesson_id, "subject_id" => $request->subject_id]);
        } else {
            $question = Question::where(["lesson_id" => $request->lesson_id]);
        }

        $question->get();


        return response()->json(["data_count" => $question->count() , "data" => $question] ,200);
    }

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
            'quiz_title' => 'required|string',
            'quiz_date' => ["required", new persian_date()],
            'quiz_time' => "integer|required",
            'award' => 'required|integer',
            'price' => 'required|integer',
            'gift_price' => 'required|integer',
            'early_price' => 'required|integer',
            'answer_file' => 'file',
        ], [
            "quiz_title.required" => "quiz_title is required!",
            "quiz_date.required" => "quiz_date is required!",
            "quiz_time.required" => "quiz_time is required!",
            "award.required" => "award is required!",
            "price.required" => "price is required!",
            "gift_price.required" => "gift_price is required!",
            "early_price.required" => "early_price is required!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $quiz_date = explode("/", $request->quiz_date);

        $quiz_date = jalali_to_gregorian($quiz_date[0], $quiz_date[1], $quiz_date[2]);
        $quiz_date = implode("-", $quiz_date);

        $answer_file_path = null;
        $file = $request->file('answer_file');
        if($file != null){
            $ext = $file->extension();
            $file_name = time() . mt_rand() . "." . $ext;

            Storage::disk('ftp')->put("quizzes/files/" . $file_name, fopen($file, 'r+'));

            $answer_file_path = "http://easyno.ir/quizzes/files/" . $file_name;
        }

        $new_quiz = Quiz::create([
            "title" => $request->quiz_title,
            "quiz_date" => $quiz_date,
            "quiz_time" => $request->quiz_time,
            "award" => $request->award,
            "price" => $request->price,
            "gift_price" => $request->gift_price,
            "early_price" => $request->early_price,
            "answer_file" => $answer_file_path,
        ]);
        return response()->json([
            "success" => [
                "message" => "quiz created succhessfully!",
                "quiz_id" => $new_quiz->id,
            ],
        ], 200);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){

        $quiz = Quiz::find($id);
        if($quiz == null){
            return response()->json(["error" => ["message" => "quiz not found!"]], 404);
        }
        $quiz->delete();
        return response()->json(["success" => ["message" => "quiz removed successfully!"]], 200);
    }

}
