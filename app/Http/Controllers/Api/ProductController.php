<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use Validator;

class ProductController extends Controller{
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
            'title' => 'string|required',
            'price' => 'integer|required',
            'gift_price' => 'integer|required',
            'grade_id' => 'integer|required|exists:grades,id',
            'download_able' => 'integer|required',
            'file' => 'file',
        ], [
            "title.required" => "title is required!",
            "price.required" => "title is required!",
            "gift_price.required" => "title is required!",
            "grade_id.required" => "title is required!",
            "download_able.required" => "title is required!",
            "file.file" => "file not supported!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        if($request->download_able == 1){
            if(!isset($request->file)){
                return response()->json(["error" => ["when you set download able to true file is needed!"]], 401);
            }
        }

        $answer_file_path = null;
        $file = $request->file('answer_file');
        if($file != null){
            $ext = $file->extension();
            $file_name = time() . mt_rand() . "." . $ext;

            $path = public_path('file/product/');
            $file->move($path, $file_name);
            $answer_file_path = $path . $file_name;
        }

        Product::create([
            "title" => $request->title,
            "price" => $request->price,
            "gift_price" => $request->gift_price,
            "grade_id" => $request->grade_id,
            "download_able" => $request->download_able,
            "file_path" => $answer_file_path,
        ]);

        return response()->json(["success" => ["message" => "product added successfully!"]], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $product = Product::find($id);
        $product->grade_id;

        return response()->json(["data" => $product], 200);
    }

    public function show_with_grade_id(Request $request){

        $products = Product::where("grade_id", $request->grade_id)->get();

        foreach($products as $product){
            $product->grade_id;
        }

        return response()->json(["data_count" => $products->count(), "data" => $products], 200);
    }

    public function show_products(Request $request){

        $user = $request->user();

        $products = Product::where("grade_id", $user->grade_id)->get();

        foreach($products as $product){
            $product->grade_id;
        }

        return response()->json(["data_count" => $products->count(), "data" => $products], 200);
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
