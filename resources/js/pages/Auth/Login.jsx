import { Link, useForm } from "@inertiajs/react";
import AuthLayout from "../../Layouts/AuthLayout";
import { Field } from "../../components/UI";
export default function Login({ status }) {
    const f = useForm({ email: "", password: "", remember: false });
    return (
        <AuthLayout title="Welcome back">
            <p className="subtitle">Sign in to your inventory workspace.</p>
            {status && <div className="notice success">{status}</div>}
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    f.post("/login", { onFinish: () => f.reset("password") });
                }}
            >
                <Field
                    label="Email address"
                    name="email"
                    type="email"
                    required
                    autoComplete="username"
                    value={f.data.email}
                    onChange={(e) => f.setData("email", e.target.value)}
                    error={f.errors.email}
                />
                <Field
                    label="Password"
                    name="password"
                    type="password"
                    required
                    autoComplete="current-password"
                    value={f.data.password}
                    onChange={(e) => f.setData("password", e.target.value)}
                    error={f.errors.password}
                />
                <div className="form-row">
                    <label className="checkbox">
                        <input
                            type="checkbox"
                            checked={f.data.remember}
                            onChange={(e) =>
                                f.setData("remember", e.target.checked)
                            }
                        />
                        Remember me
                    </label>
                    
                </div>
                <button className="button full" disabled={f.processing}>
                    Sign in
                </button>
            </form>
            <p className="auth-links">Use the seeded Admin account for this checkpoint.</p>
        </AuthLayout>
    );
}
