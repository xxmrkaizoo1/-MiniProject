<x-app-layout>
    <x-slot name="header">
        <div class="animate-fade-in relative overflow-hidden rounded-2xl border border-indigo-200/60 bg-white/80 px-5 py-4 shadow-sm backdrop-blur dark:border-indigo-700/50 dark:bg-gray-800/70">
            <div class="pointer-events-none absolute -right-8 -top-10 h-24 w-24 animate-soft-bob rounded-full bg-indigo-400/20 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-10 left-10 h-24 w-24 animate-float-delayed rounded-full bg-sky-400/20 blur-2xl"></div>

            <div class="relative flex items-center justify-between gap-4 animate-fade-up">
                <div>
                    <p class="animate-slide-shimmer inline-flex items-center rounded-full border border-indigo-500/20 bg-indigo-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-indigo-500 dark:text-indigo-300">
                        Account Settings
                    </p>
                    <h2 class="mt-2 text-2xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                        {{ __('Profile') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage your personal details, security, and account controls.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="animate-fade-up rounded-2xl border border-gray-200/70 bg-white/90 p-5 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-800/90 sm:p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="animate-fade-up-delay-1 rounded-2xl border border-gray-200/70 bg-white/90 p-5 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-800/90 sm:p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="animate-fade-up-delay-2 rounded-2xl border border-rose-200/70 bg-white/90 p-5 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-rose-700/50 dark:bg-gray-800/90 sm:p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
