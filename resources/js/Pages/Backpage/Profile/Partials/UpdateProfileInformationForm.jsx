import useUpdateProfile from "@/Features/Profile/useUpdateProfile";
import { Button, Label, TextInput } from "flowbite-react";

export default function UpdateProfileInformationForm({
    mustVerifyEmail,
    status,
    className = "",
}) {
    const {
        data,
        errors,
        processing,
        handleChange,
        handleSubmitUpdateProfile,
        setData,
    } = useUpdateProfile();

    return (
        <section className={className}>
            <header>
                <h2 className="text-lg font-semibold text-gray-900">
                    Profile Information
                </h2>
                <p className="mt-1 text-sm text-gray-600">
                    Update your profile information and make sure all details
                    are correct before saving.
                </p>
            </header>

            <form
                onSubmit={handleSubmitUpdateProfile}
                className="mt-4 space-y-2"
                encType="multipart/form-data"
            >
                <div>
                    <div className="mb-2 block">
                        <Label
                            htmlFor="name"
                            value="Name*"
                            color={errors.name ? "failure" : "gray"}
                        />
                    </div>
                    <TextInput
                        id="name"
                        name="name"
                        type="text"
                        placeholder="Enter your name..."
                        required
                        value={data.name}
                        color={errors.name ? "failure" : "gray"}
                        onChange={handleChange}
                        helperText={errors.name}
                    />
                </div>

                <div>
                    <div className="mb-2 block">
                        <Label
                            htmlFor="email"
                            value="Email*"
                            color={errors.email ? "failure" : "gray"}
                        />
                    </div>
                    <TextInput
                        id="email"
                        name="email"
                        type="text"
                        placeholder="Enter your email..."
                        required
                        value={data.email}
                        color={errors.email ? "failure" : "gray"}
                        onChange={handleChange}
                        helperText={errors.email}
                        readOnly
                    />
                </div>

                <div>
                    <div className="mb-2 block">
                        <Label
                            htmlFor="role"
                            value="Role*"
                            color={errors.role ? "failure" : "gray"}
                        />
                    </div>
                    <TextInput
                        id="role"
                        name="role"
                        type="text"
                        placeholder="Role"
                        required
                        readOnly
                        value={data.role}
                        color={errors.role ? "failure" : "gray"}
                        onChange={handleChange}
                        helperText={errors.role}
                    />
                </div>

                <div className="flex items-center justify-end gap-4">
                    <Button
                        disabled={processing}
                        type="submit"
                        color="none"
                        className="bg-primary hover:bg-yellow-500/100 text-white"
                    >
                        Save
                    </Button>
                </div>
            </form>
        </section>
    );
}
