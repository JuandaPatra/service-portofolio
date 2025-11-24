<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;
class Webhook
{
    

    public static function send($url, $payload)
    {
       $notif = Http::post(
            $url,
            $payload             
        );
    }
}
