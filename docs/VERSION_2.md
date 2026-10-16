# BiliSkwela Version 2

This alternative interface uses a deep navy sidebar, warm cream background, and blue actions and accents, and compact inventory tables. The dashboard starts with balances, low-stock items, and recent ledger entries. Promotional banners and decorative symbols are removed. Navigation uses consistent line icons, and screen descriptions use direct shop terminology.

The Laravel, Fortify, Spatie, inventory validation, and reporting behavior are unchanged. No code comments have been added. The source is a separate copy of the integrated Phase 6 version 1 checkpoint.

Verification on 16 October 2026: production Vite build passed; 24 automated tests passed with 220 assertions using an isolated SQLite database. The dashboard, products, and reports screens are checked in Microsoft Edge. Automated tests do not establish production database concurrency or SMTP delivery.

For a fresh installation, follow README.md and generate your own application key. The source archive excludes local credentials, the desktop demonstration database, dependencies, and generated build files.

Local comparison: Version 1 uses http://127.0.0.1:8006 and Version 2 uses http://127.0.0.1:8007. These addresses work while the local servers are running. Version 2 has its own copy of the desktop demonstration database, so entries recorded in one version do not automatically appear in the other.

## Color palette

Source: https://colorhunt.co/palette/0107360d1c4222396ffcf1d0

- Deep navy: `#010736` - sidebar and authentication panel.
- Midnight blue: `#0D1C42` - headings and primary text.
- Blue: `#22396F` - buttons, links, focus indicators, and accents.
- Cream: `#FCF1D0` - page background, logo, and navigation highlights.

Supporting neutral shades keep tables and form controls readable. Errors retain explicit labels and red semantic styling.
