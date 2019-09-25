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
    Route::put('/setColor','NoteController@setColor');

    Route::post('/createlabel','LabelController@createLabel');
    Route::delete('/deletelabel/{id}','LabelController@deleteLabel');
    Route::put('/editlabel','LabelController@editLabel');
    Route::get('/displaylabel','LabelController@displayLabel');
});
Route::delete('/deletenotelabel/{id}','LabelController@deleteNoteLabel');
Route::post('/createnotelabel','LabelController@createNoteLabel');
Route::get('/displaynotelabel/{noteId}','LabelController@displayNoteLabel');
Route::get('/displayNote','NoteController@displayNote');

Route::post('/login','UserController@login');


Route::post('/register','UserController@registerUser');
Route::get('/register/verifyEmail/{token}','UserController@verifyEmail');
Route::get('/forgetpassword','UserController@forgetPassword');
Route::put('/resetpassword/{token}','UserController@resetPassword');
Route::put('/upload','UserController@uploadImage');
Route::get('/fetchimage','UserController@displayImage');
Route::delete('/remove','UserController@removeImage');
Route::get('/userdetails','UserController@userDetail');



Route::post('/addcollab','CollabController@addCollab');
Route::delete('/removecollab','CollabController@removeCollab');
Route::get('/displaycollab','CollabController@displayCollabNotes');

Route::delete('/deletechecklist', 'ChecklistController@deleteChecklist');




Route::get('/displayNote','NoteController@displayNote');
Route::get('/displayTrashNote','NoteController@displayTrashNote');
Route::get('/displayArchiveNote','NoteController@displayArchiveNote');


Route::post('/facebook', 'UserController@socialLogin');

