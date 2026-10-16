# BiliSkwela Version 2

This alternative interface uses Version 1's green-and-white palette with Version 2's compact layout. The dashboard starts with balances, low-stock items, and recent ledger entries. Promotional banners and decorative symbols are removed. Navigation uses consistent line icons, and screen descriptions use direct shop terminology.

The Laravel, Fortify, Spatie, inventory validation, and reporting behavior are unchanged. No code comments have been added. The source is a separate copy of the integrated Phase 6 version 1 checkpoint.

Verification on 16 October 2026: production Vite build passed; 26 automated tests passed with 255 assertions using an isolated SQLite database. Tests cover repeatable sample data, preservation of existing stock, ledger balance consistency, and chronological stock displays. The dashboard, products, and reports screens are checked in Microsoft Edge. Automated tests do not establish production database concurrency or SMTP delivery.

For a fresh installation, follow README.md and generate your own application key. The source archive excludes local credentials, the desktop demonstration database, dependencies, and generated build files.

Local comparison: Version 1 uses http://127.0.0.1:8006 and Version 2 uses http://127.0.0.1:8007. These addresses work while the local servers are running. Version 2 has its own copy of the desktop demonstration database, so entries recorded in one version do not automatically appear in the other.

## Sample shop data

Run `php artisan db:seed --class=RealisticDemoSeeder` to add 32 products and five fictional suppliers to the eight-product starter demonstration. It records opening stock, selected replenishment deliveries, and eight stock-out batches per new product over three weeks. Prices and supplier details are fictional examples for classroom use. Product quantities are calculated through InventoryService and its transaction ledger. Existing data is preserved and repeated runs skip already-present products.

## Color palette

Restored from the original Version 1 stylesheet:

- Primary green: `#147D68` - buttons, links, logo, and active navigation.
- Deep green: `#153E35` - headings and authentication panel.
- Page background: `#F5F7F4`.
- White: `#FFFFFF` - sidebar, cards, and form surfaces.
- Light green: `#EAF4EC` - active and hovered navigation.
- Borders: `#E2E8E2`.

Supporting shades preserve readable text and recognizable success/error/low-stock states. Version 2's layout and wording remain in place.
