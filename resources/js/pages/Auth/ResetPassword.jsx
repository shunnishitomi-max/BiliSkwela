import { useForm } from "@inertiajs/react";
import AuthLayout from "../../Layouts/AuthLayout";
import { Field } from "../../components/UI";
export default function ResetPassword({ token, email }) {
    const f = useForm({
        token,
        email: email || "",
        password: "",
        password_confirmation: "",
    });
    return (
        <AuthLayout title="Choose a new password">
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    f.post("/reset-password", {
                        onFinish: () =>
                            f.reset("password", "password_confirmation"),
                    });
                }}
            >
                {[
                    ["email", "Email address", "email"],
                    ["password", "New password", "password"],
                    [
                        "password_confirmation",
                        "Confirm new password",
                        "password",
                    ],
                ].map(([k, label, type]) => (
                    <Field
                        key={k}
                        label={label}
                        name={k}
                        type={type}
                        autoComplete={
                            type === "password" ? "new-password" : "email"
                        }
                        required
                        value={f.data[k]}
                        onChange={(e) => f.setData(k, e.target.value)}
                        error={f.errors[k]}
                    />
                ))}
                <button className="button full" disabled={f.processing}>
                    Reset password
                </button>
            </form>
        </AuthLayout>
    );
}
