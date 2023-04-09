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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth/registration', [\App\Http\Controllers\Api\AuthController::class, 'registration']);
Route::post('auth/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::patch('auth/change_password', [\App\Http\Controllers\Api\AuthController::class, 'changePassword']);
Route::post('send_code', [\App\Http\Controllers\Api\SendCodeController::class, 'sendCode']);

// Account
Route::patch('account', [\App\Http\Controllers\Api\AccountController::class, 'update']);

// Course
Route::post('courses', [\App\Http\Controllers\Api\CourseController::class, 'create']);
Route::get('courses/{id}', [\App\Http\Controllers\Api\CourseController::class, 'show']);

// Test
Route::get('test/{id}', [\App\Http\Controllers\Api\TestController::class, 'show']);
Route::post('test', [\App\Http\Controllers\Api\TestController::class, 'create']);
Route::patch('test/{id}', [\App\Http\Controllers\Api\TestController::class, 'update']);
Route::delete('test/{id}', [\App\Http\Controllers\Api\TestController::class, 'delete']);

// Question
Route::post('question', [\App\Http\Controllers\Api\QuestionController::class, 'create']);
Route::patch('question/group/{id}/update-position', [\App\Http\Controllers\Api\QuestionController::class, 'updatePositionQuestionGroup']);
Route::delete('question/group/{id}', [\App\Http\Controllers\Api\QuestionController::class, 'deleteQuestionGroup']);

Route::post('teacher/add-to-account', [\App\Http\Controllers\Api\TeacherController::class, 'addToAccount']);
Route::patch('teacher/accept-invite', [\App\Http\Controllers\Api\TeacherController::class, 'acceptInvite']);
Route::delete('teacher/remove-from-account', [\App\Http\Controllers\Api\TeacherController::class, 'removeFromAccount']);

Route::post('dialog-group', [\App\Http\Controllers\Api\DialogGroupController::class, 'createGroup']);
Route::post('dialog-group/{group}/add-user', [\App\Http\Controllers\Api\DialogGroupController::class, 'addUser']);
Route::post('dialog-group/{group}/add-admin', [\App\Http\Controllers\Api\DialogGroupController::class, 'addAdmin']);
Route::delete('dialog-group/{group}/remove-admin', [\App\Http\Controllers\Api\DialogGroupController::class, 'removeAdmin']);
Route::delete('dialog-group/{group}/remove-user', [\App\Http\Controllers\Api\DialogGroupController::class, 'removeUser']);

Route::resource('attachment', \App\Http\Controllers\Api\AttachmentController::class);

// Опции для селекторов
Route::get('options/account_type', [\App\Http\Controllers\Api\OptionsController::class, 'accountTypeOptions']);
Route::get('options/course_category', [\App\Http\Controllers\Api\OptionsController::class, 'courseCategoriesOptions']);
