import { ToastTopEnd } from "@/Utils/alert";
import { router, usePage } from "@inertiajs/react";
import axios from "axios";
import { useEffect, useState } from "react";

export default function useInputPromo(setOpenModal, isUpdate = false, promo) {
    const { imageSliders } = usePage().props;

    const initialFormData = {
        image: null,
        is_active: 1,
        title: '',
        code: '',
        start_date: '',
        end_date: '',
    };

    const initialErrorData = {
        image: '',
        is_active: '',
        title: '',
        code: '',
        start_date: '',
        end_date: '',
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
                image: null,
                is_active: promo?.is_active ? 1 : 0,
                title: promo?.title ?? '',
                code: promo?.code ?? '',
                start_date: promo?.start_date ?? '',
                end_date: promo?.end_date ?? '',
            });
            promo?.image &&
                setImagePreview({
                    ...imagePreview,
                    image: `${promo?.image}` ?? null,
                });
        }
    }, [isUpdate, promo]);

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
                url: `/promos${isUpdate ? '/' + promo?.id + '/update' : ''}`,
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
