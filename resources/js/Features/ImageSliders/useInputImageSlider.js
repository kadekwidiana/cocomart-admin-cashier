import { ToastTopEnd } from "@/Utils/alert";
import { router, usePage } from "@inertiajs/react";
import axios from "axios";
import { useEffect, useState } from "react";

export default function useInputImageSlider(setOpenModal, isUpdate = false, imageSlider) {
    const { imageSliders } = usePage().props;

    const initialFormData = {
        image: null,
        link: '',
        index: imageSliders.total + 1,
        is_active: 1,
    };

    const initialErrorData = {
        image: '',
        link: '',
        index: '',
        is_active: '',
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
                link: imageSlider?.link ?? '',
                index: imageSlider?.index ?? null,
                is_active: imageSlider?.is_active ? 1 : 0,
            });
            imageSlider?.image &&
                setImagePreview({
                    ...imagePreview,
                    image: `${imageSlider?.image}` ?? null,
                });
        }
    }, [isUpdate, imageSlider]);

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
                url: `/image-sliders${isUpdate ? '/' + imageSlider?.id + '/update' : ''}`,
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
