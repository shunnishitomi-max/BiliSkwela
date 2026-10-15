import { Head, Link } from "@inertiajs/react";
export default function AuthLayout({ title, children }) {
    return (
        <div className="auth-shell">
            <Head title={title} />
            <section className="auth-art">
                <span className="brand light">
                    <span className="brand-icon">B</span>BiliSkwela
                </span>
                <div>
                    <p className="eyebrow">LESS GUESSING. MORE LEARNING.</p>
                    <h1>
                        May gamit.
                        <br />
                        May galing.
                    </h1>
                    <p>
                        Keep every pencil, page, and possibility accounted for.
                    </p>
                    <div className="supply-shapes">
                        <span>✎</span>
                        <span>▤</span>
                        <span>✦</span>
                    </div>
                </div>
                <small>Lahat ng gamit, ready sa eskwela.</small>
            </section>
            <section className="auth-form">
                <div className="auth-card">
                    <Link href="/login" className="auth-logo">
                        BiliSkwela ✦
                    </Link>
                    <h2>{title}</h2>
                    {children}
                </div>
            </section>
        </div>
    );
}
