<?php

namespace App\Http\Controllers\Api;

use App\Grade;
use App\GradeToLesson;
use App\Http\Controllers\Controller;
use App\Lesson;
use App\Subject;
use Illuminate\Http\Request;
use Validator;

class LessonController extends Controller{

    public function show_with_grade_id(Request $request){
        $validator = Validator::make($request->all(), [
            'grade_id' => 'required|integer|exists:grades,id',
        ], [
            "grade_id.required" => "grade_id is required!",
            "grade_id.integer" => "grade_id must be an integer!",
            "grade_id.exists" => "grade_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $grade_to_lessons = GradeToLesson::where("grade_id", $request->grade_id)->get();

        $lessons = [];
        foreach($grade_to_lessons as $grade_to_lesson){
            $grade_to_lesson->lessons;
            if($grade_to_lesson["lessons"] == null)
                continue;
            $lessons[] = $grade_to_lesson["lessons"];
        }
        return response()->json(["data_count" => count($lessons), "data" => $lessons], 200);
    }

    public function user_lessons(Request $request){

        $user = $request->user();
        $grade_to_lessons = $user->grade_to_lessons;

        $lessons = [];
        foreach($grade_to_lessons as $grade_to_lesson){
            $grade_to_lesson->lessons;
            if($grade_to_lesson["lessons"] == null)
                continue;
            $lessons[] = $grade_to_lesson["lessons"];
        }

        return response()->json(["data_count" => count($lessons), "data" => $lessons], 200);
    }

    public function subjects_of_lesson(Request $request){

        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|integer|exists:lessons,id',
        ], [
            "lesson_id.required" => "lesson_id is required!",
            "lesson_id.integer" => "lesson_id must be an integer!",
            "lesson_id.exists" => "lesson_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $subjects = Subject::where("lesson_id", $request->lesson_id)->get();

        return response()->json(["data_count" => $subjects->count(), "data" => $subjects], 200);
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $lessons = Lesson::all();
        return response()->json(["data_count" => $lessons->count(), "data" => $lessons], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'lesson_title' => 'required|string',
//            'grade_id' => 'required|integer|exists:grades,id',/*change this to array*/
            'grade_ids' => 'required|array',
            'grade_ids.*' => 'exists:grades,id',
        ], [
            "lesson_title.required" => "lesson_title is required!",
            "lesson_title.string" => "lesson_title is not a string!",
            "grade_id.required" => "grade_id is required!",
            "grade_id.integer" => "grade_id must be an integer!",
            "grade_id.exists" => "grade_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $new_lesson = Lesson::updateOrcreate([
            "title" => $request->lesson_title,
        ]);

        $grades = $request->grade_ids;

        foreach($grades as $grade){
            GradeToLesson::updateOrCreate([
                "grade_id" => $grade,
                "lesson_id" => $new_lesson->id,
            ]);
        }

        return response()->json(["success" => ["message" => "lessons have been added!"]], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $lesson = Lesson::find($id);

        if($lesson == null)
            return response()->json(["error" => ["message" => "lesson not found!"]],404);
        return response()->json(["data" => $lesson],200);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'lesson_title' => 'string|required',
            //            'grade_id' => 'integer|exists:grades,id',
        ], [
            "lesson_title.string" => "lesson_title is not a string!",
            "lesson_title.required" => "lesson_title is required!",
            //            "grade_id.integer" => "grade_id is not an integer!",
            //            "grade_id.exists" => "grade_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $lesson = Lesson::find($id);
        if($lesson == null)
            return response()->json(["error" => ["message" => "lesson_id not found!"]], 404);
        $lesson->title = $request->lesson_title;
        $lesson->save();

        /*
                if(isset($request->grade_id)){
                    if($request->grade_id != null){
                        GradeToLesson::
                    }
                }*/

        return response()->json(["success" => ["message" => "lesson successfully changed!"]], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){

        $lesson = Lesson::find($id);
        if($lesson == null){
            return response()->json(["error" => ["message" => "lesson not found!"]], 404);
        }
        $lesson->delete();
        return response()->json(["success" => ["message" => "lesson successfully removed!"]], 200);
    }

}
