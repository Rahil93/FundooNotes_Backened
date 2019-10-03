<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Model\Notes;
Use App\Model\Users;
Use App\Model\UsersNotes;
use Illuminate\Support\Facades\Validator;

class CollabController extends Controller
{
    public function addCollab(Request $request)
    {
        $validator = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'notes_id' => 'required'
                ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()],400);
        }

        $notes = Notes::find($request['notes_id']);
        $user = Users::where('email',$request['email'])->first();

        if ($notes->users->contains($user)) 
        {
            return response()->json(['message' => 'Collaborator already Added'],400);            
        }
        else 
        {
            $collab = $notes->users()->save($user);
            if ($collab) 
            {
                return response()->json(['message' => 'Collaborator Added'],200);               
            }
            else 
            {
                return response()->json(['message' => 'Error while Adding'],400);                               
            }
        }
    }

    public function removeCollab($id)
    {
       $usrnote = UsersNotes::where('id',$id)->delete();
        
        if($usrnote)
        {
            return response()->json(['message' => 'Successfully Remove'],200);                
        }
        else 
        {
            return response()->json(['message' => 'Error While Removing'],400);
        }
    }
}
