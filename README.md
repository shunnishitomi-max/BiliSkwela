# BiliSkwela â€” Version 2

School Supplies Inventory Management System for INTECH 3112.

BiliSkwela converts the supplied **MeronPa** PHP/PDO inventory application to Laravel 13 with React, Inertia 2, Fortify, and Spatie Permission. The previous category/product CRUD, stock-in/out, low-stock alerts, dashboard, history, filters, and printable reports remain. Supplier text is normalized into a related table; each stock movement records its authenticated author.

## Requirements

- PHP 8.3+ with curl, fileinfo, mbstring, openssl, PDO MySQL/SQLite, and zip; Composer 2.
- Node 22.12+ (or Node 20.19+) and npm. Node 24 was used for the build.
- MySQL 8+ or a compatible MariaDB for the classroom setup. SQLite is supported for a quick local demo and automated tests.
- Do not run this with XAMPP PHP 8.2. Select a compatible PHP executable in your terminal PATH. `php --version` confirms the active version.

## Install in VS Code (PowerShell)

Extract the source archive. Open the folder containing `artisan`, `composer.json`, and `package.json` in VS Code.

```powershell
composer install
npm ci
Copy-Item .env.example .env
php artisan key:generate
```

Create an empty MySQL database named `biliskwela`, then set your own DB_USERNAME and DB_PASSWORD in `.env`. Use a dedicated database rather than the original MeronPa database. Do not overwrite an existing `.env` or commit it.

```powershell
php artisan migrate --seed
php artisan db:seed --class=DemoSeeder
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Open http://127.0.0.1:8000. With built assets, only the PHP server is needed. During frontend development run `npm run dev` in a second terminal. Do not rerun `fortify:install` on this finished project: its customized scaffolding is already included. `composer install` discovers packages automatically.

### Optional quick SQLite demo

Before migrating, change `.env` to `DB_CONNECTION=sqlite`, remove DB_DATABASE or set it to the absolute path of your dedicated file, and create the file:

```powershell
New-Item -ItemType File database/database.sqlite
php artisan config:clear
php artisan migrate --seed
php artisan db:seed --class=DemoSeeder
```

SQLite validates the local workflow. Use MySQL for verification of concurrent row locking; SQLite does not implement MySQL's `SELECT ... FOR UPDATE` semantics.

## Demo accounts (local/testing only)

For a fuller school-supply shop demonstration, run `php artisan db:seed --class=RealisticDemoSeeder` after installation. This adds 32 products, five fictional suppliers, and three weeks of sample deliveries and stock-out entries. Sample prices are in PHP and are illustrative. Existing products, stock balances, and accounts are preserved; repeat runs skip products already present. Supplier email addresses use reserved `.test` domains. The sample data is restricted to local/testing environments.

| Role | Email | Password |
| --- | --- | --- |
| Admin | admin@biliskwela.test | DemoPass123! |
| Manager | manager@biliskwela.test | DemoPass123! |
| Staff | staff@biliskwela.test | DemoPass123! |

DemoSeeder is separate from the normal DatabaseSeeder and rejects production environments. It does not reset passwords, role changes, or inventory on later runs. These are public demonstration credentials; never use them for deployed users. The normal seeder creates permissions/roles only. Registration requires those roles to exist and always assigns Staff on the server.

## Permissions

- Admin: all catalog and stock actions, reports, and user role administration.
- Manager: all catalog and stock actions and reports; cannot change roles.
- Staff: read categories/products/suppliers/history/dashboard and record stock-out; cannot receive stock, edit/delete catalog, see reports, or assign roles.
- Routes enforce permissions. React conditional buttons are supplementary. The last Admin cannot be demoted.

## Inventory rules

New products start at zero stock. Record opening stock through Stock movements. Quantity cannot be written through product CRUD. Every movement updates the cached balance and inserts its ledger entry inside one DB transaction with a product row lock. Insufficient stock, fractional quantities, overflow, and negative/zero quantities are rejected. Posted movements are immutable; corrections are new IN/OUT entries with explanatory remarks. Record the author from the authenticated session, never from client data.

Referenced categories/suppliers and products with stock/history cannot be deleted. Unused zero-stock records support delete for the CRUD demonstration. Category-to-product migration includes the required cascade foreign key, while application checks prevent accidental catalog erasure. Stock/history references use restrictive deletion. There is no archive UI in this version.

## Password recovery and account settings

The local `.env.example` uses `MAIL_MAILER=log`. Request a reset link through Forgot password and inspect `storage/logs/laravel.log` for the generated notification URL. Only inspect your own local test mail. Paste that reset URL into the browser and set a new password. For an actual emailed demonstration, configure your SMTP/mail sandbox credentials in `.env`. Production email needs a real mail service. Settings supports profile updates and password changes with current-password verification.

## Verification

```powershell
php artisan test
npm run build
php vendor/bin/pint --test
php artisan route:list --except-vendor
```

Tests use a separate in-memory SQLite database, not your local MySQL data. Laravel disables CSRF middleware during unit/feature tests; verify a missing-token POST through real HTTP as described in `docs/VERIFICATION.md`. See that file for actual performed checks and limitations. Do not present an unperformed MySQL concurrency or production check as passing.

## Six phases and team contributions

See `docs/TEAM_PHASES.md`. One integrated system, one repository; each member owns two phases. A third verification/documentation pull request per member provides nine contributions total. Commit messages are a prospective guide, not a fabricated history. Teammates should understand, adapt, test, and commit their own real work using their own GitHub accounts. Do not push the same complete source as nine unrelated "systems".

## Instructor demonstration

Invite **Eljinx** as repository collaborator. Record a 3-7 minute video showing Fortify auth, role restrictions, CRUD, stock/report features, and code for Fortify, Spatie, and React/Inertia. Submit a readable YouTube/Google Drive video link before the precise Classroom cutoff. The screenshot displays October 16. Check the official Classroom time.

Important code: `bootstrap/app.php`, `resources/views/app.blade.php`, `resources/js/app.jsx`, `vite.config.js`, `app/Providers/FortifyServiceProvider.php`, `app/Actions/Fortify`, `database/seeders/RolesAndPermissionsSeeder.php`, `routes/*.php`, and `app/Services/InventoryService.php`.

## Production configuration

This is a classroom application requiring deployment configuration, not a preconfigured public service. Configure HTTPS, APP_DEBUG=false, a unique application key, trusted host/proxy settings, secure session cookies, real mail, backups, and least-privilege database credentials. Build assets and run migrations against a dedicated production database. Verify version 1 in MySQL and the browser before submission. There are no payments or public storefront checkout flows.
