<?php

namespace App\Http\Controllers\Auth;

use Google_Client;
use Laravel\Sanctum\HasApiTokens;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
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
        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ]);

        // update
        // $redirectUrl = config('services.frontend_url') . '/login/callback?' . http_build_query([
        //     'access_token' => $token,
        //     'user' => json_encode($user),
        // ]);

        // return redirect($redirectUrl);
    }

    public function mobileCallback(Request $request)
    {

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'password' => bcrypt(Str::random(24)),
                    'role' => 'visitor_logged',
                ]
            );

            $token = $user->createToken('api-token')->plainTextToken;
            // dd();
            return response()->json([

                'access_token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login gagal',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function handleMobileLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->id_token);

            if ($payload) {
                $email = $payload['email'];
                $name = $payload['name'];

                // Buat atau ambil user
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => bcrypt(Str::random(24)), // password random
                        'role' => 'visitor_logged',
                    ]
                );

                // Buat token aplikasi
                $token = $user->createToken('api-token')->plainTextToken;

                return response()->json([
                    'access_token' => $token,
                    'user' => $user,
                ]);
            } else {
                return response()->json([
                    'message' => 'ID Token tidak valid',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat verifikasi Google ID Token',
                'error' => $e->getMessage(),
            ], 500);
        }
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
