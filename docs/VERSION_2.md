# BiliSkwela Version 2

This alternative interface uses a navy sidebar, off-white background, rust-colored actions, and compact inventory tables. The dashboard starts with balances, low-stock items, and recent ledger entries. Promotional banners and decorative symbols are removed. Navigation uses consistent line icons, and screen descriptions use direct shop terminology.

The Laravel, Fortify, Spatie, inventory validation, and reporting behavior are unchanged. No code comments have been added. The source is a separate copy of the integrated Phase 6 version 1 checkpoint.

Verification on 16 October 2026: production Vite build passed; 24 automated tests passed with 220 assertions using an isolated SQLite database. The dashboard, products, and reports screens are checked in Microsoft Edge. Automated tests do not establish production database concurrency or SMTP delivery.

For a fresh installation, follow README.md and generate your own application key. The source archive excludes local credentials, the desktop demonstration database, dependencies, and generated build files.

Local comparison: Version 1 uses http://127.0.0.1:8006 and Version 2 uses http://127.0.0.1:8007. These addresses work while the local servers are running. Version 2 has its own copy of the desktop demonstration database, so entries recorded in one version do not automatically appear in the other.
