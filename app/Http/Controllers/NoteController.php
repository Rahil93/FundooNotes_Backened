<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;

class NoteController extends Controller
{
    public function createNote()
    {
        $input = Input::all();
        DB::table('notes')
            ->insert($input);
    }

    public function editNote()
    {
        $input = Input::all();
        DB::table('notes')
            ->where(['id' => $input['id'] ])
            ->update(['title' => $input['title'] , 'description' => $input['description'] ]);
    }

    public function displayNote()
    {
        $notes = DB::table('notes')
                    ->where(['is_trash' => '0' , 'is_archived' => '0']) 
                    ->get();

        foreach ($notes as $note) 
        {
           echo "<pre>";
           echo json_encode($note)."<br>";
        }
    }

    public function trashNote()
    {
        $input = Input::all();
        DB::table('notes')
            ->where([ $input ])
            ->update([ 'is_trash' => 1 ]);
    }

    public function displayTrashNote()
    {
        $notes = DB::table('notes')
                     ->where(['is_trash' => '1'])
                     ->get();

        foreach ($notes as $note) 
        {
           echo "<pre>";
           echo json_encode($note)."<br>";
        }
    }

    public function restoreNote()
    {
        $input = Input::all();
        DB::table('notes')
            ->where([ $input ])
            ->update([ 'is_trash' => 0 ]);
    }

    public function archiveNote()
    {
        $input = Input::all();
        DB::table('notes')
            ->where([ $input ])
            ->update([ 'is_archived' => 1 ]);
    }

    public function unarchiveNote()
    {
        $input = Input::all();
        DB::table('notes')
            ->where([ $input ])
            ->update([ 'is_archived' => 0 ]);
    }

    public function displayArchiveNote()
    {
        $notes = DB::table('notes')
                     ->where(['is_archived' => '1'])
                     ->get();

        foreach ($notes as $note) 
        {
           echo "<pre>";
           echo json_encode($note)."<br>";
        }
    }

    public function deleteNote()
    {
        $input = Input::all();
        DB::table('notes')
            ->where( $input )
            ->delete();
    }

    

    public function setReminder()
    {
        $input = Input::all();
        DB::table('notes')
            ->where(['id' => $input['id']])
            ->update(['reminder' => $input['reminder']]);
    }

    public function displayReminder()
    {
        $date = date("Y/m/d");
        $notes = DB::table('notes')
                     ->where(['reminder' => $date])
                     ->get();

        foreach ($notes as $note) 
        {
            echo "<pre>";
            echo json_encode($note)."<br>";
        }
    }
}
?>