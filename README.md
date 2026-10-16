# BiliSkwela

BiliSkwela is a school supplies inventory system for managing products, categories, suppliers, stock movements, user access, and inventory reports.

## Team Members

- Shun Aei Dg. Nishitomi
- Mariz Angiella Liu
- Alberto Carlos Castaneda

## Features

- Product catalog with search, category and stock filters, product details, and Philippine peso prices
- Category and supplier records linked to products
- Stock-in and stock-out records with date, quantity, remarks, and the signed-in user
- Dashboard with current inventory, low-stock items, and recent stock activity
- Inventory summary, product inventory, low-stock, and transaction-history reports, with print or Save as PDF
- Sign-in, registration, password recovery, and profile and password settings
- Role-based access enforced by the server

Laravel handles data, validation, and protected routes. Fortify provides account authentication, Spatie Laravel Permission assigns roles and permissions, and Inertia connects the Laravel pages to the React interface.

## User access

| Role | Access |
| --- | --- |
| Admin | Manage products, categories, suppliers, stock, reports, and user roles |
| Manager | Manage products, categories, suppliers, stock, and reports |
| Staff | View products, categories, suppliers, dashboard, and stock history; record stock-out |

New registrations receive the Staff role. Only an Admin can change a user's role. The system prevents removal of the last Admin account.

## Inventory rules

- New products start with zero stock. Record opening quantities as stock-in movements.
- Product details do not directly change the stock quantity.
- Stock movements are recorded in the inventory history with the acting user.
- Stock-out cannot exceed the available quantity. Quantities must be positive whole numbers.
- Products with stock or movement history cannot be deleted. Categories and suppliers in use by products cannot be deleted.
- Correct a posted stock movement by recording a new movement with an explanation in the remarks.

## Local setup

### Requirements

- PHP 8.3 or later with curl, fileinfo, mbstring, openssl, PDO MySQL or SQLite, and zip
- Composer 2
- Node.js 22.12 or later and npm
- MySQL 8 or compatible MariaDB; SQLite can be used for a local demonstration

### Install and run

Open the folder containing `artisan`, `composer.json`, and `package.json` in VS Code. Install the application dependencies and create the local environment file:

```powershell
composer install
npm ci
Copy-Item .env.example .env
php artisan key:generate
```

Create an empty database named `biliskwela`, then set `DB_USERNAME` and `DB_PASSWORD` in `.env`. Keep `.env` local and do not share it. Initialize the database, add sample data, build the interface, and start the server:

```powershell
php artisan migrate --seed
php artisan db:seed --class=DemoSeeder
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000) in a browser. After the asset build, the PHP server is enough to run the system. While changing the interface, run `npm run dev` in a second terminal.

For an SQLite demonstration, set `DB_CONNECTION=sqlite` in `.env`, remove `DB_DATABASE` or set it to the full path of a dedicated SQLite file, and create that file before running migrations:

```powershell
New-Item -ItemType File database/database.sqlite
php artisan config:clear
php artisan migrate --seed
php artisan db:seed --class=DemoSeeder
```

SQLite is suitable for a local walkthrough. Use MySQL or MariaDB when checking concurrent stock updates.

## Demo accounts

`DemoSeeder` adds these accounts and sample inventory in local or testing environments only:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@biliskwela.test` | `DemoPass123!` |
| Manager | `manager@biliskwela.test` | `DemoPass123!` |
| Staff | `staff@biliskwela.test` | `DemoPass123!` |

These shared credentials are for local demonstration only. Do not use them for a deployed system. The sample records are illustrative; product prices are in Philippine pesos and supplier contact details are fictional. The repository is prepared for a local classroom demonstration and is not a deployed service.

## Using the system

1. Sign in with an account that has the required role.
2. Add categories and suppliers before creating products that use them.
3. In Products, add or update a product's SKU, name, category, supplier, price, and minimum stock. Use the search and filters to find records.
4. In Stock, choose a product and record stock-in or stock-out, quantity, and remarks. Review the transaction in the stock history.
5. Use the dashboard to check current balances and low-stock items. Open Reports to select an inventory or transaction report, set an optional movement date range for stock activity, and print or save it as a PDF. Inventory reports always show current on-hand balances.
6. Admins can manage account roles under Users. Each account holder can update their profile and password in Settings.

## Password recovery

The local environment writes reset notifications to the Laravel log. To test password recovery, request a reset link on the sign-in page and open the generated link from `storage/logs/laravel.log`. Email delivery requires a mail service configured in `.env`.
