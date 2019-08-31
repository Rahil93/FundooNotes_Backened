<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
// use DB;
Use App\Model\Notes;
Use App\Model\Users;

class NoteController extends Controller
{
    public function createNote(Request $request)
    {
        // $input = Input::all();
        $input = $request->all();
        
        if (preg_match('/\s/',$input['title']) == true && preg_match('/\s/',$input['description']) == true) 
        {
            // DB::table('notes')
            //     ->insert([[$input],['is_trash' => 1]]);
            // echo json_encode([
            //         "Message" => "Inserted Successfully with blank space"
            //     ]);
            $input['is_trash'] = 1;
            $note = Notes::create($input);

            return response()->json(["Message" => "Inserted Successfully with blank space in trash"],200);
        }
        elseif ($input['title'] == null && $input['description'] == null) 
        {
            return response()->json(["Message" => "Title & description must not be empty"],400);         
        }
        else 
        {
            // DB::table('notes')
            //     ->insert($input);
            // echo json_encode([
            //         "Message" => "Inserted Successfully"
            //     ]);
            // $input = $request->all();
            // $input['is_trash'] = 1;
            $note = Notes::create($input);

            return response()->json(["Message" => "Note Created Successfully"],200);
        }
        
    }

    public function editNote(Request $request)
    {
        // $input = Input::all();
        // DB::table('notes')
        //     ->where(['id' => $input['id'] ])
        //     ->update(['title' => $input['title'] , 'description' => $input['description'] ]);

        $note = Notes::find($request['id']);
        if ($note) 
        {
            if ($request['title'] == null && $request['description'] == null) 
            {
                return response()->json(['message' => 'Title & Description must not be empty'],400);
            }
            else 
            {
                $note->title = $request['title'];
                $note->description = $request['description'];
                // $note->user_id = $request['user_id'];
                $note->save();
                return response()->json(['message' => 'Note Updated Successfully'],200);
            }
        }
        else {
            return response()->json(['message' => 'UnAuthorized Note Id'],404);
        }
    }

    public function displayNote(Request $request)
    {
        // $notes = DB::table('notes')
        //             ->where(['is_trash' => '0' , 'is_archived' => '0']) 
        //             ->get();

        $notes = Notes::where(['user_id' => $request,'is_trash' => '0' , 'is_archived' => '0'])->all();

        foreach ($notes as $note) 
        {
           echo "<pre>";
           echo json_encode($note)."<br>";
        }
    }

    public function trashNote(Request $request)
    {
        // $input = Input::all();
        // DB::table('notes')
        //     ->where([ $input ])
        //     ->update([ 'is_trash' => 1 ]);

        $note = Notes::find($request['id']);
        if ($note) 
        {
            $note->is_trash = 1;
            if ($note->save()) 
            {
                return response()->json(['message' => 'Note Trashed Successfully'],200);
            }
            else 
            {
                return response()->json(['message' => 'Error while Trashed'],400);
            }
        }
        else 
        {
            return response()->json(['message' => 'Note Id Invalid'],404);
        }
    }

    public function displayTrashNote(Request $request)
    {
        // $notes = DB::table('notes')
        //              ->where(['is_trash' => '1'])
        //              ->get();

        $notes = Notes::where(['user_id' => $request,'is_trash' => '1'])->all();

        foreach ($notes as $note) 
        {
           echo "<pre>";
           echo json_encode($note)."<br>";
        }
    }

    public function restoreNote(Request $request)
    {
        // $input = Input::all();
        // DB::table('notes')
        //     ->where([ $input ])
        //     ->update([ 'is_trash' => 0 ]);

        $note = Notes::find($request['id']);
        if ($note) 
        {
            $note->is_trash = 0;
            if ($note->save()) 
            {
                return response()->json(['message' => 'Note Restore Successfully'],200);
            }
            else 
            {
                return response()->json(['message' => 'Error while Restoring'],400);
            }
        }
        else 
        {
            return response()->json(['message' => 'Note Id Invalid'],404);
        }
    }

    public function archiveNote(Request $request)
    {
        // $input = Input::all();
        // DB::table('notes')
        //     ->where([ $input ])
        //     ->update([ 'is_archived' => 1 ]);

        $note = Notes::find($request['id']);
        if ($note) 
        {
            $note->is_archived = 1;
            if ($note->save()) 
            {
                return response()->json(['message' => 'Note Archived Successfully'],200);
            }
            else {
                return response()->json(['message' => 'Error while Archiving'],400);
            }
        }
        else 
        {
            return response()->json(['message' => 'Note Id Invalid'],404);
        }
    }

    public function unarchiveNote(Request $request)
    {
        // $input = Input::all();
        // DB::table('notes')
        //     ->where([ $input ])
        //     ->update([ 'is_archived' => 0 ]);

        $note = Notes::find($request['id']);
        if ($note) 
        {
            $note->is_archived = 0;
            if ($note->save()) 
            {
                return response()->json(['message' => 'Note unArchived Successfully'],200);
            }
            else {
                return response()->json(['message' => 'Error while UnArchiving'],400);
            }
        }
        else 
        {
            return response()->json(['message' => 'Note Id Invalid'],404);
        }
    }

    public function displayArchiveNote(Request $request)
    {
        // $notes = DB::table('notes')
        //              ->where(['is_archived' => '1'])
        //              ->get();

        $notes = Notes::where(['user_id' => $request,'is_archived' => '1'])->all();

        foreach ($notes as $note) 
        {
           echo "<pre>";
           echo json_encode($note)."<br>";
        }
    }

    public function deleteNote()
    {
        // $input = Input::all();
        // DB::table('notes')
        //     ->where( $input )
        //     ->delete();

        $note = Notes::find($request['id'])->delete();
        if ($note) 
        {
            // $note->is_archived = 1;
            // if ($note->save()) 
            // {
                return response()->json(['message' => 'Note Deleted Successfully'],200);
            // }
            // else {
            //     return response()->json(['message' => 'Error while Archiving'],400);
            // }
        }
        else 
        {
            return response()->json(['message' => 'Note Id Invalid'],404);
        }
    }

    

    public function setReminder(Request $request)
    {
        // $input = Input::all();
        // DB::table('notes')
        //     ->where(['id' => $input['id']])
        //     ->update(['reminder' => $input['reminder']]);

        $note = Notes::find($request['id']);
        if ($note) 
        {
            $note->reminder = $request['reminder'];
            if ($note->save()) {
                return response()->json(['message' => 'Note Reminder Set Successfully'],200);
            }
            else 
            {
                return response()->json(['message' => 'Error While setting Reminder'],400);
            }
        }
        else 
        {
            return reponse()->json(['message' => 'Note id not found'],404);
        }
    }

    public function displayReminder()
    {
        $date = date("Y/m/d");
        // $notes = DB::table('notes')
        //              ->where(['reminder' => $date])
        //              ->get();

        $notes = Notes::where(['reminder' => $date])->all();

        foreach ($notes as $note) 
        {
            echo "<pre>";
            echo json_encode($note)."<br>";
        }
    }

    // public function addCollab(Request $request)
    // {
    //     $notes = Notes::find($request['notes_id']);
    //     $user = Users::where('email',$request['email'])->first();

    //     $user_id = $notes->user_id;

    //     if ($notes->users->contains($user)) 
    //     {
    //         return response()->json(['message' => 'Collaborator already Added'],400);            
    //     }
    //     else 
    //     {
    //         $notes->users()->save($user);
            
    //         $note = Notes::create([
    //             'title' => $notes->title,
    //             'description' => $notes->description,
    //             'user_id' => $user->id
    //             ]);
    
    //         $note_id = $note->id;
    
    //         $notes = Notes::find($note_id);
    //         $user = Users::find($user_id);
    
    //         if ($notes->users->contains($user)) 
    //         {
    //             return response()->json(['message' => 'Collaborator already Added'],400);            
    //         }
    //         else 
    //         {
    //             $note->users()->save($user); 
    //             return response()->json(['message' => 'Collaborator Added'],200);               
    //         }
    //     }
       
    // }
}
?>