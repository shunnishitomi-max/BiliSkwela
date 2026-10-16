import { cloneElement, isValidElement } from "react";
import { Link, usePage } from "@inertiajs/react";
export function useCan() {
    const user = usePage().props.auth?.user;
    return (permission) => user?.permissions?.includes(permission) ?? false;
}
export function Field({ label, name, error, children, ...props }) {
    return (
        <label className="field" htmlFor={name}>
            <span>{label}</span>
            {isValidElement(children) ? (
                cloneElement(children, {
                    id: name,
                    "aria-label": label,
                    "aria-describedby": error ? `${name}-error` : undefined,
                })
            ) : (
                <input
                    id={name}
                    aria-label={label}
                    name={name}
                    {...props}
                    aria-invalid={!!error}
                    aria-describedby={error ? `${name}-error` : undefined}
                />
            )}
            {error && (
                <small className="error" id={`${name}-error`}>
                    {error}
                </small>
            )}
        </label>
    );
}
export function Errors({ errors = {} }) {
    return (
        Object.keys(errors).length > 0 && (
            <div className="notice danger" role="alert">
                {Object.entries(errors).flatMap(([k, v]) =>
                    typeof v === "object" && v !== null
                        ? Object.entries(v).map(([sub, msg]) => (
                              <div key={`${k}-${sub}`}>{String(msg)}</div>
                          ))
                        : [<div key={k}>{String(v)}</div>],
                )}
            </div>
        )
    );
}
export function Pagination({ links = [] }) {
    return (
        <nav className="pagination" aria-label="Pagination">
            {links.map((link, i) =>
                link.url ? (
                    <Link
                        key={i}
                        className={link.active ? "page active" : "page"}
                        href={link.url}
                        preserveScroll
                    >
                        {link.label
                            .replace(/&laquo;/g, "Â«")
                            .replace(/&raquo;/g, "Â»")}
                    </Link>
                ) : (
                    <span key={i} className="page disabled">
                        {link.label
                            .replace(/&laquo;/g, "Â«")
                            .replace(/&raquo;/g, "Â»")}
                    </span>
                ),
            )}
        </nav>
    );
}
export function StockBadge({ product }) {
    return (
        <span
            className={`badge ${product.quantity <= product.minimum_stock ? "warning" : "green"}`}
        >
            {product.quantity === 0
                ? "Out of stock"
                : product.quantity <= product.minimum_stock
                  ? "Low stock"
                  : "In stock"}
        </span>
    );
}
export const money = (value) =>
    new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(Number(value));
export const date = (value) =>
    new Intl.DateTimeFormat("en-PH", {
        dateStyle: "medium",
        timeStyle: "short",
    }).format(new Date(value));
export function Empty({ text = "No records yet." }) {
    return (
        <div className="empty">
            <p>{text}</p>
        </div>
    );
}
