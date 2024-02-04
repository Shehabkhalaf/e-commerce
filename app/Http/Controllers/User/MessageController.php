<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddMessageRequest;
use App\Models\Message;
use App\Traits\apiResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use apiResponse;
    public function sendMessage(AddMessageRequest $request)
    {
       $message = Message::create($request->only([
           'name',
           'email',
           'number',
           'message'
       ]));
       if($message)
           return $this->jsonResponse(201,'Message is sent.',$message);
       else
           return $this->jsonResponse(500,'Something went wrong.');
    }
}
