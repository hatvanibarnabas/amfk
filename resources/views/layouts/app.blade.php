<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Mini ticketkezelő rendszer')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-6 py-4">
                <a href="{{ route('tickets.create') }}" class="text-lg font-semibold tracking-tight text-slate-900">
                    Mini ticketkezelő
                </a>
                <nav class="flex items-center gap-4 text-sm">
                    <a href="{{ route('tickets.create') }}" class="text-slate-600 hover:text-slate-900">Ticket beküldése</a>
                    @auth
                        <a href="{{ route('admin.tickets.index') }}" class="text-slate-600 hover:text-slate-900">Admin felület</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-slate-600 hover:text-slate-900">Kilépés</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md bg-slate-900 px-3 py-1.5 font-medium text-white hover:bg-slate-700">Admin belépés</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-6 py-12">
            @if (session('status'))
                <div class="mb-8 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </body>
</html>
