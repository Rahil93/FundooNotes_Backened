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

Route::middleware('cors:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'cors'
],function (){

});

Route::group([
    'middleware' => 'auth:api'
],function(){
});

Route::post('/login','UserController@login');

Route::get('/displayNote','NoteController@displayNote');

Route::post('/register','UserController@registerUser');
Route::get('/register/verifyEmail/{token}','UserController@verifyEmail');
Route::get('/forgetpassword','UserController@forgetPassword');
Route::put('/resetpassword/{token}','UserController@resetPassword');
Route::put('/upload','UserController@uploadImage');
Route::get('/fetchimage','UserController@displayImage');
Route::delete('/remove','UserController@removeImage');

Route::post('/createlabel','LabelController@createLabel');
Route::delete('/deletelabel','LabelController@deleteLabel');
Route::put('/createnotelabel','LabelController@createNoteLabel');
Route::delete('/deletenotelabel','LabelController@deleteNoteLabel');
Route::put('/editlabel','LabelController@editLabel');

Route::post('/addcollab','CollabController@addCollab');
Route::put('/removecollab','CollabController@removeCollab');

Route::delete('/deletechecklist', 'ChecklistController@deleteChecklist');


Route::post('/createNote','NoteController@createNote');
Route::put('/editNote','NoteController@editNote');
Route::put('/setReminder','NoteController@setReminder');
Route::put('/trashNote','NoteController@trashNote');
Route::put('/restoreNote','NoteController@restoreNote');
Route::put('/archiveNote','NoteController@archiveNote');
Route::put('/unarchiveNote','NoteController@unarchiveNote');
Route::delete('/deleteNote/{id}','NoteController@deleteNote');
Route::get('/displayTrashNote','NoteController@displayTrashNote');
Route::get('/displayArchiveNote','NoteController@displayArchiveNote');
Route::get('/displayReminder','NoteController@displayReminder');






