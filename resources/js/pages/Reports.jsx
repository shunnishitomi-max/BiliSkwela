import { useState } from "react";
import { useForm, usePage } from "@inertiajs/react";
import AppLayout from "../Layouts/AppLayout";
import {
    Field,
    Pagination,
    StockBadge,
    Empty,
    money,
    date,
} from "../components/UI";
export default function Reports({
    totals,
    products,
    movements,
    filters = {},
    generatedAt,
}) {
    const [kind, setKind] = useState("summary");
    const f = useForm({ from: filters.from || "", to: filters.to || "" });
    const user = usePage().props.auth.user;
    const list =
        kind === "low"
            ? products.filter((x) => x.quantity <= x.minimum_stock)
            : products;
    return (
        <AppLayout
            title="Reports"
            subtitle="Inventory balances and stock transactions for your records."
            action={
                <button
                    className="button no-print"
                    onClick={() => window.print()}
                >
                    Print / Save PDF
                </button>
            }
        >
            <section className="card no-print">
                <div className="filters">
                    <Field label="Report" name="report">
                        <select
                            value={kind}
                            onChange={(e) => setKind(e.target.value)}
                        >
                            <option value="summary">Inventory summary</option>
                            <option value="products">Product inventory</option>
                            <option value="low">Low-stock items</option>
                            <option value="movements">
                                Transaction history
                            </option>
                        </select>
                    </Field>
                </div>
                <form
                    className="filters"
                    onSubmit={(e) => {
                        e.preventDefault();
                        f.get("/reports", { preserveState: true });
                    }}
                >
                    <Field
                        label="Movement date from"
                        name="from"
                        type="date"
                        value={f.data.from}
                        onChange={(e) => f.setData("from", e.target.value)}
                        error={f.errors.from}
                    />
                    <Field
                        label="Movement date to"
                        name="to"
                        type="date"
                        value={f.data.to}
                        onChange={(e) => f.setData("to", e.target.value)}
                        error={f.errors.to}
                    />
                    <button
                        className="button secondary"
                        disabled={f.processing}
                    >
                        Apply dates
                    </button>
                </form>
                <p className="hint">
                    Dates filter stock activity. On-hand inventory always shows
                    current balances.
                </p>
            </section>
            <section className="card report">
                <div className="report-heading">
                    <p className="eyebrow">BILISKWELA / SCHOOL SUPPLIES</p>
                    <h2>
                        {
                            {
                                summary: "Inventory summary",
                                products: "Product inventory",
                                low: "Low-stock items",
                                movements: "Transaction history",
                            }[kind]
                        }
                    </h2>
                    <p>
                        Generated {date(generatedAt)} · Prepared by {user.name}
                    </p>
                    {kind === "movements" && (
                        <p>
                            Movement range: {filters.from || "All dates"} to{" "}
                            {filters.to || "today"} · Page{" "}
                            {movements.current_page} of {movements.last_page}
                        </p>
                    )}
                </div>
                {kind === "summary" ? (
                    <div className="stats">
                        {[
                            ["Current units", totals.units],
                            ["Stock in (selected dates)", totals.in],
                            ["Stock out (selected dates)", totals.out],
                            [
                                "Low-stock products",
                                products.filter(
                                    (x) => x.quantity <= x.minimum_stock,
                                ).length,
                            ],
                        ].map(([label, n]) => (
                            <div className="stat" key={label}>
                                <p>{label}</p>
                                <strong>{n}</strong>
                            </div>
                        ))}
                    </div>
                ) : kind === "movements" ? (
                    <>
                        <div className="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Recorded by</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {movements.data.map((x) => (
                                        <tr key={x.id}>
                                            <td>{date(x.created_at)}</td>
                                            <td>{x.product.name}</td>
                                            <td>{x.type}</td>
                                            <td>{x.quantity}</td>
                                            <td>{x.user.name}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        {!movements.data.length && <Empty />}
                        <div className="no-print">
                            <Pagination links={movements.links} />
                        </div>
                        <p className="hint">
                            This report prints the displayed page of{" "}
                            {movements.total} matching movements.
                        </p>
                    </>
                ) : (
                    <>
                        <div className="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product / SKU</th>
                                        <th>Category</th>
                                        <th>Supplier</th>
                                        <th>Price</th>
                                        <th>On hand / min</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {list.map((x) => (
                                        <tr key={x.id}>
                                            <td>
                                                {x.name}
                                                <small>{x.sku}</small>
                                            </td>
                                            <td>{x.category.name}</td>
                                            <td>{x.supplier.name}</td>
                                            <td>{money(x.price)}</td>
                                            <td>
                                                {x.quantity} / {x.minimum_stock}
                                            </td>
                                            <td>
                                                <StockBadge product={x} />
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        {!list.length && <Empty />}
                    </>
                )}
            </section>
        </AppLayout>
    );
}
