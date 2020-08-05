<?php

namespace App\Http\Controllers\Api;

use App\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class CommentController extends Controller{
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
            'section_id' => 'required|integer|exists:sections,id',
            'text' => 'required',
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

        $reply_to = null;
        if($request->has("reply_to")){
            $reply_to = $request->reply_to;
        }

        $user = $request->user();

        Comment::create([
            "section_id" => $request->section_id,
            "user_id" => $user["id"],
            "text" => $request->text,
            "reply_to" => $reply_to,
        ]);

        return response()->json(["success" => "comment successfully added!"], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $comments = Comment::where("section_id", $id)->where('status', 1)->get();

        foreach($comments as $comment){
            $comment->user;
            $comment->reply_to;

            if($comment["user"]["fullName"] == null){
                $comment["user"]["fullName"] = "بی نام";
            }
        }

        return response()->json(["data_count" => $comments->count(), "data" => $comments], 200);
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

    public function accept(Request $request , $id){

        if($id == 0){

            if($request->status == 2){
                $comments = Comment::paginate(10);

                foreach($comments as $comment){
                    $comment->user;
                    $comment->section;
                }
                return response()->json($comments , 200);
            } else {
                $comments = Comment::query()->where("status" , $request->status)->get();
            }
            foreach($comments as $comment){
                $comment->user;
                $comment->section;
            }

            return response()->json($comments , 200);
        } else {
            $comment = Comment::query()->find($id);
            $status = $comment->status;
            if($status == 0){
                $comment->status = 1;
            } else {
                $comment->status = 0;
            }
            $comment->save();
            return response()->json("success" , 200);
        }

    }
}
