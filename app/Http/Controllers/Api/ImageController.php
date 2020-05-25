<?php

namespace App\Http\Controllers\Api;

use App\Control;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(){

    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        //
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

    public function sliders(){

        $sliders = Control::where("title", "slider_image")->get();

        $main = [];
        $i = 0;
        foreach($sliders as $slider){
            $slider->image;
            $main[$i] = $slider["image"];
            $main[$i]["title"] = "متن تست اسلاید " . $i;
        }
        return response()->json(["data_count" => $sliders->count(), "data" => $main], 200);
    }

}
