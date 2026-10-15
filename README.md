# BiliSkwela

School Supplies Inventory Management System using Laravel, React, Inertia, Fortify, and Spatie.

## Phase 2: Categories and Suppliers

Complete cumulative checkpoint. Includes all earlier phases.

## Install (VS Code / PowerShell)

Requirements: PHP 8.3+, Composer 2, Node 22.12+ and npm, dedicated MySQL/MariaDB database.

```powershell
composer install
npm ci
Copy-Item .env.example .env
php artisan key:generate
```

Create a database named biliskwela and set your own MySQL credentials in .env. Then:

```powershell
php artisan migrate --seed
php artisan db:seed --class=DemoSeeder
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Open http://127.0.0.1:8000 and sign in as admin@biliskwela.test / DemoPass123!.

Phases 1-3 include basic sign-in but not registration/recovery/settings. Before Phase 5, catalog/stock modules are Admin-only; granular permissions arrive in Phase 5. The final dashboard/reports arrive in Phase 6. DemoSeeder is local/testing-only. Use dedicated demo data; these are intermediate classroom checkpoints.

For SQLite: set DB_CONNECTION=sqlite, remove DB_DATABASE, create database/database.sqlite, then run migrations/seeders. To move from an earlier checkpoint, preserve your local .env and database and replace only source files listed in CHANGESET.md. Run composer install, npm ci, php artisan migrate --seed, php artisan db:seed --class=DemoSeeder, and npm run build after applying changes. Do not run migrate:fresh on data you need.

See PHASE_README.md and CHANGESET.md for exact additions, edits, removals and commit guidance. Full automated final tests are included in Phase 6. Earlier phase-specific smoke checks were performed with a dedicated database.
