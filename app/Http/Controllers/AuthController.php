<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function saveTheme(Request $request)
    {
        $data = $request->validate([
            'mode'    => 'required|in:dark,light',
            'accent'  => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'surface' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $user = auth()->user();
        $user->ui_settings = json_encode($data);
        $user->save();

        return response()->json(['success' => true]);
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    public function doLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $key = 'login.' . Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Çok fazla hatalı giriş denemesi. {$seconds} saniye sonra tekrar deneyin."
            ])->withInput();
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->intended(route('adisyon.index'));
        }

        RateLimiter::hit($key, 60); // 60 saniye kilitlenme penceresi

        $remaining = 5 - RateLimiter::attempts($key);
        $msg = $remaining > 0
            ? "E-posta veya şifre hatalı. {$remaining} deneme hakkınız kaldı."
            : 'Çok fazla hatalı giriş. 60 saniye sonra tekrar deneyin.';

        return back()->withErrors(['email' => $msg])->withInput();
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function doRegister(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'menu_token' => \Illuminate\Support\Str::random(20),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('subscription.select');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ── Şifre sıfırlama ────────────────────────────────────────────

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.')
            : back()->withErrors(['email' => 'Bu e-posta adresiyle kayıtlı hesap bulunamadı.']);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Şifreniz başarıyla güncellendi. Giriş yapabilirsiniz.')
            : back()->withErrors(['email' => 'Geçersiz veya süresi dolmuş bağlantı. Tekrar deneyin.']);
    }
}
