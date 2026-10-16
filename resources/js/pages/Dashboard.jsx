import { Link } from "@inertiajs/react";
import AppLayout from "../Layouts/AppLayout";
import { useCan, StockBadge, Empty, date } from "../components/UI";

export default function Dashboard({ stats, lowStock, recent }) {
    const can = useCan();
    return (
        <AppLayout
            title="Stock overview"
            subtitle="Current inventory and the latest entries in your stock ledger."
            action={
                can("stock.in") && (
                    <Link className="button" href="/stock">
                        Record stock entry <span aria-hidden="true">+</span>
                    </Link>
                )
            }
        >
            <div className="stats">
                {[
                    [
                        "Products",
                        stats.products,
                        `${stats.categories} categories`,
                    ],
                    ["Units on hand", stats.units, "Across all products"],
                    ["Low-stock items", stats.low, "At or below minimum"],
                    ["Suppliers", stats.suppliers, "Registered suppliers"],
                ].map(([label, value, detail], index) => (
                    <div
                        className={`stat ${index === 2 && stats.low ? "stat-alert" : ""}`}
                        key={label}
                    >
                        <p>{label}</p>
                        <strong>
                            {new Intl.NumberFormat("en-PH").format(value)}
                        </strong>
                        <small>{detail}</small>
                    </div>
                ))}
            </div>
            {stats.low > 0 && (
                <div className="restock-note">
                    <span className="note-mark" aria-hidden="true">
                        !
                    </span>
                    <p>
                        <strong>
                            {stats.low}{" "}
                            {stats.low === 1
                                ? "product needs"
                                : "products need"}{" "}
                            restocking.
                        </strong>{" "}
                        Check quantities before placing your next supplier
                        order.
                    </p>
                    <Link href="/products?stock=low">Review items →</Link>
                </div>
            )}
            <div className="dashboard-grid">
                <section className="card restock-card">
                    <div className="section-heading">
                        <div>
                            <h2>Restock list</h2>
                            <p className="hint">
                                Lowest quantities first · showing up to 8 items
                            </p>
                        </div>
                        <Link href="/products?stock=low">View list →</Link>
                    </div>
                    {lowStock.length ? (
                        <div className="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>On hand</th>
                                        <th>Minimum</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {lowStock.map((product) => (
                                        <tr key={product.id}>
                                            <td>
                                                <Link
                                                    className="record-name"
                                                    href={`/products/${product.id}`}
                                                >
                                                    {product.name}
                                                </Link>
                                                <small>
                                                    {product.category.name}
                                                </small>
                                            </td>
                                            <td className="quantity-cell">
                                                {product.quantity}
                                            </td>
                                            <td className="minimum-cell">
                                                {product.minimum_stock}
                                            </td>
                                            <td>
                                                <StockBadge product={product} />
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <Empty text="No products are below their minimum stock level." />
                    )}
                    {can("stock.in") && (
                        <div className="card-bottom">
                            <Link href="/stock">Record a delivery →</Link>
                            <span>Stock levels update after each entry.</span>
                        </div>
                    )}
                </section>
                <section className="card">
                    <div className="section-heading">
                        <div>
                            <h2>Latest stock entries</h2>
                            <p className="hint">Most recent transactions</p>
                        </div>
                        <Link href="/stock">Ledger →</Link>
                    </div>
                    {recent.length ? (
                        <div className="activity">
                            {recent.map((entry) => (
                                <div className="activity-row" key={entry.id}>
                                    <span
                                        className={`movement-icon ${entry.type === "IN" ? "positive" : "negative"}`}
                                    >
                                        {entry.type === "IN" ? "IN" : "OUT"}
                                    </span>
                                    <div>
                                        <strong>{entry.product.name}</strong>
                                        <small>
                                            {entry.user.name} ·{" "}
                                            {date(entry.created_at)}
                                        </small>
                                    </div>
                                    <b
                                        className={
                                            entry.type === "IN"
                                                ? "amount-in"
                                                : "amount-out"
                                        }
                                    >
                                        {entry.type === "IN" ? "+" : "−"}
                                        {entry.quantity}
                                    </b>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <Empty text="No stock entries yet." />
                    )}
                </section>
            </div>
            <div className="overview-links">
                <span>Need a printed copy?</span>
                {can("reports.view") ? (
                    <Link href="/reports">Open inventory reports →</Link>
                ) : (
                    <span>Ask an Admin or Manager for inventory reports.</span>
                )}
            </div>
        </AppLayout>
    );
}
