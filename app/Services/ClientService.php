<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClientService
{
    public function list(User $user, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $user->clients()
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(User $user, array $data): Client
    {
        return $user->clients()->create($data);
    }

    public function update(Client $client, array $data): Client
    {
        $client->update($data);

        return $client->fresh();
    }

    public function delete(Client $client): void
    {
        $client->delete();
    }

    public function recent(User $user, int $limit = 5)
    {
        return $user->clients()->latest()->limit($limit)->get();
    }
}
