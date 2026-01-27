import { ToastTopEnd } from "@/Utils/alert";
import { router } from "@inertiajs/react";
import axios from "axios";
import { useEffect, useState } from "react";

export default function useInputNotification(setOpenModal, isUpdate = false, notification) {
    const initialFormData = {
        title: '',
        image: null,
        type: '',
        body: '',
    };

    const initialErrorData = {
        title: '',
        image: '',
        type: '',
        body: '',
    };

    const [formData, setFormData] = useState(initialFormData);

    const [isSubmitting, setIsSubmitting] = useState(false);

    const [errors, setErrors] = useState(initialErrorData);

    const [imagePreview, setImagePreview] = useState({
        image: ''
    });

    useEffect(() => {
        if (isUpdate) {
            setFormData({
                title: notification?.title ?? '',
                image: null,
                type: notification?.type ?? '',
                body: notification?.body ?? '',
            });
            notification?.image &&
                setImagePreview({
                    ...imagePreview,
                    image: `${notification?.image}` ?? null,
                });
        }
    }, [isUpdate, notification]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({
            ...formData,
            [name]: value,
        });
    };

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        const { name } = e.target;
        if (file) {
            setFormData({
                ...formData,
                [name]: file,
            });
            setImagePreview({
                ...imagePreview,
                [name]: URL.createObjectURL(file),
            });
        } else {
            setFormData({
                ...formData,
                [name]: null,
            });
        }
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
                url: `/notifications${isUpdate ? '/' + notification?.id + '/update' : ''}`,
                data: formData
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
                setImagePreview({
                    image: null,
                });
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
        imagePreview,
        isSubmitting,
        errors,
        handleChange,
        handleFileChange,
        handleSubmit
    };
}
