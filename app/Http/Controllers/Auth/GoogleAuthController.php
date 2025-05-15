<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
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

        // dd('asfsaf');
        // return response()->json([
        //     'access_token' => $token,
        //     'user' => $user,
        // ]);

        // update
        $redirectUrl = config('services.frontend_url') . '/login/callback?' . http_build_query([
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
