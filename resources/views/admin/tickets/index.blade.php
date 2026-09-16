@php
    use App\Enums\TicketStatus;
@endphp

@extends('layouts.app')

@section('title', 'Admin felület')

@section('content')
    <div class="max-w-4xl">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Admin felület</h1>
        <p class="mt-3 text-slate-600">Az összes beküldött ticket listája. A státusz módosítható: pending, in progress, done.</p>
    </div>

    @if ($tickets->isEmpty())
        <p class="mt-10 rounded-xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-slate-500">
            Még nincs beküldött ticket.
        </p>
    @else
        <div class="mt-10 space-y-6">
            @foreach ($tickets as $ticket)
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <h2 class="text-lg font-semibold text-slate-900">{{ $ticket->title }}</h2>
                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-semibold',
                            'bg-emerald-50 text-emerald-700' => $ticket->status === TicketStatus::Pending,
                            'bg-amber-50 text-amber-700' => $ticket->status === TicketStatus::InProgress,
                            'bg-slate-100 text-slate-700' => $ticket->status === TicketStatus::Done,
                        ])>
                            {{ $ticket->status->label() }}
                        </span>
                    </div>

                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex gap-4 border-b border-slate-100 pb-3">
                            <dt class="w-8 shrink-0 font-semibold text-sky-700">01</dt>
                            <dd>
                                <span class="block text-xs uppercase tracking-wide text-slate-400">Beküldő neve</span>
                                <span class="text-slate-800">{{ $ticket->name }}</span>
                            </dd>
                        </div>
                        <div class="flex gap-4 border-b border-slate-100 pb-3">
                            <dt class="w-8 shrink-0 font-semibold text-sky-700">02</dt>
                            <dd>
                                <span class="block text-xs uppercase tracking-wide text-slate-400">E-mail-címe</span>
                                <a href="mailto:{{ $ticket->email }}" class="text-slate-800 hover:underline">{{ $ticket->email }}</a>
                            </dd>
                        </div>
                        <div class="flex gap-4 border-b border-slate-100 pb-3">
                            <dt class="w-8 shrink-0 font-semibold text-sky-700">03</dt>
                            <dd>
                                <span class="block text-xs uppercase tracking-wide text-slate-400">Leírás</span>
                                <span class="whitespace-pre-wrap text-slate-800">{{ $ticket->description }}</span>
                            </dd>
                        </div>
                        <div class="flex gap-4 border-b border-slate-100 pb-3">
                            <dt class="w-8 shrink-0 font-semibold text-sky-700">04</dt>
                            <dd>
                                <span class="block text-xs uppercase tracking-wide text-slate-400">Státusz</span>
                                <span class="text-slate-800">{{ $ticket->status->label() }}</span>
                            </dd>
                        </div>
                        <div class="flex gap-4">
                            <dt class="w-8 shrink-0 font-semibold text-sky-700">05</dt>
                            <dd>
                                <span class="block text-xs uppercase tracking-wide text-slate-400">Létrehozás időpontja</span>
                                <time datetime="{{ $ticket->created_at->toIso8601String() }}" class="text-slate-800">
                                    {{ $ticket->created_at->timezone(config('app.timezone'))->format('Y. m. d. H:i') }}
                                </time>
                            </dd>
                        </div>
                    </dl>

                    <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}" class="mt-6 flex flex-wrap items-end gap-3">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="status-{{ $ticket->id }}" class="text-xs font-medium uppercase tracking-wide text-slate-400">Státusz módosítása</label>
                            <select id="status-{{ $ticket->id }}" name="status"
                                class="mt-1 rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200">
                                @foreach (TicketStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($ticket->status === $status)>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                            Mentés
                        </button>
                    </form>
                </article>
            @endforeach
        </div>
    @endif
@endsection
