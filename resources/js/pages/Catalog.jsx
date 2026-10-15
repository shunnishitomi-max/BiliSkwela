import { Link, router, useForm } from "@inertiajs/react";
import AppLayout from "../Layouts/AppLayout";
import {
    Field,
    Pagination,
    useCan,
    StockBadge,
    Empty,
    money,
} from "../components/UI";
const definitions = {
    categories: {
        title: "Categories",
        single: "category",
        fields: [
            ["name", "Name", "text"],
            ["description", "Description", "text"],
        ],
    },
    suppliers: {
        title: "Suppliers",
        single: "supplier",
        fields: [
            ["name", "Name", "text"],
            ["email", "Email", "email"],
            ["phone", "Phone", "tel"],
            ["address", "Address", "text"],
        ],
    },
    products: {
        title: "Products",
        single: "product",
        fields: [
            ["sku", "SKU", "text"],
            ["name", "Product name", "text"],
            ["category_id", "Category", "select"],
            ["supplier_id", "Supplier", "select"],
            ["price", "Price (PHP)", "number"],
            ["minimum_stock", "Minimum stock", "number"],
        ],
    },
};
function CatalogForm({
    resource,
    mode,
    record,
    categories = [],
    suppliers = [],
}) {
    const d = definitions[resource];
    const initial = Object.fromEntries(
        d.fields.map(([k]) => [
            k,
            record?.[k] ?? (k === "minimum_stock" ? 5 : ""),
        ]),
    );
    const f = useForm(initial);
    return (
        <section className="card form-card">
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    mode === "edit"
                        ? f.put(`/${resource}/${record.id}`)
                        : f.post(`/${resource}`);
                }}
            >
                <div className="form-grid">
                    {d.fields.map(([k, label, type]) => (
                        <Field
                            key={k}
                            label={label}
                            name={k}
                            error={f.errors[k]}
                            {...(type !== "select"
                                ? {
                                      type,
                                      required: ![
                                          "description",
                                          "email",
                                          "phone",
                                          "address",
                                      ].includes(k),
                                      value: f.data[k],
                                      onChange: (e) =>
                                          f.setData(k, e.target.value),
                                      min: type === "number" ? 0 : undefined,
                                      step:
                                          k === "price"
                                              ? "0.01"
                                              : type === "number"
                                                ? "1"
                                                : undefined,
                                  }
                                : {})}
                        >
                            {type === "select" && (
                                <select
                                    name={k}
                                    required
                                    value={f.data[k]}
                                    onChange={(e) =>
                                        f.setData(k, e.target.value)
                                    }
                                    aria-invalid={!!f.errors[k]}
                                >
                                    <option value="">
                                        Choose {label.toLowerCase()}
                                    </option>
                                    {(k === "category_id"
                                        ? categories
                                        : suppliers
                                    ).map((x) => (
                                        <option key={x.id} value={x.id}>
                                            {x.name}
                                        </option>
                                    ))}
                                </select>
                            )}
                        </Field>
                    ))}
                </div>
                {resource === "products" && (
                    <p className="hint">
                        Stock starts at zero. Record opening stock and later
                        changes through Stock movements.
                    </p>
                )}
                <div className="actions">
                    <button className="button" disabled={f.processing}>
                        {mode === "edit"
                            ? "Save changes"
                            : `Create ${d.single}`}
                    </button>
                    <Link className="button secondary" href={`/${resource}`}>
                        Cancel
                    </Link>
                </div>
            </form>
        </section>
    );
}
function CatalogList({ resource, records, filters = {}, categories = [] }) {
    const d = definitions[resource];
    const can = useCan();
    const f = useForm({
        search: filters.search || "",
        ...(resource === "products"
            ? {
                  category_id: filters.category_id || "",
                  stock: filters.stock || "",
              }
            : {}),
    });
    return (
        <>
            <section className="card">
                <form
                    className="filters"
                    onSubmit={(e) => {
                        e.preventDefault();
                        f.get(`/${resource}`, { preserveState: true });
                    }}
                >
                    <Field
                        label="Search"
                        name="search"
                        placeholder={`Search ${d.title.toLowerCase()}...`}
                        value={f.data.search}
                        onChange={(e) => f.setData("search", e.target.value)}
                        error={f.errors.search}
                    />
                    {resource === "products" && (
                        <>
                            <Field
                                label="Category"
                                name="category_id"
                                error={f.errors.category_id}
                            >
                                <select
                                    value={f.data.category_id}
                                    onChange={(e) =>
                                        f.setData("category_id", e.target.value)
                                    }
                                >
                                    <option value="">All categories</option>
                                    {categories.map((x) => (
                                        <option key={x.id} value={x.id}>
                                            {x.name}
                                        </option>
                                    ))}
                                </select>
                            </Field>
                            <Field
                                label="Stock status"
                                name="stock"
                                error={f.errors.stock}
                            >
                                <select
                                    value={f.data.stock}
                                    onChange={(e) =>
                                        f.setData("stock", e.target.value)
                                    }
                                >
                                    <option value="">All stock</option>
                                    <option value="low">Low stock</option>
                                    <option value="available">Available</option>
                                    <option value="out">Out of stock</option>
                                </select>
                            </Field>
                        </>
                    )}
                    <button
                        className="button secondary"
                        disabled={f.processing}
                    >
                        Filter
                    </button>
                    <Link href={`/${resource}`}>Clear</Link>
                </form>
                {records.data.length ? (
                    <div className="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    {resource === "products" ? (
                                        <>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>On hand</th>
                                            <th>Status</th>
                                        </>
                                    ) : (
                                        <>
                                            <th>
                                                {resource === "suppliers"
                                                    ? "Contact"
                                                    : "Description"}
                                            </th>
                                            <th>Products</th>
                                        </>
                                    )}
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {records.data.map((x) => (
                                    <tr key={x.id}>
                                        <td>
                                            <Link
                                                className="record-name"
                                                href={`/${resource}/${x.id}`}
                                            >
                                                {x.name}
                                            </Link>
                                            {resource === "products" && (
                                                <small>{x.sku}</small>
                                            )}
                                        </td>
                                        {resource === "products" ? (
                                            <>
                                                <td>{x.category.name}</td>
                                                <td>{money(x.price)}</td>
                                                <td>{x.quantity}</td>
                                                <td>
                                                    <StockBadge product={x} />
                                                </td>
                                            </>
                                        ) : (
                                            <>
                                                <td>
                                                    {x.email ||
                                                        x.description ||
                                                        "—"}
                                                </td>
                                                <td>{x.products_count}</td>
                                            </>
                                        )}
                                        <td>
                                            <div className="row-actions">
                                                <Link
                                                    href={`/${resource}/${x.id}`}
                                                >
                                                    View
                                                </Link>
                                                {can(`${resource}.edit`) && (
                                                    <Link
                                                        href={`/${resource}/${x.id}/edit`}
                                                    >
                                                        Edit
                                                    </Link>
                                                )}
                                                {can(`${resource}.delete`) && (
                                                    <button
                                                        className="text-button danger-text"
                                                        onClick={() => {
                                                            if (
                                                                confirm(
                                                                    `Delete ${x.name}? Referenced records and stock history are protected.`,
                                                                )
                                                            )
                                                                router.delete(
                                                                    `/${resource}/${x.id}`,
                                                                    {
                                                                        preserveScroll: true,
                                                                    },
                                                                );
                                                        }}
                                                    >
                                                        Delete
                                                    </button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <Empty text="No matching records. Try another filter or add a record." />
                )}
                <div className="table-footer">
                    <span>{records.total} records</span>
                    <Pagination links={records.links} />
                </div>
            </section>
        </>
    );
}
export default function Catalog(props) {
    const { resource, mode, record } = props;
    const d = definitions[resource];
    const can = useCan();
    const title =
        mode === "index"
            ? d.title
            : mode === "show"
              ? record.name
              : `${mode === "edit" ? "Edit" : "New"} ${d.single}`;
    return (
        <AppLayout
            title={title}
            subtitle={
                mode === "index"
                    ? `Manage your ${d.title.toLowerCase()} with confidence.`
                    : "Keep every detail accounted for."
            }
            action={
                mode === "index" && can(`${resource}.create`) ? (
                    <Link className="button" href={`/${resource}/create`}>
                        + Add {d.single}
                    </Link>
                ) : (
                    <Link className="button secondary" href={`/${resource}`}>
                        Back to {d.title.toLowerCase()}
                    </Link>
                )
            }
        >
            {mode === "index" ? (
                <CatalogList {...props} />
            ) : mode === "show" ? (
                <section className="card detail-card">
                    <dl>
                        {d.fields.map(([k, label]) => (
                            <div key={k}>
                                <dt>{label}</dt>
                                <dd>
                                    {k === "category_id"
                                        ? record.category.name
                                        : k === "supplier_id"
                                          ? record.supplier.name
                                          : k === "price"
                                            ? money(record[k])
                                            : record[k] || "—"}
                                </dd>
                            </div>
                        ))}
                        {resource === "products" && (
                            <div>
                                <dt>On hand</dt>
                                <dd>
                                    {record.quantity} units ·{" "}
                                    <StockBadge product={record} />
                                </dd>
                            </div>
                        )}
                    </dl>
                    {can(`${resource}.edit`) && (
                        <Link
                            className="button"
                            href={`/${resource}/${record.id}/edit`}
                        >
                            Edit {d.single}
                        </Link>
                    )}
                </section>
            ) : (
                <CatalogForm
                    key={`${resource}-${mode}-${record?.id || "new"}`}
                    {...props}
                />
            )}
        </AppLayout>
    );
}
