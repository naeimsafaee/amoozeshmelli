<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\People;
use App\PeopleToPercent;
use App\Question;
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

        return response()->json(["success" => ["message" => "percent added successfully!"]], 200);
    }

    public function search_people(Request $request){

        $validator = Validator::make($request->all(), [
            'search' => 'string|required',
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

        $people = People::where("name", "LIKE", "%" . $request->name . "%")->get();

        return response()->json(["data_count" => $people->count(), "data" => $people], 200);
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

        return response()->json([
            "success" => [
                "message" => "people created successfully",
                "people_id" => $people_id,
            ],
        ], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){

    }

    public function show_percent($id){

        $people_to_percent = PeopleToPercent::orWhere(["quiz_id" => $id , "section_id" => $id])->get();

        foreach($people_to_percent as $item){
            $item->people;
            $item["people_id"] = $item["people"]["name"];
            unset($item["people"]);
        }

        return response()->json(["data_count" => $people_to_percent->count() , "data" => $people_to_percent],200);
    }

    public function edit_percent(Request $request , $id){
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

        $people_to_percent = PeopleToPercent::find($id);
        if($people_to_percent == null)
            return response()->json(["error" => ["message" => "people to percent not found!"]],404);

        $people_to_percent->percent = $request->percent;
        $people_to_percent->people_id = $request->people_id;
        $people_to_percent->quiz_id = $request->quiz_id;
        $people_to_percent->product_id = $request->product_id;
        $people_to_percent->section_id = $request->section_id;
        $people_to_percent->advertise_id = $request->advertise_id;
        $people_to_percent->save();

        return response()->json(["success" => ["message" => "people to percent edited successfully!"]],200);
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
        $people = People::find($id);
        if($people == null)
            return response()->json(["error" => ["message" => "people not found!"]], 404);
        $people->delete();
        return response()->json(["success" => ["message" => "people deleted successfully!"]], 200);

    }

}
