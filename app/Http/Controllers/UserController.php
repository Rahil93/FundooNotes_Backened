<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\RBMQSender;
use App\Http\Controllers\JWT;
use App\Users;


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
        $input['password'] = md5($input['password']);
        $user = Users::create($input);
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
        $user = Users::where(['id' => $key['id']])->first();
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

    public function login(Request $request)
    {
        $input = $request->all();
        $input['password'] = md5($input['password']);

        $user = Users::where('email',$input['email'])->first();
        if ($user) 
        {
            if ($user->password === $input['password']) {
                if ($user->email_verified === 1) 
                {
                    return response()->json(['message' => 'Valid User.'],200);   
                }
                else 
                {
                    return response()->json(['message' => 'Please Verify Your Email.'],400);           
                }
            }
            else 
            {
                return response()->json(['message' => 'Invalid Password.'],400);
            }  
            
        }
        else
        {
            return response()->json(['message' => 'Invalid Email.'],400);
        }
    }

    public function forgetPassword(Request $request)
    {
        
        $validator = Validator::make($request->all(),
                [
                    'email' => 'required|email:users',
                ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->errors()],201);
        }
        $input = $request->all();
        $user = Users::where($input)->first();
        if ($user) 
        {
            $key = json_encode($input);
            $token = JWT::GenerateToken($key);

            $rabbitmq = new RBMQSender();

            $subject = "Please verify email to reset your password";
            $message = "Hi ".$user->firstname." ".$user->lastname.", \nThis is email verification mail from Fundoo Login Register system.\nFor complete reset password process and login into system you have to verify you email by click this link.\n".url('/')."/api/verify/".$token."\nOnce you click this link your email will be verified and you can login into system.\nThanks.";

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

    public function getToken($token)
    {
        Redis::set('token',$token);
    }

    public function resetPassword(Request $request)
    {
        $token = Redis::get('token');
        $key = JWT::DecodeToken($token);
        $key = json_decode($key,true);
        $user = Users::where(['email' => $key["email"]])->first();

        if ($user) 
        {
            $user->password = md5($request['password']);
            $user->save();
            return response()->json(['message' => 'Password is Setted'],200);   
        }
        else 
        {
            return response()->json(['message' => 'Unathorized token'],400);
        }
        
    }

}


