<x-guest-layout>
    <div class="space-y-6 animate-fade-up">
        <div class="space-y-2 text-center">
            <p class="inline-flex items-center rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-sky-300">
                Join Us
            </p>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create your Student Voice Hub account</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">Start sharing feedback and tracking class experience with better clarity.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Name')" class="text-gray-700 dark:text-gray-200" />
                <x-text-input id="name" class="mt-1 block w-full rounded-xl border-gray-300/80 bg-white/80 transition duration-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900/80" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Your full name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-200" />
                <x-text-input id="email" class="mt-1 block w-full rounded-xl border-gray-300/80 bg-white/80 transition duration-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900/80" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 dark:text-gray-200" />

                <x-text-input id="password" class="mt-1 block w-full rounded-xl border-gray-300/80 bg-white/80 transition duration-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900/80"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="At least 8 characters" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 dark:text-gray-200" />

                <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-xl border-gray-300/80 bg-white/80 transition duration-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900/80"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Repeat password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                <a class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="animate-glow-pulse rounded-xl px-5 py-2.5 text-sm font-semibold">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
