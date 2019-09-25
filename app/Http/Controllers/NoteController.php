<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
Use App\Model\Notes;
Use App\Model\Users;
Use App\Exceptions\Handler;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function createNote(Request $request)
    {
        $input = $request->all();
        $token = $request->header('Authorization');
        $tokenArray = preg_split("/\./",$token);
        $decodetoken = base64_decode($tokenArray[1]);
        $decodetoken = json_decode($decodetoken,true);
        $user_id = $decodetoken['sub'];
        // unset($input['token']);
        $input['user_id'] = $user_id;
        
        
        if ($input['title'] == null && $input['description'] == null) 
        {
            return response()->json(['Message' => 'Title & description must not be empty'],400);         
        }
        else 
        {
            $note = Notes::create($input);

            return response()->json(['Message' => 'Note Created Successfully'],200);
        }
        
    }

    public function editNote(Request $request)
    {
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
                $note->save();
                return response()->json(['message' => 'Note Updated Successfully'],200);
            }
        }
        else {
            return response()->json(['message' => 'Unauthorized Note Id'],404);
        }
    }

    public function displayNote(Request $request)
    {
        $token = $request->header('Authorization');
        $tokenArray = preg_split("/\./",$token);
        $decodetoken = base64_decode($tokenArray[1]);
        $decodetoken = json_decode($decodetoken,true);
        $user_id = $decodetoken['sub'];

        $notes = Notes::with('labels')->where(['user_id' => $user_id,'is_trash' => '0' , 'is_archived' => '0'])
                        ->get(['id','title','description','color']);

        return response()->json(['data' => $notes],200);
       
    }

    public function trashNote(Request $request)
    {
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
        $token = $request->header('Authorization');
        $tokenArray = preg_split("/\./",$token);
        $decodetoken = base64_decode($tokenArray[1]);
        $decodetoken = json_decode($decodetoken,true);
        $user_id = $decodetoken['sub'];

        $notes = Notes::with('labels')->where(['user_id' => $user_id,'is_trash' => '1'])
                        ->get(['id','title','description','color']);

        return response()->json(['data' => $notes],200);
    }

    public function restoreNote(Request $request)
    {
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
        $token = $request->header('Authorization');
        $tokenArray = preg_split("/\./",$token);
        $decodetoken = base64_decode($tokenArray[1]);
        $decodetoken = json_decode($decodetoken,true);
        $user_id = $decodetoken['sub'];

        $notes = Notes::with('labels')->where(['user_id' => $user_id,'is_archived' => '1','is_trash' => '0'])
                        ->get(['id','title','description','color']);

        return response()->json(['data' => $notes],200);
    }

    public function deleteNote($id)
    {
        $note = Notes::find($id)->delete();
        if ($note) 
        {
            return response()->json(['message' => 'Note Deleted Successfully'],200);
        }
        else 
        {
            return response()->json(['message' => 'Note Id Invalid'],404);
        }
    }

    public function setColor(Request $request)
    {
        $note = Notes::find($request['id']);
        if ($note) 
        {
            $note->color = $request['color'];
            if ($note->save()) {
                return response()->json(['message' => 'Note background color changed'],200);
            }
            else {
                return response()->json(['message' => 'Error while changing background color'],400);
            }
        }
        else 
        {
            return reponse()->json(['message' => 'Note id not found'],404);
        }
    }

    public function setReminder(Request $request)
    {
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

        $notes = Notes::where(['reminder' => $date])->all();

        foreach ($notes as $note) 
        {
            echo "<pre>";
            echo json_encode($note)."<br>";
        }
    }

}
?>