<?php

namespace App\Libraries;

use Illuminate\Http\Request;
Use App\Model\Notes;
Use App\Model\Users;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class FirebaseNotification
{
    public function pushNotification()
    {
        $currentDateTime = date("Y-m-d H:i:00");

        $notes = Notes::where('reminder',$currentDateTime)->get(['title','description','user_id']);
        
        foreach ($notes as $note) {
            $user_id = $note['user_id'];
            $user = Users::find($user_id);
            $token = $user->firebase_token;
            if ($token) {
                $client = new Client();
                $client->request('POST','https://fcm.googleapis.com/fcm/send',[
                    'headers' => [
                        'Authorization' => 'key=AAAAZrYkQwY:APA91bFAi5Iw6NF5SOyFkBmCZZbpFWYiKyfritfkd_ho8BXT0H0vzjMyvy-tfcYiCGQASHRTaTvZw7aVr8zitNU3NfPwfXr_mlYhN8ZZtcpvjXmXzZ_Hp1jfmVby_Mq4saeAPBAuN2pC',
                        'Content-Type' => 'application/json'
                    ],
                    "json" => [
                        "notification" => [
                            "title" => $note['title'], 
                            "body" => $note['description'],
                            "click_action" => "http://localhost:4200/dashboard/notes"
                        ],
            
                        "to" => $token
                    ]
                ]);
            }
        }
    }
}



?>