<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
Use App\Notes;
Use App\Users;

class NoteController extends Controller
{
    public function createNote()
    {
        $input = Input::all();
        
        if (preg_match('/\s/',$input['title']) == true && preg_match('/\s/',$input['description']) == true) 
        {
            DB::table('notes')
                ->insert([[$input],['is_trash' => 1]]);
            echo json_encode([
                    "Message" => "Inserted Successfully with blank space"
                ]);
        }
        elseif ($input['title'] == null && $input['description'] == null) 
        {
            echo json_encode([
                "Message" => "Title & description must not be empty"
            ]);         
        }
        else 
        {
            DB::table('notes')
                ->insert($input);
            echo json_encode([
                    "Message" => "Inserted Successfully"
                ]);
        }
        
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

    public function addCollab(Request $request)
    {
        $notes = Notes::find($request['notes_id']);
        $user = Users::where('email',$request['email'])->first();

        $user_id = $notes->user_id;

        if ($notes->users->contains($user)) 
        {
            return response()->json(['message' => 'Collaborator already Added'],400);            
        }
        else 
        {
            $notes->users()->save($user);
            
            $note = Notes::create([
                'title' => $notes->title,
                'description' => $notes->description,
                'user_id' => $user->id
                ]);
    
            $note_id = $note->id;
    
            $notes = Notes::find($note_id);
            $user = Users::find($user_id);
    
            if ($notes->users->contains($user)) 
            {
                return response()->json(['message' => 'Collaborator already Added'],400);            
            }
            else 
            {
                $note->users()->save($user); 
                return response()->json(['message' => 'Collaborator Added'],200);               
            }
        }
       
    }
}
?>