<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function create(): View
    {
        return view('tickets.create');
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = new Ticket($request->validated());
        $ticket->status = TicketStatus::Pending;
        $ticket->save();

        return redirect()
            ->route('tickets.create')
            ->with('status', 'A ticketet sikeresen beküldted. Az admin hamarosan foglalkozik vele.');
    }
}
