import { useForm } from "@inertiajs/react";
import AppLayout from "../Layouts/AppLayout";
import { Pagination, Field, Empty } from "../components/UI";
function UserRow({ user }) {
    const f = useForm({ role: user.roles[0]?.name || "Staff" });
    return (
        <tr>
            <td>
                <strong>{user.name}</strong>
                <small>{user.email}</small>
            </td>
            <td>{user.roles.map((x) => x.name).join(", ") || "No role"}</td>
            <td>
                <form
                    className="inline-form"
                    onSubmit={(e) => {
                        e.preventDefault();
                        f.put(`/users/${user.id}/role`, {
                            preserveScroll: true,
                        });
                    }}
                >
                    <select
                        aria-label={`Role for ${user.name}`}
                        value={f.data.role}
                        onChange={(e) => f.setData("role", e.target.value)}
                    >
                        {["Admin", "Manager", "Staff"].map((x) => (
                            <option key={x}>{x}</option>
                        ))}
                    </select>
                    <button
                        className="button secondary"
                        disabled={f.processing}
                    >
                        Save role
                    </button>
                    {f.errors.role && (
                        <small className="error">{f.errors.role}</small>
                    )}
                </form>
            </td>
        </tr>
    );
}
export default function Users({ users, filters = {} }) {
    const f = useForm({ search: filters.search || "" });
    return (
        <AppLayout
            title="Team access"
            subtitle="Manage staff roles and access to shop records."
        >
            <section className="card">
                <p className="hint">
                    Only Admin can assign roles. At least one Admin must remain.
                </p>
                <form
                    className="filters"
                    onSubmit={(e) => {
                        e.preventDefault();
                        f.get("/users", { preserveState: true });
                    }}
                >
                    <Field
                        label="Find a teammate"
                        name="search"
                        value={f.data.search}
                        onChange={(e) => f.setData("search", e.target.value)}
                    />
                    <button
                        className="button secondary"
                        disabled={f.processing}
                    >
                        Search
                    </button>
                </form>
                {users.data.length ? (
                    <div className="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Current role</th>
                                    <th>Access level</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.data.map((x) => (
                                    <UserRow
                                        key={`${x.id}-${x.roles.map((r) => r.name).join("-")}`}
                                        user={x}
                                    />
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <Empty />
                )}
                <Pagination links={users.links} />
            </section>
        </AppLayout>
    );
}
