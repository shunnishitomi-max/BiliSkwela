# MeronPa to BiliSkwela conversion

The uploaded archive has a README labeled "Phase 2", but its actual code also contains report pages. Source inspection, not the README boundary alone, determines this conversion.

| MeronPa component | Laravel/React replacement |
| --- | --- |
| `public/index.php` query-string dispatcher | Named modular resource routes and Laravel controllers |
| PDO repositories and `schema.sql` | Eloquent models, relationships and migrations |
| Category/product service validation | Explicit `$request->validate()` and React field errors |
| Bootstrap PHP templates | Shared React layout, Catalog forms, tables and pages |
| `InventoryService::transact()` lock/transaction | `InventoryService::record()` with DB::transaction and lockForUpdate |
| `inventory_transactions` | `stock_movements`, adding authenticated user_id and updated_at |
| Supplier free-text on products | Related suppliers table with supplier_id |
| Dashboard and stock status filters | Database-backed Inertia Dashboard/Catalog pages |
| Printable report templates | React Reports page with print CSS and date-filtered activity |
| Manual session CSRF helper | Laravel web middleware and Inertia/Axios same-origin session requests |
| No identity/RBAC tables | Fortify users/password recovery and Spatie roles/permissions |

School-supply demo records replace the archive's grocery demo catalog. No original data is overwritten or imported automatically. For a real database transfer, build a reviewed importer that maps supplier strings, preserves balances and opening movements, and validates duplicate names/keys. That data transfer is not included in this source package.

The original archive establishes a compatible inventory-system foundation. It does not establish whether the instructor classifies it as lecture or laboratory work; verify that classification if needed.
