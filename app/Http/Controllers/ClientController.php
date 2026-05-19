<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(private ClientService $service) {}

    public function index(Request $request)
    {
        $clients = $this->service->list(
            $request->user(),
            $request->input('search'),
        );

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(ClientRequest $request)
    {
        $this->service->create($request->user(), $request->validated());

        return redirect()->route('clients.index')
            ->with('success', 'Клиентът е добавен успешно.');
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);

        $client->load([
            'jobs' => fn ($q) => $q->latest()->limit(10),
            'communications' => fn ($q) => $q->with(['user', 'job'])->latest()->limit(10),
        ]);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);

        return view('clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);

        $this->service->update($client, $request->validated());

        return redirect()->route('clients.show', $client)
            ->with('success', 'Клиентът е обновен успешно.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        $this->service->delete($client);

        return redirect()->route('clients.index')
            ->with('success', 'Клиентът е изтрит.');
    }
}
