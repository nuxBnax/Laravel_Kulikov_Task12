<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
   public function redirectToProvider()
   {
        return Socialite::driver('google')->redirect();
   }

   public function handleProviderCallback()
   {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $exception) {
            // в случае ошибки будем его редиректить в самое начало
            return redirect('/');
        }
        // проверка на существование пользователя
        $existingUser = User::where('email', $user->email)->first();

        
        if ($existingUser) { 
            auth()->login($existingUser);
            // если юзер есть, то передаем логин
        } else {
            // если его нет, то его нужно создать
            $newUser = new User();
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->google_id = $user->id;
            $newUser->save();

            auth()->login($newUser);
        }
        return redirect()->to('/dashboard');
   }
}
