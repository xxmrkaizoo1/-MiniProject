<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-300">
        Enter the 6-digit OTP sent to your email to complete registration.
    </div>

    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register.verify-otp.submit') }}">
        @csrf

        <div>
            <x-input-label for="otp" :value="__('OTP Code')" />
            <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" :value="old('otp')" required autofocus maxlength="6" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-between gap-3">
            <x-primary-button>
                {{ __('Verify OTP') }}
            </x-primary-button>
        </div>
    </form>

    <form method="POST" action="{{ route('register.resend-otp') }}" class="mt-4">
        @csrf
        <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
            Resend OTP
        </button>
    </form>
</x-guest-layout>
