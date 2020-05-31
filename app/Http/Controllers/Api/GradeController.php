<?php

namespace App\Http\Controllers\Api;


use App\City;
use App\Grade;
use App\Http\Controllers\Controller;
use App\Lesson;
use App\User;
use Illuminate\Http\Request;
use Validator;

class GradeController extends Controller{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $grades = Grade::orderBy('created_at', 'ASC')->get();

        return response()->json(["data_count" => $grades->count(), "data" => $grades], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
        ], [
            "title.required" => "title is required!",
            "title.string" => "title is not a string!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        Grade::updateOrcreate([
            "title" => $request->title,
        ]);

        return response()->json(["success" => ["message" => "grade has been added!"]], 200);
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
            'grade_title' => 'required|string',
        ], [
            "grade_title.required" => "grade_title is required!",
            "grade_title.string" => "grade_title is not a string!",
        ]);

        $grade = Grade::find($id);
        $grade->title = $request->grade_title;
        $grade->save();

        return response()->json(["success" => ["message" => "grade successfully edited!"]], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        $grade = Grade::find($id);
        if($grade == null){
            return response()->json(["error" => ["message" => "grade not found!"]], 404);
        }
        $grade->delete();
        return response()->json(["success" => ["message" => "grade successfully removed!"]], 200);

    }
}
