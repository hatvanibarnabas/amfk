<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $tickets = Ticket::query()
            ->latest()
            ->get();

        return view('admin.tickets.index', compact('tickets'));
    }

    public function update(UpdateTicketStatusRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->status = $request->validated('status');
        $ticket->save();

        return redirect()
            ->route('admin.tickets.index')
            ->with('status', 'A ticket státusza frissült.');
    }
}
