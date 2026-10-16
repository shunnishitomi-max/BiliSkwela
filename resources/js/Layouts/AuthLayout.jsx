import { Head, Link } from "@inertiajs/react";

export default function AuthLayout({ title, children }) {
    return (
        <div className="auth-shell">
            <Head title={title} />
            <section className="auth-art">
                <span className="brand light">
                    <span className="brand-icon">bs.</span>
                    <span>
                        BiliSkwela<small>School supplies</small>
                    </span>
                </span>
                <div className="auth-shop-sign">
                    <span className="shop-sign-label">
                        SCHOOL SUPPLIES STORE
                    </span>
                    <h1>
                        Inventory
                        <br />
                        and stock records.
                    </h1>
                    <p>
                        Manage products, record deliveries, and track supplies
                        issued from the shop.
                    </p>
                    <div className="shop-lines">
                        <span>NOTEBOOKS</span>
                        <span>WRITING TOOLS</span>
                        <span>PAPER & MORE</span>
                    </div>
                </div>
                <small>Shop inventory & stock records</small>
            </section>
            <section className="auth-form">
                <div className="auth-card">
                    <Link href="/login" className="auth-logo">
                        BiliSkwela <span>/ Staff access</span>
                    </Link>
                    <h2>{title}</h2>
                    {children}
                    <p className="auth-footnote">For authorized shop staff.</p>
                </div>
            </section>
        </div>
    );
}
