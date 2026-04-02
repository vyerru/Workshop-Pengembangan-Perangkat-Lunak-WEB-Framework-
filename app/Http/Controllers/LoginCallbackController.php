<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class LoginCallbackController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal autentikasi Google.');
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->email],
            [
                'name' => $googleUser->name,
                'id_google' => $googleUser->id,
                'password' => $user->password ?? bcrypt(Str::random(16)),
            ]
        );

        $otpCode = Str::random(6);
        $user->update(['otp' => $otpCode]);

        Mail::to($user->email)->send(new OtpMail($otpCode));

        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.view');
    }

    public function otpView()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('auth.otp'); 
    }

    public function otpVerify(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $userId = session('otp_user_id');
        if (!$userId) return redirect('/login');

        $user = User::findOrFail($userId);

        // 5. Validasi Kode
        if ($request->otp == $user->otp) {
            // Login Resmi
            Auth::login($user);

            // Bersihkan data OTP dan Session
            $user->update(['otp' => null]);
            session()->forget('otp_user_id');

            return redirect('/home');
        }

        return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
    }
}
