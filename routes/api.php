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

Route::group([
    'middleware' => 'cors'
],function (){
 Route::post('/login','UserController@login');

});

// Route::post('/login','UserController@login');


Route::post('/register','UserController@registerUser');
Route::get('/forgetpassword','UserController@forgetPassword');
Route::put('/resetpassword/{token}','UserController@resetPassword');
Route::post('/createlabel','LabelController@createLabel');
Route::delete('/deletelabel','LabelController@deleteLabel');
Route::put('/createnotelabel','LabelController@createNoteLabel');
Route::delete('/deletenotelabel','LabelController@deleteNoteLabel');
Route::put('/editlabel','LabelController@editLabel');
Route::put('/createlabelnote','LabelController@createNoteLabel');
Route::post('/addcollab','CollabController@addCollab');
Route::put('/removecollab','CollabController@removeCollab');
Route::delete('/deletechecklist', 'ChecklistController@deleteChecklist');
Route::put('/upload','UserController@uploadImage');
Route::get('/fetchimage','UserController@displayImage');
Route::delete('/remove','UserController@removeImage');






