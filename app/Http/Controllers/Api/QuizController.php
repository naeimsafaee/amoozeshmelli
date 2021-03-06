<?php

namespace App\Http\Controllers\Api;

use App\Finance;
use App\Http\Controllers\Controller;
use App\Lesson;
use App\PeopleToPercent;
use App\Question;
use App\Quiz;
use App\Rules\persian_date;
use App\Section;
use App\User;
use App\UserToQuizzes;
use App\UserToQuizzesBoughts;
use App\UserToSections;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator;


class QuizController extends Controller{

    public function buy_quiz(Request $request){

        $user = $request->user();

        $quiz = Quiz::find($request->quiz_id);

        $now = new DateTime();
        $now = strtotime($now->format('Y-m-d'));

        $expire_time = strtotime($quiz["opening_date"]);

        if($expire_time <= $now)
            $status = 0;
        else
            $status = 1;

        $price = $quiz["price"];
        $gift_price = $quiz["gift_price"];

        $early_price = $quiz["early_price"];

        $current_user = User::find($user["id"]);

        if($status == 0){

            if($user["wallet"] < $price){
                return response()->json([
                    "error" => "موجودی کیف پول کافی نمی باشد!",
                ], 200);
            }
            if($user["gift_wallet"] < $gift_price){
                return response()->json([
                    "error" => "موجودی کیف پول هدیه کافی نمی باشد!",
                ], 200);
            }

            $current_user->wallet -= $price;
            $current_user->gift_wallet -= $gift_price;

        } elseif($status == 1) {
            if($user["wallet"] < $early_price){
                return response()->json([
                    "error" => "موجودی کیف پول کافی نمی باشد!",
                ], 200);
            }
            $current_user->wallet -= $early_price;
        }

        $main_price = $current_user->wallet;
        if($main_price != 0){
            $peoples = PeopleToPercent::query()
                ->where("quiz_id", $request->quiz_id)->get();

            foreach($peoples as $people){

                $main_price = $main_price * $people["percent"] / 100;

                Finance::query()->create([
                    "people_id" => $people["people_id"],
                    "city_id" => $current_user->city_id,
                    "price" => $main_price,
                    "info" => "خرید آزمون " . $quiz["title"],
                ]);
            }
        }

        $current_user->save();

        UserToQuizzesBoughts::create([
            "user_id" => $user["id"],
            "quiz_id" => $request->quiz_id,
        ]);
        return response()->json(["data" => "عملیات خرید با موفقیت انجام شد!"], 200);
    }

    public function quiz_correction(Request $request){

        $question_ids = explode(",", $request->questions);
        $answers = explode(",", $request->answers);

        $quiz_id = $request->quiz_id;

        $i = 0;
        $correct = 0;
        $wrong = 0;
        foreach($question_ids as $question_id){
            $question = Question::find($question_id);
            $question->options;

            $answer = $answers[$i];

            $options = $question["options"];
            foreach($options as $option){
                if($option["id"] == $answer){

                    if($option["is_correct"] == 0){
                        $wrong++;
                    } else {
                        $correct++;
                    }
                }
            }
            $i++;
        }

        $fomula = (($correct * 3) - $wrong) / (count($question_ids) * 3);
        $fomula *= 100;

        $user = $request->user();

        $answer_file = "null";
        if($quiz_id != 0){
            $user = User::find($user["id"]);

            $quiz = Quiz::find($quiz_id);
            $answer_file = $quiz->answer_file;
            $quiz_id = $quiz->id;

            $user_to_quizzes = UserToQuizzes::where(["user_id" => $user->id, "quiz_id" => $quiz_id])->get();

            if($user_to_quizzes->count() > 0){

            } else {
                if($fomula > 0)
                    $user->gift_wallet = $user->gift_wallet + ceil(($quiz->award * $fomula / 100));
                UserToQuizzes::create(["user_id" => $user->id, "quiz_id" => $quiz_id]);
            }

            $user->save();

        }

        return response()->json(["darsad" => round($fomula, 2), "answer_file" => $answer_file], 200);
    }

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

        $question = $question->inRandomOrder()->limit($request->how_many)->get();

        /*if(Auth::user()->id == 1){
            $question[] = Question::find(3229);
        }  */

        foreach($question as $q){
            $q->image;
            $options = $q->options;

            foreach($options as $option){
                $option->image;

                $option["image_url"] = $option["image"]["url"];
                unset($option["image"]);
            }

            $q["image_url"] = $q["image"]["url"];
            unset($q["image"]);
            unset($q["image_id"]);
        }

        return response()->json(["data_count" => $question->count(), "data" => $question], 200);
    }

    public function get_complete_quiz(Request $request){

        $sections = Section::where("quiz_id", "<>", "")->get();

        $quiz_ids = [];

        foreach($sections as $section){
            $quiz_ids[] = $section["quiz_id"];
        }

        $quizzes = Quiz::whereNotIn('id', $quiz_ids)->get();

        $now = new DateTime();
        $now = strtotime($now->format('Y-m-d'));

        $MainQuiz = [];
        foreach($quizzes as $quiz){

            $expire_time = strtotime($quiz["quiz_date"]);

            $questions = Question::where("quiz_id" , $quiz["id"])->get();
            if($questions->count() == 0){
                continue;
            }
            if($quiz["quiz_time"] == "0"){
                continue;
            }

            if($expire_time < $now)
                $quiz["is_locked"] = false;
            else
                $quiz["is_locked"] = true;

            $user = $request->user();

            $user_to_quizzes_boughts = UserToQuizzesBoughts::where(["user_id" => $user["id"] , "quiz_id" => $quiz["id"]])->get();
            $has_paid = false;
            if($user_to_quizzes_boughts->count() > 0)
                $has_paid = true;

            $quiz["has_paid"] = $has_paid;

            $MainQuiz[] = $quiz;
        }

        return response()->json(["data_count" => count($MainQuiz), "data" => $MainQuiz], 200);
    }

    public function show_questions_of_quiz(Request $request , $id){

        $quiz = Quiz::find($id);
        if($quiz == null)
            return response()->json(["error" => ["message" => "quiz not found!"]], 404);

        $questions = Question::where("quiz_id", $id)->get();

        $lesson_id = 0;
        foreach($questions as $question){
            $question->image;
            $question->lesson;
            $options = $question->options;

            foreach($options as $option){
                $option->image;

                $option["image_url"] = $option["image"]["url"];
                unset($option["image"]);
            }

            if($question["lesson_id"] != $lesson_id){
                $question["is_new_tab"] = true;
            } else {
                $question["is_new_tab"] = false;
            }

            $question["lesson_name"] = $question["lesson"]["title"];

            $lesson_id = $question["lesson_id"];
            $question["image_url"] = $question["image"]["url"];

            unset($question["image"]);
            unset($question["lesson"]);
            unset($question["image_id"]);
        }

        $user = $request->user();

        $user_to_quizzes_boughts = UserToQuizzesBoughts::where(["user_id" => $user["id"] , "quiz_id" => $id])->get();
        $has_paid = false;
        if($user_to_quizzes_boughts->count() > 0)
            $has_paid = true;

        return response()->json(["has_paid" => $has_paid , "quiz" => $quiz , "data_count" => $questions->count(), "data" => $questions], 200);
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
