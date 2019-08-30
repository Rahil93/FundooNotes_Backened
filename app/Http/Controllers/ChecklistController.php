<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\Notes;

class ChecklistController extends Controller
{
    public function deleteChecklist(Request $request)
    {
        $desc = Notes::where(['id' => $request['id']])->pluck('description')->all();
        $description = $request['description'];
        $res = preg_replace("/\s$description/","",$desc);
        $note = Notes::where(['id' => $request['id']])->first();
        $note->description = $res[0];
        $note->save();
    }
}
