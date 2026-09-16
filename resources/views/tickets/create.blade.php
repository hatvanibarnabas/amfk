@extends('layouts.app')

@section('title', 'Ticket beküldése')

@section('content')
    <div class="max-w-2xl">
        <h1 class="text-4xl font-bold tracking-tight text-slate-900">Mini ticketkezelő rendszer</h1>
        <p class="mt-4 inline-block rounded bg-sky-100 px-2.5 py-1 text-sm font-medium text-sky-800">
            Laravel / PHP fejlesztési feladat
        </p>
        <p class="mt-6 max-w-xl text-slate-600">
            Hibajegy beküldése bejelentkezés nélkül. Az admin a beérkezett ticketeket a belső felületen kezeli.
        </p>
    </div>

    <section class="mt-12 max-w-2xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <h2 class="text-xl font-semibold text-slate-900">Ticket beküldése</h2>
        <p class="mt-2 text-sm text-slate-500">Bárki elküldhet új hibajegyet. Az új ticket státusza automatikusan <strong>pending</strong>.</p>

        <form method="POST" action="{{ route('tickets.store') }}" class="mt-8 space-y-6">
            @csrf

            <div class="border-b border-slate-100 pb-5">
                <label for="name" class="flex items-baseline gap-4">
                    <span class="w-8 shrink-0 text-sm font-semibold text-sky-700">01</span>
                    <span class="text-sm font-medium text-slate-700">Név</span>
                </label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-b border-slate-100 pb-5">
                <label for="email" class="flex items-baseline gap-4">
                    <span class="w-8 shrink-0 text-sm font-semibold text-sky-700">02</span>
                    <span class="text-sm font-medium text-slate-700">E-mail-cím</span>
                </label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-b border-slate-100 pb-5">
                <label for="title" class="flex items-baseline gap-4">
                    <span class="w-8 shrink-0 text-sm font-semibold text-sky-700">03</span>
                    <span class="text-sm font-medium text-slate-700">Cím</span>
                </label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required
                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="flex items-baseline gap-4">
                    <span class="w-8 shrink-0 text-sm font-semibold text-sky-700">04</span>
                    <span class="text-sm font-medium text-slate-700">Leírás</span>
                </label>
                <textarea id="description" name="description" rows="5" required
                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm">
                <span class="text-slate-600">Az új ticket alapértelmezett státusza:</span>
                <span class="font-semibold text-emerald-700">pending</span>
            </div>

            <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2.5 font-medium text-white hover:bg-slate-700">
                Ticket beküldése
            </button>
        </form>
    </section>
@endsection
