<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Cloudinary\Uploader;
use App\Users;

class ImageUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(),
                [
                    'photo' => 'required|image|mimes:jpeg,jpg,bmp,png'
                ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->errors()],400);
        }
        
        $user = Users::find($request['id']);

        if ($user) 
        {
            if ($user->profile_pics == null) 
            {
                $this->upload($request['photo'],$user);
            }
            else 
            {
                $tag = $user->profile_pics;
                $delete = Uploader::destroy($tag);
                if ($delete) 
                {
                     $this->upload($request['photo'],$user);                           
                }
            }
            
        }
        else 
        {
            return response()->json(['message' => 'Id not found'],404);
        }        
    }

    public function upload($photo,$user)
    {
        $upload = Uploader::upload($photo);
        $pics = $upload['public_id'];
        $user->profile_pics = $pics;

        if ($user->save()) 
        {
            return response()->json(['message' => 'Successfully Uploaded'],200);
        }
        else 
        {
            return response()->json(['message' => 'Error while Uploadind'],400);
        }
    }

    public function displayImage(Request $request)
    {
        $user = Users::find($request['id']);

        if ($user) 
        {
            $tag = $user->profile_pics;
            $img = cl_image_tag($tag);
            print_r($img);
            return response()->json(['message' => 'Successfully Uploaded'],200);
        }
        else 
        {
            return response()->json(['message' => 'Id not Found'],404);
        }
    }

    public function removeImage(Request $request)
    {
        $user = Users::find($request['id']);

        if ($user) 
        {
            if ($user->profile_pics == null) 
            {
                return response()->json(['message' => 'Profile pics is already removed']);
            }
            else 
            {
                $tag = $user->profile_pics;
                $delete = Uploader::destroy($tag);
                $user->profile_pics = null;
                $user->save();
                if ($delete) 
                {
                    return response()->json(['message' => 'Profile pics is deleted'],200);
                }
                else 
                {
                    return response()->json(['message' => 'Error while deleting'],400);
                }
            }
        }
        else 
        {
            return response()->json(['message' => 'Id not found'],404);
        }
    }

}
