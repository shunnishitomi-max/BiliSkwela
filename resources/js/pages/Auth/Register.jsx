import { Link, useForm } from "@inertiajs/react";
import AuthLayout from "../../Layouts/AuthLayout";
import { Field } from "../../components/UI";
export default function Register() {
    const f = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    });
    return (
        <AuthLayout title="Join the workspace">
            <p className="subtitle">New accounts receive Staff access.</p>
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    f.post("/register", {
                        onFinish: () =>
                            f.reset("password", "password_confirmation"),
                    });
                }}
            >
                {[
                    ["name", "Full name", "text", "name"],
                    ["email", "Email address", "email", "username"],
                    ["password", "Password", "password", "new-password"],
                    [
                        "password_confirmation",
                        "Confirm password",
                        "password",
                        "new-password",
                    ],
                ].map(([k, label, type, complete]) => (
                    <Field
                        key={k}
                        label={label}
                        name={k}
                        type={type}
                        autoComplete={complete}
                        required
                        value={f.data[k]}
                        onChange={(e) => f.setData(k, e.target.value)}
                        error={f.errors[k]}
                    />
                ))}
                <button className="button full" disabled={f.processing}>
                    Create account
                </button>
            </form>
            <p className="auth-links">
                Already registered? <Link href="/login">Sign in</Link>
            </p>
        </AuthLayout>
    );
}
