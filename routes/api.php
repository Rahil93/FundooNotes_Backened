<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register','UserController@registerUser');
Route::get('/login','UserController@login');
Route::get('/forgetpassword','UserController@forgetPassword');
Route::put('/resetpassword','UserController@resetPassword');
Route::get('/verify/{token}','UserController@getToken');
Route::post('/createlabel','LabelController@createLabel');
Route::delete('/deletelabel','LabelController@deleteLabel');
Route::put('/createnotelabel','LabelController@createNoteLabel');
Route::delete('/deletenotelabel','LabelController@deleteNoteLabel');
Route::put('/editlabel','LabelController@editLabel');
Route::put('/createlabelnote','LabelController@createNoteLabel');
Route::post('/addcollab','CollabController@addCollab');
Route::put('/removecollab','CollabController@removeCollab');
Route::delete('/deletechecklist', 'ChecklistController@deleteChecklist');
Route::put('/upload','ImageUploadController@uploadImage');
Route::get('/fetchimage','ImageUploadController@displayImage');
Route::delete('/remove','ImageUploadController@removeImage');






