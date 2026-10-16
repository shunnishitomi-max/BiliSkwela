# Phase 6 version 1 verification

Integration baseline: Phase 5 on main, f4b33a096ab77044464964501dac9d7781030247.

The integration preserves the existing Fortify account management and role permissions. Phase 6 adds dashboard summaries, date-filtered stock reports, demo data updates, a demonstration script, and automated authentication, authorization, catalog, and stock tests.

Checks on 16 October 2026 use PHP 8.4.26 and the locked dependencies. The production Vite build and Pint verification for the changed PHP files passed. The PHPUnit suite uses an isolated in-memory SQLite database and a temporary test application key; 24 tests passed with 220 assertions. Coverage includes authentication, role restrictions, catalog validation, stock ledger rollback, dashboard data, report date boundaries, and repeatable demo seeding.

The repository-wide Pint scan reports pre-existing formatting issues in earlier-phase files. Those files are outside this contribution; the Phase 6 PHP files pass their scoped formatting check.

## Manual CSRF check

Start the local application with its own test database. Send a POST request to `/login` without a CSRF token, for example `curl.exe -i -X POST http://127.0.0.1:8000/login`. Confirm HTTP 419, then verify ordinary browser login succeeds with the form-generated token. This real-HTTP check is separate from the automated feature tests and has not been repeated for this integration.

SQLite tests do not establish production MySQL/MariaDB concurrency behavior. Production SMTP, HTTPS hosting, backups, and a fresh browser demonstration require environment-specific setup and verification.
