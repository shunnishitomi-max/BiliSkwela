import { useForm, usePage } from "@inertiajs/react";
import { useState } from "react";
import AppLayout from "../Layouts/AppLayout";
import { Field } from "../components/UI";
export default function Settings() {
    const user = usePage().props.auth.user;
    const profile = useForm({ name: user.name, email: user.email });
    const password = useForm({
        current_password: "",
        password: "",
        password_confirmation: "",
    });
    const [notice, setNotice] = useState("");
    return (
        <AppLayout
            title="My account"
            subtitle="Your details and sign-in security."
        >
            {notice && (
                <div className="notice success" role="status">
                    {notice}
                </div>
            )}
            <div className="dashboard-grid">
                <section className="card">
                    <h2>Profile</h2>
                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            profile.put("/user/profile-information", {
                                errorBag: "updateProfileInformation",
                                onSuccess: () => setNotice("Profile saved."),
                            });
                        }}
                    >
                        {[
                            ["name", "Full name", "text"],
                            ["email", "Email address", "email"],
                        ].map(([k, label, type]) => (
                            <Field
                                key={k}
                                label={label}
                                name={k}
                                type={type}
                                required
                                value={profile.data[k]}
                                onChange={(e) =>
                                    profile.setData(k, e.target.value)
                                }
                                error={profile.errors[k]}
                            />
                        ))}
                        <button
                            className="button"
                            disabled={profile.processing}
                        >
                            Save profile
                        </button>
                    </form>
                </section>
                <section className="card">
                    <h2>Change password</h2>
                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            password.put("/user/password", {
                                errorBag: "updatePassword",
                                onSuccess: () => {
                                    password.reset();
                                    setNotice("Password updated.");
                                },
                            });
                        }}
                    >
                        {[
                            ["current_password", "Current password"],
                            ["password", "New password"],
                            ["password_confirmation", "Confirm new password"],
                        ].map(([k, label]) => (
                            <Field
                                key={k}
                                label={label}
                                name={k}
                                type="password"
                                autoComplete={
                                    k === "current_password"
                                        ? "current-password"
                                        : "new-password"
                                }
                                required
                                value={password.data[k]}
                                onChange={(e) =>
                                    password.setData(k, e.target.value)
                                }
                                error={password.errors[k]}
                            />
                        ))}
                        <button
                            className="button"
                            disabled={password.processing}
                        >
                            Update password
                        </button>
                    </form>
                </section>
            </div>
        </AppLayout>
    );
}
