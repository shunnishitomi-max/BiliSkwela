import { Head, Link, router, usePage } from "@inertiajs/react";
import { useState } from "react";
import { useCan, Errors } from "../components/UI";
export default function AppLayout({ title, subtitle, children, action }) {
    const { auth, flash, errors } = usePage().props;
    const can = useCan();
    const [open, setOpen] = useState(false);
    const nav = [
        ["/dashboard", "Overview", null, "◈"],
        ["/categories", "Categories", "categories.view", "◫"],
        ["/suppliers", "Suppliers", "suppliers.view", "◎"],
    ];
    const url = usePage().url.split("?")[0];
    return (
        <div className="app">
            <Head title={title} />
            <aside
                id="workspace-navigation"
                className={`sidebar ${open ? "open" : ""}`}
            >
                <Link href="/dashboard" className="brand">
                    <span className="brand-icon">B</span>
                    <span>
                        BiliSkwela<small>SCHOOL SUPPLIES</small>
                    </span>
                </Link>
                <div className="nav-label">YOUR WORKSPACE</div>
                <nav>
                    {nav
                        .filter((x) => !x[2] || can(x[2]))
                        .map(([href, label, _, icon]) => (
                            <Link
                                key={href}
                                href={href}
                                onClick={() => setOpen(false)}
                                className={
                                    url === href ||
                                    (href !== "/dashboard" &&
                                        url.startsWith(href + "/"))
                                        ? "nav-link current"
                                        : "nav-link"
                                }
                            >
                                <span>{icon}</span>
                                {label}
                            </Link>
                        ))}
                </nav>
                <div className="sidebar-bottom">
                    <span className="badge green">
                        {auth.user.roles.join(", ") || "No role"}
                    </span>
                    <p>
                        {auth.user.name}
                        <small>{auth.user.email}</small>
                    </p>
                    <button
                        className="button secondary full"
                        onClick={() => router.post("/logout")}
                    >
                        Sign out
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
                    <span>Inventory workspace</span>
                    <span className="top-pill">Ready sa eskwela ✦</span>
                </div>
                <header className="page-heading">
                    <div>
                        <p className="eyebrow">BILISKWELA / WORKSPACE</p>
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
                    BiliSkwela · Lahat ng gamit, ready sa eskwela.
                </footer>
            </main>
        </div>
    );
}
