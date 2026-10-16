import { Head, Link, router, usePage } from "@inertiajs/react";
import { useState } from "react";
import { useCan, Errors } from "../components/UI";

const paths = {
    dashboard: "M3 3h7v7H3z M14 3h7v7h-7z M3 14h7v7H3z M14 14h7v7h-7z",
    products: "M4 7l8-4 8 4v10l-8 4-8-4z M4 7l8 4 8-4 M12 11v10 M8 5l8 4",
    categories: "M3 6h7l2 2h9v12H3z M3 6V4h7l2 2",
    suppliers:
        "M3 7h11v11H3z M14 11h4l3 4v3h-7 M6 18a2 2 0 1 0 4 0 M16 18a2 2 0 1 0 4 0",
    stock: "M4 7h15l-4-4 M20 17H5l4 4 M19 7l-4 4 M5 17l4-4",
    reports: "M5 3h10l4 4v14H5z M14 3v5h5 M9 12h6 M9 16h6",
    users: "M9 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M2 21v-2a7 7 0 0 1 14 0v2 M17 5a4 4 0 0 1 0 7 M19 16a5 5 0 0 1 3 5",
    settings: "M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M4 22v-1a8 8 0 0 1 16 0v1",
};

function NavIcon({ name }) {
    return (
        <svg
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.6"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
        >
            <path d={paths[name]} />
        </svg>
    );
}

export default function AppLayout({ title, subtitle, children, action }) {
    const { auth, flash, errors } = usePage().props;
    const can = useCan();
    const [open, setOpen] = useState(false);
    const url = usePage().url.split("?")[0];
    const nav = [
        ["dashboard", "Overview", null],
        ["products", "Products", "products.view"],
        ["categories", "Categories", "categories.view"],
        ["suppliers", "Suppliers", "suppliers.view"],
        ["stock", "Stock ledger", "stock.view"],
        ["reports", "Reports", "reports.view"],
        ["users", "User access", "users.manage"],
        ["settings", "My account", null],
    ];
    return (
        <div className="app">
            <Head title={title} />
            <aside
                id="workspace-navigation"
                className={`sidebar ${open ? "open" : ""}`}
            >
                <Link href="/dashboard" className="brand">
                    <span className="brand-icon">bs.</span>
                    <span>
                        BiliSkwela<small>School supplies</small>
                    </span>
                </Link>
                <div className="nav-label">SHOP RECORDS</div>
                <nav aria-label="Main navigation">
                    {nav
                        .filter(
                            ([, , permission]) =>
                                !permission || can(permission),
                        )
                        .map(([name, label]) => {
                            const href = `/${name}`;
                            const current =
                                url === href || url.startsWith(href + "/");
                            return (
                                <Link
                                    key={name}
                                    href={href}
                                    onClick={() => setOpen(false)}
                                    className={`nav-link ${current ? "current" : ""}`}
                                    aria-current={current ? "page" : undefined}
                                >
                                    <NavIcon name={name} />
                                    {label}
                                </Link>
                            );
                        })}
                </nav>
                <div className="sidebar-bottom">
                    <div className="account-label">SIGNED IN AS</div>
                    <p>
                        {auth.user.name}
                        <small>{auth.user.roles.join(", ") || "No role"}</small>
                    </p>
                    <button
                        className="signout"
                        onClick={() => router.post("/logout")}
                    >
                        Sign out <span aria-hidden="true">↗</span>
                    </button>
                </div>
            </aside>
            {open && (
                <button
                    className="scrim"
                    aria-label="Close navigation"
                    onClick={() => setOpen(false)}
                />
            )}
            <main className="main">
                <div className="topbar">
                    <button
                        className="menu-button"
                        aria-label="Open navigation"
                        aria-expanded={open}
                        aria-controls="workspace-navigation"
                        onClick={() => setOpen(!open)}
                    >
                        ☰
                    </button>
                    <span className="breadcrumb">
                        BiliSkwela <span>/</span> Inventory
                    </span>
                    <span className="top-date">
                        {new Intl.DateTimeFormat("en-PH", {
                            month: "short",
                            day: "numeric",
                            year: "numeric",
                        }).format(new Date())}
                    </span>
                </div>
                <header className="page-heading">
                    <div>
                        <h1>{title}</h1>
                        {subtitle && <p className="subtitle">{subtitle}</p>}
                    </div>
                    {action}
                </header>
                {flash?.success && (
                    <div className="notice success" role="status">
                        {flash.success}
                    </div>
                )}
                <Errors errors={errors} />
                {children}
                <footer className="main-footer">
                    <span>BiliSkwela · School supplies inventory</span>
                    <span>Version 2</span>
                </footer>
            </main>
        </div>
    );
}
