<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Config;

class WebGoogleAuthController extends Controller
{
    public function redirect()
    {
        Config::set('services.google.client_id', env('WEB_GOOGLE_CLIENT_ID'));
        Config::set('services.google.client_secret', env('WEB_GOOGLE_CLIENT_SECRET'));
        Config::set('services.google.redirect', env('WEB_GOOGLE_REDIRECT_URI'));

        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {

        Config::set('services.google.client_id', env('WEB_GOOGLE_CLIENT_ID'));
        Config::set('services.google.client_secret', env('WEB_GOOGLE_CLIENT_SECRET'));
        Config::set('services.google.redirect', env('WEB_GOOGLE_REDIRECT_URI'));

        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt(Str::random(24)), // dummy password
                'role' => 'visitor_logged', // bisa 'visitor_logged', 'entrepreneur', 'admin'
            ]
        );

        $token = $user->createToken('api-token')->plainTextToken;

        // return response()->json([
        //     'access_token' => $token,
        //     'user' => $user,
        // ]);

        // dd('pelm');
        // update
        $redirectUrl = config('services.frontend_url') . '/auth/callback/google?' . http_build_query([
            'access_token' => $token,
            'user' => json_encode($user),
        ]);

        return redirect($redirectUrl);
    }

    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
