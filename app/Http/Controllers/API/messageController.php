<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\Emails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class messageController extends Controller
{
    public function index()
    {
        $data = 'tes';

        if($data == 'tes'){
            return ApiFormatter::createApi(200,'success', $data);
        }else{
            return ApiFormatter::createApi(400,'fail');
        }
    }

    public function store(Request $request)
    {
        $newMessage = Emails::create([
            'name'      => $request->name,
            'email'      => $request->email,
            'message'      => $request->message,
        ]);

        $discord_webhook = env('DISCORD_WEBHOOK');

        $notif = Http::post(
            $discord_webhook,
            [
                'content' => "New Message from: \nName: " . $request->name . "\nEmail: " . $request->email . "\nMessage: " . $request->message
            ]             
        );
        

        return ApiFormatter::createApi(200, 'success', 'berhasil');
    }
}
