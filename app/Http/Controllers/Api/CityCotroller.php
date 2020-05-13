<?php

namespace App\Http\Controllers\Api;

use App\City;
use App\Grade;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class CityCotroller extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $cities = City::all();

        return response()->json(["data_count" => $cities->count() , "data" => $cities], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
            ],
            [
                "name.required" => "name is required!",
                "name.string" => "name is not a string!",
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    "responseCode" => 401,
                    "errorCode" => 'incomplete data',
                    'message' => $validator->errors(),

                ], 401);
        }

        City::updateOrcreate([
            "name" => $request->name,
        ]);

        return response()->json(["success" => ["message" => "city has been added!"]], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
