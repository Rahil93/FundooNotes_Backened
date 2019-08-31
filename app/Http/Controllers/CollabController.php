<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Model\Notes;
Use App\Model\Users;

class CollabController extends Controller
{
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

    public function removeCollab(Request $request)
    {
        $notes = Notes::find($request['notes_id']);
        $user = Users::where('email',$request['email'])->first();

        $user_id = $notes->user_id;
        $title = $notes->title;
        $id = $user->id;
        
        if($notes->users()->detach($user))
        {
            $notes = Notes::where(['title' => $title, 'user_id' => $id])->first();
            $user = Users::find($user_id);
            $notes->users()->detach($user);

            return response()->json(['message' => 'Successfully Remove'],200);                
        }
        else 
        {
            return response()->json(['message' => 'Error While Removing'],400);
        }
    }
}
