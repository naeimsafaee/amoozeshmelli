<?php

namespace App\Http\Controllers\Api;

use App\Book;
use App\GradeToLesson;
use App\Http\Controllers\Controller;
use App\Lesson;
use App\Subject;
use App\Teacher;
use App\TeacherToSubjects;
use App\User;
use Illuminate\Http\Request;
use Validator;

class TeacherController extends Controller{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){

    }

    public function search_teacher(Request $request){

        $users = User::where('fullName', "like", "%" . $request->fullname . "%")->Paginate(10);

        $collection = $users->getCollection();

        $main_array = [];

        $i = 0;
        foreach($users as $user){
            $user->teacher;
            $user->city;
            $user->grade;
            if($user->teacher != null){
                $main_array[$i]["id"] = $user->id;
                $main_array[$i]["teacher_id"] = $user["teacher"]["id"];
                $main_array[$i]["teacher_phone"] = $user["phone"];
                $main_array[$i]["fullName"] = $user["fullName"];
                $main_array[$i]["grade"] = $user["grade"];
                $main_array[$i]["city"] = $user["city"];
                $main_array[$i]["is_global"] = ($user["teacher"]["is_global"] == 1 ? "بله" : "خیر");

                $i++;
            }
            //            unset($book["PublisherID"]);
            //            unset($book["Opt"]);
        }

        return response()->json([
            "data_count" => count($collection),
            "current_page" => (int)$request->page,
            "total_count" => $users->total(),
            "total_pages" => ceil($users->total() / 10),
            "data" => $main_array,
        ], 200);
    }

    public function search_teacher_with_sub(Request $request){

        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|integer|exists:subjects,id',
        ], [
            "subject_id.required" => "subject_id is required!",
            "subject_id.integer" => "subject_id is not an integer!",
            "subject_id.exists" => "subject_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }


        $teacher_to_subjects = TeacherToSubjects::where("subject_id", $request->subject_id)->get();

        $teachers = [];

        $i = 0;
        foreach($teacher_to_subjects as $teacher_to_subject){
            $teacher_to_subject->teacher->info;
            $teacher_to_subject["teacher"]["info"]["teacher_id"] = $teacher_to_subject["teacher_id"];

            $teachers[$i] = $teacher_to_subject["teacher"]["info"];
            $teachers[$i]["is_global"] = $teacher_to_subject["teacher"]["is_global"];

            $i++;
        }

        return response()->json(["data_count" => $teacher_to_subjects->count(), "data" => $teachers], 200);
    }

    public function grades_of_teacher(Request $request){

        $validator = Validator::make($request->all(), [
            'grade_id' => 'required|integer|exists:grades,id',
        ], [
            "grade_id.required" => "grade_id is required!",
            "grade_id.integer" => "grade_id is not an integer!",
            "grade_id.exists" => "grade_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $users = User::teacherOnly()->where("grade_id", $request->grade_id)->select("id", "fullName")->get();
        $main_users = [];
        $i = 0;
        foreach($users as $user){
            $user->teacher;
            $main_users[$i]["id"] = $user["id"];
            $main_users[$i]["fullName"] = $user["fullName"];
            $main_users[$i]["teacher_id"] = $user["teacher"]["id"];
            $i++;
        }

        $lessons = GradeToLesson::where("grade_id", $request->grade_id)->get();

        $main_lessons = [];

        foreach($lessons as $lesson){
            $lesson->lessons;
            $main_lessons[] = $lesson["lessons"];
        }

        return response()->json([
            "teachers" => ["data_count" => $users->count(), "data" => $main_users],
            "lessons" => ["data_count" => $lessons->count(), "data" => $main_lessons],
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'is_global' => 'required|integer',
            'phone' => 'required|iran_mobile',
            'fullName' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'grade_id' => 'required|exists:grades,id',
        ], [
            "is_global.required" => "is_global is required!",
            "is_global.integer" => "is_global is not an integer!",
            "phone.required" => "phone is required!",
            "phone.iran_mobile" => "phone must be an iran_mobile!",
            "fullName.required" => "fullName is required!",
            "fullName.string" => "fullName must be a string!",
            "city_id.required" => "city_id is required!",
            "city_id.exists" => "city_id does not exist!",
            "grade_id.required" => "grade_id is required!",
            "grade_id.exists" => "grade_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $new_user = User::create([
            "phone" => $request->phone,
            "fullName" => $request->fullName,
            "city_id" => $request->city_id,
            "grade_id" => $request->grade_id,
        ]);

        Teacher::create([
            "user_id" => $new_user->id,
            "is_global" => $request->is_global,
        ]);

        return response()->json(["success" => ["message" => "teacher created!"]], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $teacher = Teacher::find($id);
        $teacher->info;
        if($teacher == null)
            return response()->json(["error" => ["message" => "teacher not found!"]], 404);

        return response()->json(["data" => $teacher], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'teacher_name' => 'string|required',
            'teacher_phone' => 'required|iran_mobile',
            'grade_id' => 'integer|required|exists:grades,id',
            'city_id' => 'integer|required|exists:cities,id',
            'is_global' => 'integer|required',
        ], [
            "is_global.required" => "is_global is required!",
            "is_global.integer" => "is_global is not an integer!",
            "teacher_phone.required" => "phone is required!",
            "teacher_phone.iran_mobile" => "phone must be an iran_mobile!",
            "teacher_name.required" => "fullName is required!",
            "teacher_name.string" => "fullName must be a string!",
            "city_id.required" => "city_id is required!",
            "city_id.exists" => "city_id does not exist!",
            "grade_id.required" => "grade_id is required!",
            "grade_id.exists" => "grade_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $teacher = Teacher::find($id);

        if($teacher == null)
            return response()->json(["error" => ["message" => "teacher not found!"]], 404);

        $teacher->is_global = $request->is_global;
        $teacher->save();

        $user_id = $teacher->user_id;

        $user = User::find($user_id);
        $user->fullName = $request->teacher_name;
        $user->phone = $request->teacher_phone;
        $user->grade_id = $request->grade_id;
        $user->city_id = $request->city_id;
        $user->save();

        return response()->json(["success" => ["message" => "teacher successfully changed!"]], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){

        $teacher = Teacher::find($id);

        if($teacher == null)
            return response()->json(["error" => ["message" => "teacher not found!"]], 404);

        $teacher->delete();
        return response()->json(["success" => ["message" => "teacher successfully removed!"]], 200);
    }
}
