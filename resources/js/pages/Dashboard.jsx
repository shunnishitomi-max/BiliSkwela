import { Link } from "@inertiajs/react";
import AppLayout from "../Layouts/AppLayout";
import { useCan, StockBadge, Empty, date } from "../components/UI";
export default function Dashboard({ stats, lowStock, recent }) {
    const can = useCan();
    return (
        <AppLayout
            title="A good day to get stocked."
            subtitle="Your school essentials, all accounted for."
            action={
                can("stock.in") && (
                    <Link className="button" href="/stock">
                        + Receive stock
                    </Link>
                )
            }
        >
            <section className="hero">
                <div>
                    <span className="badge cream">EVERYDAY ESSENTIALS</span>
                    <h2>
                        Small supplies.
                        <br />
                        Big possibilities.
                    </h2>
                    <p>Keep the shelves ready for the next bright idea.</p>
                </div>
                <div className="hero-art" aria-hidden="true">
                    <span className="notebook">
                        B<span>READY TO LEARN</span>
                    </span>
                    <span className="pencil">✎</span>
                    <span className="spark">✦</span>
                </div>
            </section>
            <div className="stats">
                {[
                    ["Products", stats.products, "Catalog essentials"],
                    ["Units on hand", stats.units, "Available inventory"],
                    ["Needs restocking", stats.low, "At or below minimum"],
                    ["Suppliers", stats.suppliers, "Supply partners"],
                ].map(([label, n, sub]) => (
                    <div className="stat" key={label}>
                        <p>{label}</p>
                        <strong>{n}</strong>
                        <small>{sub}</small>
                    </div>
                ))}
            </div>
            <div className="dashboard-grid">
                <section className="card">
                    <div className="section-heading">
                        <h2>Restock radar</h2>
                        <span className="badge warning">{stats.low} low</span>
                    </div>
                    {lowStock.length ? (
                        <div className="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>On hand</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {lowStock.map((x) => (
                                        <tr key={x.id}>
                                            <td>
                                                <Link
                                                    href={`/products/${x.id}`}
                                                >
                                                    {x.name}
                                                </Link>
                                                <small>{x.category.name}</small>
                                            </td>
                                            <td>
                                                {x.quantity} / min{" "}
                                                {x.minimum_stock}
                                            </td>
                                            <td>
                                                <StockBadge product={x} />
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <Empty text="All essentials are stocked." />
                    )}
                </section>
                <section className="card">
                    <div className="section-heading">
                        <h2>Recent activity</h2>
                        <Link href="/stock">View all →</Link>
                    </div>
                    {recent.length ? (
                        <div className="activity">
                            {recent.map((x) => (
                                <div className="activity-row" key={x.id}>
                                    <span
                                        className={`movement-icon ${x.type === "IN" ? "positive" : "negative"}`}
                                    >
                                        {x.type === "IN" ? "↗" : "↘"}
                                    </span>
                                    <div>
                                        <strong>{x.product.name}</strong>
                                        <small>
                                            {x.user.name} · {date(x.created_at)}
                                        </small>
                                    </div>
                                    <b>
                                        {x.type === "IN" ? "+" : "-"}
                                        {x.quantity}
                                    </b>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <Empty text="Stock activity will appear here." />
                    )}
                </section>
            </div>
        </AppLayout>
    );
}
