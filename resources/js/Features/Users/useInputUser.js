import { ToastTopEnd } from "@/Utils/alert";
import { router } from "@inertiajs/react";
import axios from "axios";
import { useEffect, useState } from "react";

export default function useInputUser(setOpenModal, isUpdate = false, user) {
    const initialFormData = {
        name: '',
        email: '',
        password: '',
        role: 'CASHIER',
        is_active: 1,
        phone_number: '',
        address: '',
        oxy_location_id: '',
    };

    const initialErrorData = {
        name: '',
        email: '',
        password: '',
        role: '',
        is_active: '',
        phone_number: '',
        address: '',
        oxy_location_id: '',
    };

    const [formData, setFormData] = useState(initialFormData);

    const [isSubmitting, setIsSubmitting] = useState(false);

    const [errors, setErrors] = useState(initialErrorData);

    useEffect(() => {
        if (isUpdate) {
            setFormData({
                name: user?.name ?? '',
                email: user?.email ?? '',
                password: '',
                role: user?.role ?? '',
                is_active: user?.is_active ? 1 : 0,
                phone_number: user?.phone_number ?? '',
                address: user?.address ?? '',
                oxy_location_id: user?.oxy_location_id ?? '',
            });
        }
    }, [isUpdate, user]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({
            ...formData,
            [name]: value,
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        setIsSubmitting(true);

        try {
            const response = await axios({
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                method: 'POST',
                url: `/users${isUpdate ? '/' + user?.id + '/update' : ''}`,
                data: {
                    ...formData,
                    oxy_location_id: formData.role === 'CASHIER' ? formData.oxy_location_id : null,
                    password_confirmation: formData.password,
                }
            });

            const { status, data } = response;
            const { message, errors } = data;

            if (status === 200 || status === 201) {
                ToastTopEnd.fire({
                    icon: "success",
                    title: message,
                });
                setFormData(initialFormData);
                setErrors(initialErrorData);
                setOpenModal(false);
                router.reload();
            } else {
                ToastTopEnd.fire({
                    icon: "error",
                    title: message,
                });
                setErrors(errors || {});
            }
        } catch (error) {
            if (error.response) {
                const { message, errors } = error.response.data;
                ToastTopEnd.fire({
                    icon: "error",
                    title: message || 'Terjadi kesalahan.',
                });
                setErrors(errors || {});
            } else {
                ToastTopEnd.fire({
                    icon: "error",
                    title: 'Terjadi kesalahan.' + error,
                });
            }
        } finally {
            setIsSubmitting(false);
        }
    };

    return {
        formData,
        isSubmitting,
        errors,
        handleChange,
        handleSubmit
    };
}
