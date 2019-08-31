<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Labels;

class LabelController extends Controller
{
    public function createLabel(Request $request)
    {
        if ($request['name'] == null) 
        {
            return response()->json(['message' => "Label name must not be empty"],400);
        }
        else 
        {
            $label = Labels::create($request->all());
            if ($label) 
            {
                return response()->json(['message' => "Label created Successfull"],200);                
            }
            else 
            {
                return response()->json(['message' => "Error while creating label"],400);                
            }
        }
        
    }

    public function deleteLabel(Request $request)
    {
        $delete = Labels::where('id',$request['id'])->delete();
        if ($delete) 
        {
            return response()->json(['message' => "Label deleted Successfully"],200);            
        }
        else 
        {
            return response()->json(['message' => "Error While deleting label"],400);            
        }
    }

    public function editLabel(Request $request)
    {
        $edit = Labels::where('id',$request['id'])->first();
        if ($edit) 
        {
            if ($request['name'] != null) 
            {
                $edit->name = $request['name']; 
                $save = $edit->save();
                if($save)
                {
                    return response()->json(['message' => 'Label updated successfully'],200);
                }
                else 
                {
                    return response()->jston(['message' => 'Error While editing label'],400);
                }
            }
            else 
            {
                return response()->json(['message' => 'Label name must not be empty'],400);
            }
                 
        }
        else 
        {
            return response()->json(['message' => 'Error While editing label'],400);
        }
    }

    public function createNoteLabel(Request $request)
    {
        $note = \App\Notes::find($request['notes_id']);
        $label = Labels::find($request['labels_id']);

        if ($note->labels->contains($label)) 
        {
            return response()->json(['message' => 'Already Added'],400);
        }
        else 
        {
            if ($note->labels()->save($label)) 
            {
                return response()->json(['message' => 'Added Successfully'],200);                
            }
            else 
            {
                return response()->json(['message' => 'Error while adding'],400);               
            }
        }
    }

    public function deleteNoteLabel(Request $request)
    {
        $note = \App\Notes::find($request['a']);
        $labnot = $note->labels()->detach($request['labels_id']);
        if ($labnot) 
        {
            return response()->json(['message' => 'Remove successfully'],200);
        }
        else 
        {
            return response()->json(['message' => 'Error while removing'],400);
        }
    }
}
