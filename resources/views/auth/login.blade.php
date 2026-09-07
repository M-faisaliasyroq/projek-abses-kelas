<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Absensi RPL SMK</title>
    <script>(function () { const saved = localStorage.getItem('theme'); const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; const dark = saved ? saved === 'dark' : prefersDark; document.documentElement.classList.toggle('dark', dark); })();</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 dark:bg-slate-950 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-indigo-500 items-center justify-center font-bold text-white text-2xl mb-4">AR</div>
            <h1 class="text-2xl font-semibold text-white">Sistem Absensi RPL</h1>
            <p class="text-slate-400 text-sm mt-1">SMK RPL - Absensi per Mata Pelajaran</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl dark:bg-slate-800 p-8">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-slate-100 mb-6">Masuk ke Akun</h2>

            @if($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-500/15 dark:border-red-500/30 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <x-input name="email" label="Email" type="email" required autocomplete="email" placeholder="nama@smkrpl.test"/>
                <x-input name="password" label="Password" type="password" required autocomplete="current-password" placeholder="••••••••"/>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-slate-300">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">SMK RPL &copy; {{ date('Y') }}</p>
    </div>

    <button id="theme-toggle" type="button" class="fixed top-4 right-4 rounded-lg bg-white/10 p-2.5 text-slate-300 hover:bg-white/20 dark:bg-slate-700/50 dark:text-slate-300 dark:hover:bg-slate-700 transition" aria-label="Toggle theme">
        <svg class="h-5 w-5 hidden dark:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
        <svg class="h-5 w-5 block dark:hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.455 2.004a8.259 8.259 0 0111.06 0c.669.67 1.043 1.502 1.043 2.373 0 4.992-3.657 8.697-8.257 8.697a8.29 8.29 0 01-8.257-8.697c0-.872.374-1.704 1.043-2.373z" clip-rule="evenodd"/></svg>
    </button>
    <script>
        document.getElementById('theme-toggle').addEventListener('click', function () {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    </script>

</body>
</html>
