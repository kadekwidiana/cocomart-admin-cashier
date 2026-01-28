import { ToastTopEnd } from "@/Utils/alert";
import { useForm, usePage } from "@inertiajs/react";

export default function useUpdateProfile() {
    const {
        props: {
            auth: { user },
        },
    } = usePage();

    const { data, setData, post, errors, processing, recentlySuccessful } =
        useForm({
            name: user.name,
            email: user.email,
            role: user.role,
            phone_number: user?.phone_number,
            address: user?.address,
        });

    const handleChange = (e) => {
        const { name, value } = e.target;
        setData({
            ...data,
            [name]: value,
        });
    };

    const handleSubmitUpdateProfile = (e) => {
        e.preventDefault();

        post(route("profile.update", { userId: user.id }), {
            onSuccess: () => {
                ToastTopEnd.fire({
                    icon: "success",
                    title: "Your profile has been successfully updated.",
                });
            },
        });
    };

    return {
        data,
        errors,
        processing,
        handleChange,
        handleSubmitUpdateProfile,
        setData
    };
}
