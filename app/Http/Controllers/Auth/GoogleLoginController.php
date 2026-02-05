<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleLoginController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return redirect()
            ->route('register')
            ->withInput(['email' => $validated['email']]);
    }
}
