<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;


class APITokenController extends Controller
{
    public function create_token(Request $request)
    {
        //$data = session()->get('data');
        $data = json_decode(request()->input('data'), true);
        $email = $data['email'];
       
        Log::info('token start tworzenia'. $email);
                   
        $user = User::where('email', $data['email'])->first();
        $userReturn = User::where('email', $user['email'])->first();
        
        if (! $user ||! Hash::check($data['password'], $user->password)) 
        {
            return [
            'error' => 'The provided credentials are incorrect.'
            ];
        }
        $userReturn->id = $user->id;
        Log::info('token utworzony dla: ' .  $user->id);
        $userReturn->token =$userReturn->createToken($data['nick'])->plainTextToken;
        //$userReturn->createToken($data['nick'])->plainTextToken;
        return $userReturn;
       
    }
}