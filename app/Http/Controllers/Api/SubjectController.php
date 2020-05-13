<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lesson;
use App\Subject;
use App\TeacherToSubjects;
use Illuminate\Http\Request;
use Validator;

class SubjectController extends Controller{

    public function teachers_of_section(Request $request){

        $validator = Validator::make($request->all(), [
            'section_id' => 'required|integer|exists:lessons,id',
        ], [
            "subject_title.required" => "subject_title is required!",
            "subject_title.string" => "subject_title is not a string!",
            "teachers_ids.array" => "teachers_ids is not an array!",
            "lesson_id.required" => "lesson_id is required!",
            "teachers_ids.required" => "teachers_ids is required!",
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
            'subject_title' => 'required|string',
            'lesson_id' => 'required|integer|exists:lessons,id',
            'teachers_ids' => 'required|array',
            'teachers_ids.*' => 'exists:teachers,id',
        ], [
            "subject_title.required" => "subject_title is required!",
            "subject_title.string" => "subject_title is not a string!",
            "teachers_ids.array" => "teachers_ids is not an array!",
            "lesson_id.required" => "lesson_id is required!",
            "teachers_ids.required" => "teachers_ids is required!",
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

        $new_subject = Subject::create([
            "title" => $request->subject_title,
            "lesson_id" => $request->lesson_id,
        ]);

        $teachers_ids = $request->teachers_ids;

        foreach($teachers_ids as $teachers_id){
            TeacherToSubjects::create([
                "teacher_id" => $teachers_id,
                "subject_id" => $new_subject->id
            ]);
        }


        return response()->json(["success" => ["message" => "subject with teachers created!"]], 200);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'subject_title' => 'string|required',
            'lesson_id' => 'required|integer|exists:lessons,id',
        ], [
            "subject_title.string" => "subject_title is not a string!",
            "subject_title.required" => "subject_title is required!",
            "lesson_id.integer" => "lesson_id is not an integer!",
            "lesson_id.exists" => "lesson_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $subject = Subject::find($id);
        if($subject == null)
            return response()->json(["error" => ["message" => "subject not found!"]], 404);

        $subject->title = $request->subject_title;
        $subject->lesson_id = $request->lesson_id;
        $subject->save();

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
        $subject = Subject::find($id);
        if($subject == null){
            return response()->json(["error" => ["message" => "subject not found!"]], 404);
        }
        $subject->delete();
        return response()->json(["success" => ["message" => "subject successfully removed!"]], 200);

    }
}
