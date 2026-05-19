# Majstor

Business assistant platform for small Bulgarian service businesses — repair technicians, builders, electricians, plumbers, HVAC installers, and handymen.

## Tech Stack

- **Laravel 13** (PHP 8.4)
- **Blade** + **TailwindCSS** + **Alpine.js**
- **MySQL 8.4**
- **Docker** for development & deployment

## Quick Start (Docker)

```bash
# 1. Clone & configure
cp .env.example .env

# 2. Start services
docker compose up -d --build

# 3. Run migrations & seed demo data
docker compose run --rm --profile setup migrate

# 4. Generate app key
docker compose exec app php artisan key:generate

# 5. Open in browser
open http://localhost:8000
```

**Demo credentials:** `demo@majstor.bg` / `password`

## Features

- **Authentication** — register, login, password reset (Laravel Breeze)
- **Dashboard** — today's jobs, upcoming tasks, stats overview
- **Clients** — CRUD, search, contact history
- **Jobs** — full lifecycle tracking (new → scheduled → in progress → completed), photo uploads, comments, status timeline
- **AI Assistant** — describe a work request in plain Bulgarian, get a structured summary, task checklist, materials estimate, and draft quotation

## Project Structure

```
app/
├── Http/Controllers/     # Thin controllers
├── Http/Requests/        # Form validation
├── Models/               # Eloquent models
├── Policies/             # Authorization
├── Services/             # Business logic
│   └── AI/               # AI provider abstraction
└── Providers/
```

## AI Assistant

The AI module uses a clean interface (`AIServiceInterface`). If `OPENAI_API_KEY` is set, it uses OpenAI. Otherwise, a `FakeAIService` returns placeholder data for development.

## License

Proprietary.
