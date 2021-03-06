<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('migrate', function(){
    Artisan::call('migrate');
    //    Artisan::call('migrate:refresh');
    die('migrate complete');
});

Route::get('config_clear', function(){
    Artisan::call('config:clear');
//    Artisan::call('cache:clear');
    die('clear complete');
});
Route::get('passport', function(){
    Artisan::call('passport:install');
    die('passport installed');
});
//Route::apiResource('users', 'Api\UserController');


Route::post('register', 'PassportController@register');
Route::post('verify_sms', 'PassportController@verify_sms');
Route::post('login_admin', 'PassportController@login_admin');
Route::post('add_admin', 'PassportController@add_admin');
Route::post('login', 'PassportController@login');


Route::middleware(['auth:api', 'user_scope:user'])->group(function(){

    Route::post('user_lessons', 'Api\LessonController@user_lessons');
    Route::post('subjects_of_lesson_of_user', 'Api\LessonController@subjects_of_lesson')->middleware('check_user_lesson');
    //    Route::post('user_lessons', 'Api\LessonController@user_lessons');

    Route::get('u_products', 'Api\ProductController@show_products');
    Route::post('buy_product', 'Api\ProductController@buy_product');
//    Route::post('u_products_with_lesson', 'Api\ProductController@show_product_with_lesson_id');

    Route::get('sliders', 'Api\ImageController@sliders');
    Route::post('user_search_teacher_with_sub', 'Api\TeacherController@search_teacher_with_sub');

    Route::post('user_search_section', 'Api\SectionController@section_with_teacher_and_subject');

    Route::post('buy_section', 'Api\SectionController@buy_section');
    Route::post('end_section', 'Api\SectionController@end_section');

    Route::post('user_part/{id}' , 'Api\PartController@show_user_part');

    Route::post('make_quiz' , 'Api\QuizController@make_quiz');
    Route::post('complete_quiz' , 'Api\QuizController@get_complete_quiz');
    Route::post('show_questions_of_quiz/{id}' , 'Api\QuizController@show_questions_of_quiz');
    Route::post('quiz_correction' , 'Api\QuizController@quiz_correction');
    Route::post('buy_quiz' , 'Api\QuizController@buy_quiz');


    Route::apiResource('comment' , 'Api\CommentController');
    Route::apiResource('profile' , 'Api\ProfileController');

    Route::post('get_random_advertise' , 'Api\AdvertiseController@get_random_advertise');
    Route::post('end_advertise/{id}' , 'Api\AdvertiseController@end_advertise');

    Route::post('increase_wallet' , 'Api\ProfileController@increase_wallet');

});

Route::middleware(['auth:admin', 'admin_scope:admin'])->group(function(){

    Route::apiResource('grade', 'Api\GradeController');
    Route::post('city', 'Api\CityCotroller@store');

    Route::apiResource('lesson', 'Api\LessonController');
    Route::post('lessons', 'Api\LessonController@show_with_grade_id');

    Route::apiResource('subject', 'Api\SubjectController');
    Route::post('subjects_of_lesson', 'Api\LessonController@subjects_of_lesson');

    Route::apiResource('section', 'Api\SectionController');
    Route::post('search_section', 'Api\SectionController@section_with_teacher_and_subject');

    Route::apiResource('quiz', 'Api\QuizController');
    Route::post('show_quiz', 'Api\QuizController@show_quiz');
    Route::post('search_quiz', 'Api\QuizController@search_quiz');

    Route::apiResource('teacher', 'Api\TeacherController');
    Route::post('search_teacher', 'Api\TeacherController@search_teacher');
    Route::post('grades_of_teacher', 'Api\TeacherController@grades_of_teacher');
    Route::post('search_teacher_with_sub', 'Api\TeacherController@search_teacher_with_sub');

    Route::get('admin_city', 'Api\CityCotroller@index');
    Route::get('admin_grade', 'Api\GradeController@index');

    Route::apiResource('question', 'Api\QuestionController');

    Route::apiResource('option', 'Api\OptionController');

    Route::apiResource('part', 'Api\PartController');

    Route::apiResource('people', 'Api\PeopleController');
    Route::post('add_percent', 'Api\PeopleController@add_percent');
    Route::post('search_people', 'Api\PeopleController@search_people');

    Route::post('percent/{id}', 'Api\PeopleController@show_percent');
    Route::put('percent/{id}', 'Api\PeopleController@edit_percent');
    Route::delete('percent/{id}', 'Api\PeopleController@destroy_percent');

    Route::apiResource('advertise', 'Api\AdvertiseController');

    Route::apiResource('product', 'Api\ProductController');
    Route::get('products/{grade_id}', 'Api\ProductController@show_with_grade_id');
    Route::get('products', 'Api\ProductController@show_products');

    Route::post('check_token', 'PassportController@check_token');

});


Route::get('city', 'Api\CityCotroller@index');
Route::get('grade', 'Api\GradeController@index');


