<x-guest-layout>
    <div class="space-y-6 animate-fade-up">
        <div class="space-y-2 text-center">
            <p class="inline-flex items-center rounded-full border border-indigo-500/20 bg-indigo-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-indigo-300">
                Welcome Back
            </p>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sign in to Student Voice Hub</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">Track feedback, mood trends, and classroom insights in one place.</p>
        </div>

        <x-auth-session-status class="rounded-lg border border-emerald-400/40 bg-emerald-100/60 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-500/10 dark:text-emerald-300" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-200" />
                <x-text-input id="email" class="mt-1 block w-full rounded-xl border-gray-300/80 bg-white/80 transition duration-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900/80" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 dark:text-gray-200" />
                <x-text-input id="password" class="mt-1 block w-full rounded-xl border-gray-300/80 bg-white/80 transition duration-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900/80"
                              type="password"
                              name="password"
                              required
                              autocomplete="current-password"
                              placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between gap-3">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-1">
                <x-primary-button class="animate-glow-pulse justify-center rounded-xl px-5 py-2.5 text-sm font-semibold">
                    {{ __('Log in') }}
                </x-primary-button>

                <x-secondary-button class="rounded-xl border-gray-300/80 bg-gray-50/90 px-4 py-2.5 text-sm hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900" type="button" id="quick-login">
                    {{ __('Quick login') }}
                </x-secondary-button>
            </div>

            <div id="quick-login-menu" class="hidden rounded-2xl border border-gray-200 bg-gray-50/80 p-4 text-sm text-gray-700 backdrop-blur dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-200">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">
                    {{ __('Choose an account') }}
                </p>
                <div class="space-y-2">
                    <button type="button" class="quick-login-option w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-left transition hover:-translate-y-0.5 hover:bg-indigo-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700/60" data-quick-action="google">
                        <span class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300">G</span>
                            <span>
                                <span class="block font-medium">{{ __('Google Account') }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">{{ __('Use an existing Google email') }}</span>
                            </span>
                        </span>
                    </button>

                    <button type="button" class="quick-login-option w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-left transition hover:-translate-y-0.5 hover:bg-emerald-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700/60" data-quick-action="demo" data-quick-email="demo@gmail.com" data-quick-password="password">
                        <span class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300">D</span>
                            <span>
                                <span class="block font-medium">{{ __('Demo Account') }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">demo@gmail.com</span>
                            </span>
                        </span>
                    </button>
                </div>
            </div>
        </form>

        <p class="text-center text-xs text-gray-500 dark:text-gray-400">
            New here?
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200">
                Create an account
            </a>
        </p>
    </div>

    <form method="POST" action="{{ route('google.login') }}" id="google-login-form" class="hidden">
        @csrf
        <input type="hidden" name="email" id="google-login-email" />
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const quickLoginButton = document.getElementById('quick-login');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const quickLoginMenu = document.getElementById('quick-login-menu');
            const quickLoginOptions = document.querySelectorAll('.quick-login-option');
            const googleLoginForm = document.getElementById('google-login-form');
            const googleLoginEmail = document.getElementById('google-login-email');

            if (!quickLoginButton || !emailInput || !passwordInput || !quickLoginMenu || !googleLoginForm || !googleLoginEmail) {
                return;
            }

            quickLoginButton.addEventListener('click', () => {
                quickLoginMenu.classList.toggle('hidden');
                if (!quickLoginMenu.classList.contains('hidden')) {
                    quickLoginMenu.classList.add('animate-fade-up');
                }
            });

            quickLoginOptions.forEach((option) => {
                option.addEventListener('click', () => {
                    const action = option.dataset.quickAction;
                    const email = option.dataset.quickEmail;
                    const password = option.dataset.quickPassword;

                    if (action === 'google') {
                        const googleEmail = window.prompt('Enter your Google email');

                        if (googleEmail) {
                            googleLoginEmail.value = googleEmail;
                            googleLoginForm.submit();
                        }

                        quickLoginMenu.classList.add('hidden');
                        return;
                    }

                    if (!email || !password) {
                        return;
                    }

                    emailInput.value = email;
                    passwordInput.value = password;
                    emailInput.form.submit();
                });
            });
        });
    </script>
</x-guest-layout>
