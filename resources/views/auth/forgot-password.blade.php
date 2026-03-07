<x-guest-layout>
    <div class="space-y-6 animate-fade-up">
        <div class="space-y-2 text-center">
            <p class="inline-flex items-center rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-amber-300">
                Password Help
            </p>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Forgot your password?</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                No worries. Enter your email address and we’ll send you a secure reset link.
            </p>
        </div>

        <x-auth-session-status class="rounded-lg border border-emerald-400/40 bg-emerald-100/60 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-500/10 dark:text-emerald-300" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-200" />
                <x-text-input id="email"
                              class="mt-1 block w-full rounded-xl border-gray-300/80 bg-white/80 transition duration-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900/80"
                              type="email"
                              name="email"
                              :value="old('email')"
                              required
                              autofocus
                              autocomplete="username"
                              placeholder="you@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between gap-3 pt-1">
                <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200">
                    Back to login
                </a>

                <x-primary-button class="animate-glow-pulse rounded-xl px-5 py-2.5 text-sm font-semibold">
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
