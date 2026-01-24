import { ToastTopEnd } from '@/Utils/alert';
import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';

export default function useLogin() {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset("password");
        };
    }, []);

    const handleSubmitLogin = (e) => {
        e.preventDefault();

        post(route("login"), {
            onSuccess: () => {
                ToastTopEnd.fire({
                    icon: "success",
                    title: "Login Successful!",
                });
            },
            onError: () => {
                ToastTopEnd.fire({
                    icon: "error",
                    title: "Login Failed! Please check your details again.",
                });
            },
        });
    };

    return {
        data,
        setData,
        processing,
        errors,
        reset,
        handleSubmitLogin,
    };
}
