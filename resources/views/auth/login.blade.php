@extends('layouts.app')

@section('title', 'Admin belépés')

@section('content')
    <div class="mx-auto max-w-md">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Admin belépés</h1>
        <p class="mt-3 text-slate-600">Az admin felület kizárólag bejelentkezett admin felhasználók számára érhető el.</p>

        <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            @csrf

            <div>
                <label for="email" class="text-sm font-medium text-slate-700">E-mail-cím</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="text-sm font-medium text-slate-700">Jelszó</label>
                <input id="password" name="password" type="password" required
                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                Emlékezzen rám
            </label>

            <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2.5 font-medium text-white hover:bg-slate-700">
                Belépés
            </button>
        </form>
    </div>
@endsection
