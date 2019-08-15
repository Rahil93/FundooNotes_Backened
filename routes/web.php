<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::post('/createNote','NoteController@createNote');
Route::put('/editNote','NoteController@editNote');
Route::post('/setReminder','NoteController@setReminder');
Route::post('/trashNote','NoteController@trashNote');
Route::post('/restoreNote','NoteController@restoreNote');
Route::post('/archiveNote','NoteController@archiveNote');
Route::post('/unarchiveNote','NoteController@unarchiveNote');
Route::delete('/deleteNote','NoteController@deleteNote');
Route::get('/displayNote','NoteController@displayNote');
Route::get('/displayTrashNote','NoteController@displayTrashNote');
Route::get('/displayArchiveNote','NoteController@displayArchiveNote');
Route::get('/displayReminder','NoteController@displayReminder');





