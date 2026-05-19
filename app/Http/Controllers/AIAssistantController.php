<?php

namespace App\Http\Controllers;

use App\Http\Requests\AIQueryRequest;
use App\Models\AiQuery;
use App\Services\AI\AIServiceInterface;
use Illuminate\Http\Request;

class AIAssistantController extends Controller
{
    public function __construct(private AIServiceInterface $ai) {}

    public function index(Request $request)
    {
        $clients = $request->user()->clients()->orderBy('name')->get();
        $history = $request->user()->aiQueries()->with('client')->latest()->limit(10)->get();

        return view('ai.index', compact('clients', 'history'));
    }

    public function query(AIQueryRequest $request)
    {
        $data = $request->validated();

        $result = $this->ai->analyzeRequest($data['prompt']);

        $query = AiQuery::create([
            'user_id' => $request->user()->id,
            'client_id' => $data['client_id'] ?? null,
            'prompt' => $data['prompt'],
            'response' => $result,
        ]);

        return view('ai.result', compact('query', 'result'));
    }
}
