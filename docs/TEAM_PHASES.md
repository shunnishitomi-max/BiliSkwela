# Six phases / three members

The source archive is a complete reference implementation. The table below divides ownership and review; each phase depends on the integrated foundation and should be submitted through a feature pull request. Partial copies of arbitrary files are not standalone apps.

| Phase | Owner | File areas / deliverable | Branch |
| --- | --- | --- | --- |
| 1 | Member 1 | Composer/npm, migrations/models, middleware, root Blade, app.jsx, Vite, shared React UI, seeders | member1/phase1-foundation |
| 2 | Member 2 | Category/Supplier controllers/routes and corresponding Catalog CRUD | member2/phase2-catalog |
| 3 | Member 3 | Product and Stock controllers/routes, InventoryService, product Catalog and Stock page | member3/phase3-stock |
| 4 | Member 1 | Fortify config/provider/actions; Auth pages; Settings | member1/phase4-fortify |
| 5 | Member 2 | Role permissions seeder, backend middleware, UserRoleController/routes, Users UI and conditional navigation | member2/phase5-rbac |
| 6 | Member 3 | Dashboard, Reports, search/pagination, responsive/print CSS, final integration | member3/phase6-integration |

Phase 1 establishes the React foundation, preventing a rushed frontend conversion in Phase 6. Coordinate edits to shared Catalog.jsx, UI.jsx, AppLayout.jsx, and seeders. Keep route/permission names consistent. Integrate and test RBAC before releasing feature routes publicly.

For three uploads per member, add:

1. Member 1: member1/auth-verification - auth/reset/profile tests and installation/mail documentation.
2. Member 2: member2/permission-verification - role/action tests, direct-request denial and permission matrix.
3. Member 3: member3/release-verification - stock integration checks, demo script and final evidence.

For two uploads per member, include those checks/docs in each member's second feature PR. These count as substantive reviewed submissions. Git pushes, commit counts, and repositories are different concepts.

Use the supplied commit-plan PDF for suggested messages, correcting them to match the actual final diff. Do not create fictitious commits or pretend this package is an existing team-authored history. Keep the role default in registration coordinated with the Spatie seeders.

## Separate code checkpoints

This download is a real cumulative phase checkpoint. PHASE_README.md explains this stage, and CHANGESET.md identifies exact code changes from the prior checkpoint. Use the phase-specific README for the available functions and installation.
