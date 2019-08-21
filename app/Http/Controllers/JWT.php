<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT as FJWT;

class JWT extends Controller
{
    public static function GenerateToken($data)
    {          
        $jwt = FJWT::encode($data,$key = "star");
        return $jwt;
    }
     
    public static function DecodeToken($token)
    {          
        $decoded = FJWT::decode($token,$key = "star", array('HS256'));
        return $decoded;
    }
}
