<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Labels;
use App\Model\Notes;
use App\Model\LabelsNotes;

class LabelController extends Controller
{
    public function createLabel(Request $request)
    {
        $token = $request->header('Authorization');
        $tokenArray = preg_split("/\./",$token);
        $decodetoken = base64_decode($tokenArray[1]);
        $decodetoken = json_decode($decodetoken,true);
        $user_id = $decodetoken['sub'];
        $request['user_id'] = $user_id;
        
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

    public function deleteLabel($id)
    {
        $delete = Labels::where('id',$id)->delete();
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

    public function displayLabel(Request $request)
    {
        $token = $request->header('Authorization');
        $tokenArray = preg_split("/\./",$token);
        $decodetoken = base64_decode($tokenArray[1]);
        $decodetoken = json_decode($decodetoken,true);
        $user_id = $decodetoken['sub'];

        $label = Labels::with('notes')->where(['user_id' => $user_id])->get(['id','name']);

        return response()->json(['data' => $label],200);
    }

    public function createNoteLabel(Request $request)
    {
        $note =  Notes::find($request['notes_id']);
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

    public function deleteNoteLabel($id)
    {
        $labnot = LabelsNotes::where('id',$id)->delete();
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
