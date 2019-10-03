<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Libraries\RBMQSender;
use App\Model\Users;
use Illuminate\Support\Facades\Hash;
use Cloudinary\Uploader;

class UserController extends Controller
{
    
    public function registerUser(Request $request)
    {

        $validator = Validator::make($request->all(),
                [
                    'firstname' => 'required|alpha',
                    'lastname' => 'required|alpha',
                    'email' => 'required|email',
                    'password' => 'required|min:8',
                    'confirm_password' => 'required|same:password'
                ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->errors()],400);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = Users::create($input);
        $token = $user->createToken('star')->accessToken;

        Redis::set($token,$token);
        $toEmail = $user->email;   

        $rabbitmq = new RBMQSender();

        $subject = "Please verify email for login";
        $message = "Hi ".$user->firstname." ".$user->lastname.", \nThis is email verification mail from Fundoo Login Register system.\nFor complete registration process and login into system you have to verify you email by click this link.\n".url('/')."/register/verifyEmail/".$token."\nOnce you click this link your email will be verified and you can login into system.\nThanks.";

        if($rabbitmq->sendRabQueue($toEmail,$subject,$message))
        {
            return response()->json(['success' => $token, 'message'=> 'Please Check Mail for Email Verification.'],200);
        }
        else 
        {
            return response()->json(['success' => $token, 'message'=> 'Error While Sending Mail.'],400);
        }
        
    }

    public function verifyEmail($token)
    {

        $token = Redis::get($token);
        if (!$token) 
        {
            return response()->json(['message' => 'UnAuthorized Token'],400);
        }

        $tokenArray = preg_split("/\./",$token);
        $decodetoken = base64_decode($tokenArray[1]);
        $decodetoken = json_decode($decodetoken,true);
        $user_id = $decodetoken['sub'];

        $user = Users::where(['id' => $user_id])->first();
        if ($user) 
        { 
            if ($user->email_verified) 
            {
                return response()->json(['message' => "Verification of email has already done."],400);   
            }
            else 
            {
                $user->email_verified = 1;
                $user->save();
                Redis::del($token);
                return response()->json(['message' => 'Verification of email has Done Successfully.'],200); 
                
            }
            
        }
        else 
        {
            return response()->json(['message' => 'Unauthorized Token.'],400);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->errors()],400);
        }

        $input = $request->all();
        
        $credential = ['email' => $input['email'], 'password' => $input['password']];
        if (Auth::attempt($credential)) 
        {
            $user = Auth::user();
            $token = $user->createToken('star')->accessToken;
                if ($user->email_verified) 
                {
                    return response()->json(['message' => 'Valid User.','data' => $token],200);   
                }
                else 
                {
                    return response()->json(['message' => 'Please Verify Your Email.'],400);           
                }
        }
        else
        {
            return response()->json(['message' => 'Invalid User.'],400);
        }
    }

    public function forgetPassword(Request $request)
    {
        
        $validator = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->errors()],201);
        }

        $input = $request->all();
        $user = Users::where($input)->first();

        if ($user) 
        {

            $token = $user->createToken('star')->accessToken;
            Redis::set($token,$token);

            $rabbitmq = new RBMQSender();

            $subject = "Please verify email to reset your password";
            $message = "Hi ".$user->firstname." ".$user->lastname.", \nThis is email verification mail from Fundoo Login Register system.\nFor complete reset password process and login into system you have to verify you email by click this link.\n".url('/')."/api/resetpassword/".$token."\nOnce you click this link your email will be verified and you can login into system.\nThanks.";

            if($rabbitmq->sendRabQueue($input['email'],$subject,$message))
            {
                return response()->json(['success' => $token, 'message'=> 'Please Check Mail for Email Verification.'],200);
            }
            else 
            {
                return response()->json(['success' => $token, 'message'=> 'Error While Sending Mail.'],400);
            }
        }
        else 
        {
            return response()->json(['message' => 'Email id is not Registered'],400);
        }
    }

    public function resetPassword(Request $request,$token)
    {
        $token = Redis::get($token);
        if (!$token) 
        {
            return response()->json(['message' => 'UnAuthorized token'],400);
        }

        $tokenArray = preg_split("/\./",$token);
        $decodetoken = base64_decode($tokenArray[1]);
        $decodetoken = json_decode($decodetoken,true);
        $user_id = $decodetoken['sub'];

        $user = Users::where(['id' => $user_id])->first();

        if ($user) 
        {
            $user->password = Hash::make($request['password']);
            $user->save();
            Redis::del($token);
            return response()->json(['message' => 'Password is Setted'],200);   
        }
        else 
        {
            return response()->json(['message' => 'Unathorized token'],400);
        }
        
    }

    public function userDetail(Request $request)
    {
        $token = $request->header('Authorization');
        $tokenArray = preg_split("/\./",$token);
        $decodetoken = base64_decode($tokenArray[1]);
        $decodetoken = json_decode($decodetoken,true);
        $user_id = $decodetoken['sub'];

        $user = Users::where('id',$user_id)->get(['firstname','lastname','email']);

        return response()->json(['data' => $user],200);
    }

    public function uploadImage(Request $request)
    {
        
        $token = $request->header('Authorization');
        $tokenArray = preg_split("/\./",$token);
        $decodetoken = base64_decode($tokenArray[1]);
        $decodetoken = json_decode($decodetoken,true);
        $user_id = $decodetoken['sub'];
        
        $user = Users::find($user_id);

        if ($user) 
        {
            if ($user->profile_pics == 'default') 
            {
                $response = $this->upload($request['path'],$user);
                return $response;
            }
            else 
            {
                $tag = $user->profile_pics;
                $delete = Uploader::destroy($tag);
                if ($delete) 
                {
                     $response = $this->upload($request['path'],$user);  
                     return $response;                         
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
            return response()->json(['message' => 'Error while Uploading'],400);
        }
    }

    public function displayImage(Request $request)
    {
        $token = $request->header('Authorization');
        $tokenArray = preg_split("/\./",$token);
        $decodetoken = base64_decode($tokenArray[1]);
        $decodetoken = json_decode($decodetoken,true);
        $user_id = $decodetoken['sub'];

        $user = Users::find($user_id);

        if ($user) 
        {
            $tag = $user->profile_pics;
            $img = cloudinary_url($tag,array("width" => 100, "height" => 150, "crop" => "fill","gravity"=>"faces"));
            return response()->json(['data' => $img],200);
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
            if ($user->profile_pics == 'default') 
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

    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(),
                [
                    'id' => 'required',
                    'firstname' => 'required|alpha',
                    'lastname' => 'required|alpha',
                    'profile_pics' => 'required',

                ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->errors()],400);
        }

        $input = $request->all();

        $user = Users::find($input['id']);
        if (!$user) 
        {
            $user = Users::create($input);
        }

        if ($user) 
        {
            $token = $user->createToken('star')->accessToken;
             
            return response()->json(['message' => 'Authorized User','token' => $token],200);
        }
        else 
        {
            return response()->json(['message' => 'Unauthorized User'],400);
        }
    }

}


