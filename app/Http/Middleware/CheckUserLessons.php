<?php

namespace App\Http\Middleware;

use App\GradeToLesson;
use Closure;

class CheckUserLessons{
    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next){

        $user_grade_id = $request->user()->grade_id;
        $lesson_id = $request->lesson_id;

        $grade_to_lesson = GradeToLesson::where(["grade_id" => $user_grade_id, "lesson_id" => $lesson_id]);

        if($grade_to_lesson->count() == 0)
            return response()->json([
                "error" => ["message" => "you do not have access to this grade!"],
            ], 403);
        return $next($request);
    }
}
