<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-white">
    <div class="relative isolate min-h-screen overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute -top-40 right-0 h-80 w-80 rounded-full bg-indigo-500/30 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-96 w-96 rounded-full bg-sky-500/20 blur-3xl"></div>
        </div>

        <div class="mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-6 py-16 lg:px-10">
            <div class="grid gap-10 lg:grid-cols-[1.3fr_1fr] lg:items-center">
                <div class="space-y-7">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white/80">
                        Student Feedback Portal
                    </span>
                    <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">
                        Student Voice Hub
                    </h1>
                    <p class="text-lg text-white/70">
                        Ruang rasmi pelajar untuk menghantar maklum balas kelas, semak emosi selepas sesi, dan bantu
                        pensyarah bertindak pantas.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        @auth
                            @if (Auth::user()->isLecturer())
                                <a href="{{ url('/dashboard') }}"
                                   class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100">
                                    Dashboard
                                </a>
                            @elseif (Auth::user()->isStudent())
                                <a href="{{ url('/feedback') }}"
                                   class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100">
                                    Submit Feedback
                                </a>
                            @elseif (Auth::user()->isAdmin())
                                <a href="{{ url('/admin/feedback') }}"
                                   class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100">
                                    Admin Panel
                                </a>
                            @endif
                            <a href="{{ route('profile.edit') }}"
                               class="inline-flex items-center justify-center rounded-xl border border-white/30 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                   class="inline-flex items-center justify-center rounded-xl border border-rose-400/60 px-5 py-3 text-sm font-semibold text-rose-200 transition hover:bg-rose-500/20">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100">
                                Login
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="inline-flex items-center justify-center rounded-xl border border-white/30 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                    <div class="grid gap-3 pt-2 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-white/60">Akses Pelajar</p>
                            <p class="mt-2 text-sm text-white/80">
                                Hantar rating, mood, dan komen terus selepas kelas untuk tindakan segera.
                            </p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-white/60">Privasi</p>
                            <p class="mt-2 text-sm text-white/80">
                                Pilih mod anonim supaya maklum balas lebih terbuka dan selesa.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur">
                    <div class="space-y-6">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-white/60">Ringkasan Cepat</p>
                            <h2 class="mt-2 text-2xl font-semibold text-white">Suara Pelajar, Tindakan Nyata.</h2>
                            <p class="mt-3 text-sm text-white/70">
                                Serlahkan isu utama kelas anda, kongsi mood selepas sesi, dan bantu pensyarah membuat
                                penambahbaikan segera.
                            </p>
                        </div>
                        <div class="grid gap-3 text-sm text-white/80">
                            <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                <span>1 minit untuk hantar maklum balas</span>
                                <span class="text-xs uppercase text-white/60">Fast</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                <span>Mood check-in selepas kelas</span>
                                <span class="text-xs uppercase text-white/60">Mood</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                <span>Analitik khas untuk pensyarah</span>
                                <span class="text-xs uppercase text-white/60">Insight</span>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-xs text-white/70">
                            <p class="text-white/80">Langkah pantas:</p>
                            <ol class="mt-2 list-decimal space-y-1 pl-4">
                                <li>Pilih subjek kelas anda.</li>
                                <li>Isi rating &amp; mood selepas sesi.</li>
                                <li>Tambah komen dan hantar.</li>
                            </ol>
                        </div>
                        <div class="rounded-2xl bg-white/10 px-4 py-3 text-xs text-white/70">
                            Tip: Gunakan butang Profile untuk kemas kini nama &amp; emel anda.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
