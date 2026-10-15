import { Link, useForm } from "@inertiajs/react";
import AuthLayout from "../../Layouts/AuthLayout";
import { Field } from "../../components/UI";
export default function ForgotPassword({ status }) {
    const f = useForm({ email: "" });
    return (
        <AuthLayout title="Reset your password">
            <p className="subtitle">
                Enter your account email to request a reset link.
            </p>
            {status && (
                <div className="notice success" role="status">
                    {status}
                </div>
            )}
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    f.post("/forgot-password");
                }}
            >
                <Field
                    label="Email address"
                    name="email"
                    type="email"
                    autoComplete="email"
                    required
                    value={f.data.email}
                    onChange={(e) => f.setData("email", e.target.value)}
                    error={f.errors.email}
                />
                <button className="button full" disabled={f.processing}>
                    Send reset link
                </button>
            </form>
            <p className="auth-links">
                <Link href="/login">Back to sign in</Link>
            </p>
        </AuthLayout>
    );
}
