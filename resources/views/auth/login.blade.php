<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-secondary-button class="ms-3" type="button" id="quick-login">
                {{ __('Quick login') }}
            </x-secondary-button>

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <div id="quick-login-menu" class="mt-4 hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 text-sm text-gray-700 dark:text-gray-200">
            <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">
                {{ __('Choose an account') }}
            </p>
            <div class="space-y-2">
                <button type="button" class="quick-login-option flex w-full items-center gap-3 rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700/60" data-quick-action="google">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300">G</span>
                    <span>
                        <span class="block font-medium">{{ __('Google Account') }}</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ __('Use an existing Google email') }}</span>
                    </span>
                </button>
                <button type="button" class="quick-login-option flex w-full items-center gap-3 rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700/60" data-quick-action="demo" data-quick-email="demo@gmail.com" data-quick-password="password">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300">D</span>
                    <span>
                        <span class="block font-medium">{{ __('Demo Account') }}</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">demo@gmail.com</span>
                    </span>
                </button>
            </div>
        </div>
    </form>

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
