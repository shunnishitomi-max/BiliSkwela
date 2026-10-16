import { useForm } from "@inertiajs/react";
import AppLayout from "../Layouts/AppLayout";
import { Field, Pagination, useCan, Empty, date } from "../components/UI";
export default function Stock({ movements, products, filters = {} }) {
    const can = useCan();
    const f = useForm({
        product_id: "",
        type: can("stock.in") ? "IN" : "OUT",
        quantity: "",
        remarks: "",
    });
    const q = useForm({
        product_id: filters.product_id || "",
        type: filters.type || "",
    });
    const selected = products.find(
        (x) => String(x.id) === String(f.data.product_id),
    );
    return (
        <AppLayout
            title="Stock movements"
            subtitle="Record deliveries and issued supplies. View the stock ledger below."
        >
            {(can("stock.in") || can("stock.out")) && (
                <section className="card">
                    <div className="section-heading">
                        <h2>Record a movement</h2>
                        <span className="badge green">
                            Entries cannot be edited
                        </span>
                    </div>
                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            f.post("/stock", {
                                preserveScroll: true,
                                onSuccess: () => f.reset("quantity", "remarks"),
                            });
                        }}
                    >
                        <div className="form-grid stock-grid">
                            <Field
                                label="Product"
                                name="product_id"
                                error={f.errors.product_id}
                            >
                                <select
                                    required
                                    value={f.data.product_id}
                                    onChange={(e) =>
                                        f.setData("product_id", e.target.value)
                                    }
                                >
                                    <option value="">Choose product</option>
                                    {products.map((x) => (
                                        <option key={x.id} value={x.id}>
                                            {x.name} · {x.quantity} available
                                        </option>
                                    ))}
                                </select>
                            </Field>
                            <Field
                                label="Movement"
                                name="type"
                                error={f.errors.type}
                            >
                                <select
                                    value={f.data.type}
                                    onChange={(e) =>
                                        f.setData("type", e.target.value)
                                    }
                                >
                                    {can("stock.in") && (
                                        <option value="IN">Stock in</option>
                                    )}
                                    {can("stock.out") && (
                                        <option value="OUT">Stock out</option>
                                    )}
                                </select>
                            </Field>
                            <Field
                                label="Quantity"
                                name="quantity"
                                type="number"
                                min="1"
                                step="1"
                                required
                                value={f.data.quantity}
                                onChange={(e) =>
                                    f.setData("quantity", e.target.value)
                                }
                                error={f.errors.quantity}
                            />
                            <Field
                                label="Remarks"
                                name="remarks"
                                maxLength="255"
                                value={f.data.remarks}
                                onChange={(e) =>
                                    f.setData("remarks", e.target.value)
                                }
                                error={f.errors.remarks}
                            />
                        </div>
                        {selected && (
                            <p className="hint">
                                Currently available: {selected.quantity} units.
                            </p>
                        )}
                        <button className="button" disabled={f.processing}>
                            Record movement
                        </button>
                    </form>
                </section>
            )}
            <section className="card">
                <div className="section-heading">
                    <h2>Movement ledger</h2>
                </div>
                <form
                    className="filters"
                    onSubmit={(e) => {
                        e.preventDefault();
                        q.get("/stock", { preserveState: true });
                    }}
                >
                    <Field label="Product" name="filter_product">
                        <select
                            value={q.data.product_id}
                            onChange={(e) =>
                                q.setData("product_id", e.target.value)
                            }
                        >
                            <option value="">All products</option>
                            {products.map((x) => (
                                <option key={x.id} value={x.id}>
                                    {x.name}
                                </option>
                            ))}
                        </select>
                    </Field>
                    <Field label="Type" name="filter_type">
                        <select
                            value={q.data.type}
                            onChange={(e) => q.setData("type", e.target.value)}
                        >
                            <option value="">All movements</option>
                            <option value="IN">Stock in</option>
                            <option value="OUT">Stock out</option>
                        </select>
                    </Field>
                    <button
                        className="button secondary"
                        disabled={q.processing}
                    >
                        Filter
                    </button>
                </form>
                {movements.data.length ? (
                    <div className="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Recorded</th>
                                    <th>Product</th>
                                    <th>Movement</th>
                                    <th>Recorded by</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                {movements.data.map((x) => (
                                    <tr key={x.id}>
                                        <td>{date(x.created_at)}</td>
                                        <td>{x.product.name}</td>
                                        <td>
                                            <span
                                                className={`badge ${x.type === "IN" ? "green" : "warning"}`}
                                            >
                                                {x.type === "IN" ? "+" : "-"}
                                                {x.quantity} {x.type}
                                            </span>
                                        </td>
                                        <td>{x.user.name}</td>
                                        <td>{x.remarks || "—"}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <Empty text="No movements match your filters." />
                )}
                <div className="table-footer">
                    <span>{movements.total} movements</span>
                    <Pagination links={movements.links} />
                </div>
            </section>
        </AppLayout>
    );
}
