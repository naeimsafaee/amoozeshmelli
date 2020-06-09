<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rules\persian_date;
use App\Section;
use App\User;
use App\UserToSections;
use DateTime;
use Illuminate\Http\Request;
use Validator;

class SectionController extends Controller{

    public function section_with_teacher_and_subject(Request $request){

        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|integer|exists:teachers,id',
            'subject_id' => 'required|integer|exists:subjects,id',
        ], [
            "teacher_id.required" => "teacher_id is required!",
            "subject_id.required" => "subject_id is required!",
            "teacher_id.exists" => "teacher_id does not exist!",
            "subject_id.exists" => "subject_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $sections = Section::where([
            "teacher_id" => $request->teacher_id,
            "subject_id" => $request->subject_id,
        ])->select("id", "title", "price", "gift_price", "early_price", "quiz_id", "award", "helper_award", "pre_section_id", "opening_date", "can_pass")->get();

        $now = new DateTime();
        $now = strtotime($now->format('Y-m-d'));

        $user = $request->user();

        foreach($sections as $section){

            $expire_time = strtotime($section["opening_date"]);

            if($expire_time <= $now)
                $section["is_locked"] = false;
            else
                $section["is_locked"] = true;

            $user_to_section = UserToSections::where(["section_id" => $section["id"], "user_id" => $user["id"]])->get();
            if($user_to_section->count() == 0)
                $section["has_paid"] = false;
            else
                $section["has_paid"] = true;

        }

        return response()->json(["data_count" => $sections->count(), "data" => $sections], 200);
    }

    public function buy_section(Request $request){

        $user = $request->user();

        $section = Section::find($request->section_id);

        $now = new DateTime();
        $now = strtotime($now->format('Y-m-d'));

        $expire_time = strtotime($section["opening_date"]);

        if($expire_time < $now)
            $status = 0;
        else
            $status = 1;


        $price = $section["price"];
        $gift_price = $section["gift_price"];

        $early_price = $section["early_price"];

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

        $current_user->save();

        UserToSections::create([
            "user_id" => $user["id"],
            "section_id" => $request->section_id,
        ]);
        return response()->json(["data" => "عملیات خرید با موفقیت انجام شد!"], 200);
    }

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
            'title' => 'required|string',
            'price' => 'required|integer',
            'gift_price' => 'required|integer',
            'early_price' => 'required|integer',
            'award' => 'required|integer',
            'helper_award' => 'required|integer',
            'teacher_id' => 'required|integer|exists:teachers,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            //            'quiz_id' => 'exists:quizzes,id',
            'pre_section_id' => 'integer|exists:sections,id',
            'can_pass' => 'integer|required',
            'opening_date' => ["required", new persian_date()],
        ], [
            "title.required" => "title is required!",
            "price.required" => "price is required!",
            "gift_price.required" => "gift_price is required!",
            "early_price.required" => "early_price is required!",
            "award.required" => "award is required!",
            "helper_award.required" => "helper_award is required!",
            "teacher_id.required" => "teacher_id is required!",
            "subject_id.required" => "subject_id is required!",
            "quiz_id.required" => "quiz_id is required!",
            "opening_date.required" => "opening_date is required!",
            "teacher_id.exists" => "teacher_id does not exist!",
            "subject_id.exists" => "subject_id does not exist!",
            "quiz_id.exists" => "quiz_id does not exist!",
            "pre_section_id.exists" => "pre_section_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $pre_section_id = null;
        if(isset($request->pre_section_id)){
            $pre_section_id = $request->pre_section_id;
        }

        $opening_date = explode("/", $request->opening_date);

        $opening_date = jalali_to_gregorian($opening_date[0], $opening_date[1], $opening_date[2]);
        $opening_date = implode("-", $opening_date);

        Section::create([
            "title" => $request->title,
            "price" => $request->price,
            "gift_price" => $request->gift_price,
            "early_price" => $request->early_price,
            "award" => $request->award,
            "helper_award" => $request->helper_award,
            "teacher_id" => $request->teacher_id,
            "subject_id" => $request->subject_id,
            "quiz_id" => $request->quiz_id,
            "pre_section_id" => $pre_section_id,
            "opening_date" => $opening_date,
            "can_pass" => $request->can_pass,
        ]);

        return response()->json(["success" => ["message" => "section created successfully"]], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'price' => 'required|integer',
            'gift_price' => 'required|integer',
            'early_price' => 'required|integer',
            'award' => 'required|integer',
            'helper_award' => 'required|integer',
            'teacher_id' => 'required|integer|exists:teachers,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'quiz_id' => 'required|integer|exists:quizzes,id',
            'pre_section_id' => 'integer|exists:sections,id',
            'can_pass' => 'integer|required',
            'opening_date' => ["required", new persian_date()],
        ], [
            "title.required" => "title is required!",
            "price.required" => "price is required!",
            "gift_price.required" => "gift_price is required!",
            "early_price.required" => "early_price is required!",
            "award.required" => "award is required!",
            "helper_award.required" => "helper_award is required!",
            "teacher_id.required" => "teacher_id is required!",
            "subject_id.required" => "subject_id is required!",
            "quiz_id.required" => "quiz_id is required!",
            "opening_date.required" => "opening_date is required!",
            "teacher_id.exists" => "teacher_id does not exist!",
            "subject_id.exists" => "subject_id does not exist!",
            "quiz_id.exists" => "quiz_id does not exist!",
            "pre_section_id.exists" => "pre_section_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $pre_section_id = null;
        if(isset($request->pre_section_id)){
            $pre_section_id = $request->pre_section_id;
        }

        $section = Section::find($id);
        if($section == null){
            return response()->json(["error" => ["message" => "section not found!"]], 404);
        }

        $opening_date = explode("/", $request->opening_date);

        $opening_date = jalali_to_gregorian($opening_date[0], $opening_date[1], $opening_date[2]);
        $opening_date = implode("-", $opening_date);

        $section->title = $request->title;
        $section->price = $request->price;
        $section->gift_price = $request->gift_price;
        $section->early_price = $request->early_price;
        $section->award = $request->award;
        $section->helper_award = $request->helper_award;
        $section->teacher_id = $request->teacher_id;
        $section->subject_id = $request->subject_id;
        $section->quiz_id = $request->quiz_id;
        $section->pre_section_id = $pre_section_id;
        $section->can_pass = $request->can_pass;
        $section->opening_date = $opening_date;
        $section->save();

        return response()->json(["success" => ["message" => "section successfully edited!"]], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){

        $section = Section::find($id);
        if($section == null){
            return response()->json(["error" => ["message" => "section not found!"]], 404);
        }
        $section->delete();

        return response()->json(["success" => ["message" => "section successfully removed!"]], 200);
    }

}
