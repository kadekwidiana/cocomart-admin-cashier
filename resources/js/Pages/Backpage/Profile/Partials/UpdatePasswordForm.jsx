import useUpdatePassword from "@/Features/Profile/useUpdatePassword";
import { Button, Label, TextInput } from "flowbite-react";

export default function UpdatePasswordForm({ className = "" }) {
    const { data, errors, processing, handleChange, handleUpdatePassword } =
        useUpdatePassword();

    return (
        <section className={className}>
            <header>
                <h2 className="text-lg font-semibold text-gray-900">
                    Change Password
                </h2>

                <p className="mt-1 text-sm text-gray-600">
                    Make sure your account uses a long and random password to
                    stay secure.
                </p>
            </header>

            <form onSubmit={handleUpdatePassword} className="mt-4 space-y-2">
                <div>
                    <div className="mb-2 block">
                        <Label
                            htmlFor="current_password"
                            value="Current Password*"
                            color={errors.current_password ? "failure" : "gray"}
                        />
                    </div>
                    <TextInput
                        id="current_password"
                        name="current_password"
                        type="password"
                        placeholder="Enter your current password..."
                        required
                        value={data.current_password}
                        color={errors.current_password ? "failure" : "gray"}
                        onChange={handleChange}
                        helperText={errors.current_password}
                    />
                </div>

                <div>
                    <div className="mb-2 block">
                        <Label
                            htmlFor="password"
                            value="New Password*"
                            color={errors.password ? "failure" : "gray"}
                        />
                    </div>
                    <TextInput
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Enter your new password..."
                        required
                        value={data.password}
                        color={errors.password ? "failure" : "gray"}
                        onChange={handleChange}
                        helperText={errors.password}
                    />
                </div>

                <div>
                    <div className="mb-2 block">
                        <Label
                            htmlFor="password_confirmation"
                            value="Confirm New Password*"
                            color={
                                errors.password_confirmation
                                    ? "failure"
                                    : "gray"
                            }
                        />
                    </div>
                    <TextInput
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        placeholder="Confirm your new password..."
                        required
                        value={data.password_confirmation}
                        color={
                            errors.password_confirmation ? "failure" : "gray"
                        }
                        onChange={handleChange}
                        helperText={errors.password_confirmation}
                    />
                </div>

                <div className="flex items-center justify-end gap-4">
                    <Button
                        disabled={processing}
                        type="submit"
                        color="none"
                        className="bg-primary/80 hover:bg-primary/100 text-white"
                    >
                        Save
                    </Button>
                </div>
            </form>
        </section>
    );
}
