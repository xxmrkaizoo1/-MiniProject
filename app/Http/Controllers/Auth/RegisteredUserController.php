<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $otp = (string) random_int(100000, 999999);

        $request->session()->put('registration.pending', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10)->toIso8601String(),
        ]);

        Mail::raw("Your OTP code is {$otp}. It expires in 10 minutes.", function ($message) use ($validated): void {
            $message->to($validated['email'])
                ->subject('Your registration OTP');
        });

        return redirect()->route('register.verify-otp')
            ->with('status', 'We sent an OTP code to your email.');
    }

    public function showOtpForm(Request $request): RedirectResponse|View
    {
        if (! $request->session()->has('registration.pending')) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $pending = $request->session()->get('registration.pending');

        if (! $pending) {
            return redirect()->route('register')
                ->withErrors(['otp' => 'Registration session expired. Please register again.']);
        }

        if (now()->greaterThan(Carbon::parse($pending['otp_expires_at']))) {
            return back()->withErrors(['otp' => 'OTP expired. Please request a new OTP.']);
        }

        if ($pending['otp'] !== $request->string('otp')->toString()) {
            return back()->withErrors(['otp' => 'Invalid OTP code.']);
        }

        if (User::where('email', $pending['email'])->exists()) {
            $request->session()->forget('registration.pending');

            return redirect()->route('register')
                ->withErrors(['email' => 'Email already registered. Please login.']);
        }

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'password' => $pending['password'],
        ]);

        $request->session()->forget('registration.pending');

        Auth::login($user);

        return redirect($user->landingUrl())
            ->with('success', 'Registration completed successfully.');
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('registration.pending');

        if (! $pending) {
            return redirect()->route('register')
                ->withErrors(['email' => 'Registration session expired. Please register again.']);
        }

        $otp = (string) random_int(100000, 999999);

        $pending['otp'] = $otp;
        $pending['otp_expires_at'] = now()->addMinutes(10)->toIso8601String();

        $request->session()->put('registration.pending', $pending);

        Mail::raw("Your OTP code is {$otp}. It expires in 10 minutes.", function ($message) use ($pending): void {
            $message->to($pending['email'])
                ->subject('Your registration OTP');
        });

        return back()->with('status', 'A new OTP has been sent to your email.');
    }
}
