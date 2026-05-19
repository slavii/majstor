<?php

namespace App\Providers;

use App\Services\AI\AIServiceInterface;
use App\Services\AI\FakeAIService;
use App\Services\AI\OpenAIService;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AIServiceInterface::class, function () {
            $key = config('services.openai.api_key');

            if ($key && $key !== 'your-openai-api-key') {
                return new OpenAIService;
            }

            return new FakeAIService;
        });
    }

    public function boot(): void
    {
        Carbon::setLocale('bg');
    }
}
