# Copilot instructions for StockFlowPOS

These concise guidelines help AI coding agents be productive in this Laravel + Inertia (React) codebase.

**Project Overview**
- **Type:** Laravel 11 backend with Inertia + React frontend and Vite asset pipeline.
- **Where to look:** backend controllers/models in `app/Http/Controllers` and `app/Models`; frontend pages and components under `resources/js` and server-rendered templates in `resources/views`.
- **Routing:** HTTP routes live in `routes/web.php` (primary UI) and `routes/api.php` (offline-sync + API endpoints). Installer routes are in `routes/installer.php` and must be preserved before auth routes.

**How to run (dev)**
- **Local (recommended):** follow README quick steps: `composer install`, `npm install`, copy `.env.example` → `.env`, `php artisan key:generate`, `php artisan migrate --seed`, `php artisan storage:link`, then `npm run dev` and `php artisan serve`.
- **Parallel dev:** use `npm run dev:all` (package.json) or `composer run-script native:dev` to start Vite + Laravel together.
- **Docker:** `docker compose up --build` uses `docker-compose.yml` (ports: app 8080, vite 5173, mysql 3306). The compose command in the repo runs migrations and seeds automatically for dev.

**Build & deploy**
- **Frontend production:** `npm run build` (Vite). See `package.json` scripts `build:prod` and `deploy:build`.
- **Composer build scripts:** `composer` scripts include `deploy:setup` and `deploy:production` which call Laravel cache/optimize commands.
- **Dockerfile:** multi-stage production build builds assets then installs PHP deps; reference `Dockerfile` and `Dockerfile.frontend` for environment-specific differences.

**Tests & linting**
- **PHPUnit:** test suites defined in `phpunit.xml` (tests/Unit and tests/Feature). Run `vendor/bin/phpunit` or `./vendor/bin/phpunit`.
- **JS:** no explicit test runner in repo; focus on building Vite assets when modifying frontend code.

**Common conventions & patterns to follow (observed)**
- Controllers are organized per feature (e.g., `SaleController`, `POSController`). Follow existing controller structure and method naming when adding endpoints.
- Many endpoints return Inertia responses—prefer returning Inertia pages from web routes and JSON from `routes/api.php` for sync endpoints.
- Database migrations and seeders can be heavy; prefer running targeted migrations locally when experimenting. Use `php artisan migrate --path=/database/migrations/XXX.php` for single-file runs.
- Storage: templates and uploaded assets expect `storage/app/public` and rely on `php artisan storage:link` to expose them.

**Integration points & external dependencies**
- MPESA: config and speciality endpoints exist (see `config/mpesa.php` and MPESA docs in the repo). Be cautious changing payment / polling code (SaleController endpoints `/api/sales/{sale}/status`).
- Redis, MySQL: local Docker compose wires `mysql` and `redis`; queue connection defaults to `database` in composer scripts.
- Notifications: Telegram channel and activity log packages are used (see `composer.json` require list). Avoid breaking interfaces those packages expect.

**Files to inspect for context before edits**
- Routes: [routes/web.php](routes/web.php)
- Controllers: [app/Http/Controllers](app/Http/Controllers)
- Frontend entry & scripts: [package.json](package.json) and [resources/js](resources/js) (Vite + Inertia)
- Docker/dev orchestration: [docker-compose.yml](docker-compose.yml) and [Dockerfile](Dockerfile)
- Build scripts: [composer.json](composer.json) and [package.json](package.json)

**Small, practical rules for patches**
- When adding routes, register installer/maintenance routes before auth routes, following `routes/web.php` order.
- For frontend pages use Inertia page components under `resources/js` to keep server + client rendering consistent.
- Preserve migration and seeder order; if adding seeds, append safely and test `php artisan migrate --seed` in a disposable DB.
- Prefer using existing helper services under `app/Services` and traits under `app/Traits` instead of duplicating logic.

If anything in this guidance is unclear or you want more detail for a specific area (tests, deployment, MPESA integration, or offline sync), tell me which area and I will expand the file.
