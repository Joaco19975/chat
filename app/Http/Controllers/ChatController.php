<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function chat_with(User $user)
    {
        $user_a = auth()->user();

        $user_b = $user;

        $chat = $user_a->chats()->wherehas('users', function($q) use ($user_b){
            $q->where('chat_user.user_id', $user_b->id);
        })->first();

        if(!$chat)
        {
            $chat = \App\Models\Chat::create([]);

            $chat->users()->sync([$user_a->id, $user_b->id]);
        }

        return redirect()->route('chat.show', $chat);

    }

    public function show(Chat $chat)
    {
        /*va a abortar la conexion excepto si se cumple la
        condicion que vamos a pasar como primer
        primer parametro, sino se pasa el error 403 */
        abort_unless($chat->users->contains(auth()->id()), 403);


        return view('chat', [
            'chat' => $chat
        ]);
    }

   
}
