<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPConnection;

class RBMQReceiver extends Controller
{
    public function sendMail()
    {
        $connection = new AMQPConnection('localhost', 5672, 'admin', 'admin');
        $channel = $connection->channel();
        
        $channel->queue_declare('email_queue', false, false, false, false);
        
        
        $callback = function($msg){
        
            $data = json_decode($msg->body, true);
            
            $from = 'Rahil Sayed';
            $from_email = 'rahil@gmail.com';
            $to_email = $data['toEmail'];
            $subject = $data['subject'];
            $message = $data['message'];
        
            $transporter = (new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
                                            ->setUsername($from_email)
                                            ->setPassword('1245789635');
        
            $mailer = new \Swift_Mailer($transporter);  
        
            $body = (new \Swift_Message($transporter))
                        ->setSubject($subject)
                        ->setFrom([$from_email => $from])
                        ->setTo(array($to_email))
                        ->setBody($message);
        
            $mailer->send($body);
        
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_qos(null, 1, null);        
        $channel->basic_consume('email_queue', '', false, false, false, false, $callback);

        while(count($channel->callbacks)) 
        {
            $channel->wait();
        }

        $channel->close();
        $connection->close();   
            
    }
}
