<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\RBMQSender;
use App\Http\Controllers\JWT;
use App\User;


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
            return response()->json(['error' => $validator->errors()],201);
        }

        $input = $request->all();
        $input['created_at'] = now();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $toEmail = $user->email;   

        $key = ['id' => $user->id];
        $key = json_encode($key);
        $token = JWT::GenerateToken($key);

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
        $key = JWT::DecodeToken($token);
        $key = json_decode($key,true);
        $user = User::where(['id' => $key['id']])->first();
        if ($user) 
        { 
            if ($user->email_verified == 0) 
            {
                $user->email_verified = 1;
                $user->save();
                return response()->json(['message' => 'Verification of email has Done Successfully.'],200);    
            }
            else 
            {
                return response()->json(['message' => "Verification of email has already done."],400);
            }
            
        }
        else 
        {
            return response()->json(['message' => 'Unauthorized Token.'],400);
        }
    }
}
