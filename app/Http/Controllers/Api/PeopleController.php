<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\People;
use App\PeopleToPercent;
use Illuminate\Http\Request;
use Validator;

class PeopleController extends Controller{

    public function add_percent(Request $request){

        $validator = Validator::make($request->all(), [
            'percent' => 'integer|required',
            'people_id' => 'integer|required|exists:people,id',
            'quiz_id' => 'integer|exists:quizzes,id',
            'product_id' => 'integer|exists:products,id',
            'section_id' => 'integer|exists:sections,id',
            'advertise_id' => 'integer|exists:advertises,id',
        ], [
            "percent.required" => "percent is required!",
            "people_id.required" => "people_id is required!",
            "people_id.exists" => "people_id does not exist!",
            "quiz_id.exists" => "quiz_id does not exist!",
            "product_id.exists" => "product_id does not exist!",
            "section_id.exists" => "section_id does not exist!",
            "advertise_id.exists" => "advertise_id does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        PeopleToPercent::create([
            "percent" => $request->percent,
            "people_id" => $request->people_id,
            "quiz_id" => $request->quiz_id,
            "product_id" => $request->product_id,
            "section_id" => $request->section_id,
            "advertise_id" => $request->advertise_id,
        ]);

        return response()->json(["success" => ["message" => "percent added successfully!"]],200);
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
            'name' => 'string|required',
        ], [
            "name.string" => "name is not a string!",
            "name.required" => "name is required!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $people_id = People::create([
            "name" => $request->name,
        ])->id;

        return response()->json(["success" => ["message" => "people created successfully", "people_id" => $people_id]], 200);
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        //
    }
}
