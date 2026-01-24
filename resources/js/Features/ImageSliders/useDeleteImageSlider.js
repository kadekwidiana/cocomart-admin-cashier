import { ToastTopEnd } from '@/Utils/alert';
import { router } from '@inertiajs/react';
import axios from 'axios';
import Swal from 'sweetalert2';

export default function useDeleteImageSlider() {
    const handleDelete = async (id) => {
        try {
            const response = await axios({
                method: 'DELETE',
                url: `/image-sliders/${id}`,
            });

            const { status, data } = response;
            const { message } = data;

            if (status === 200) {
                ToastTopEnd.fire({
                    icon: "success",
                    title: message,
                });
                router.reload();
            } else {
                ToastTopEnd.fire({
                    icon: "error",
                    title: message,
                });
            }
        } catch (error) {
            if (error.response) {
                const { message } = error.response.data;
                ToastTopEnd.fire({
                    icon: "error",
                    title: message || 'Terjadi kesalahan.',
                });
            } else {
                ToastTopEnd.fire({
                    icon: "error",
                    title: 'Terjadi kesalahan.' + error,
                });
            }
        }
    };

    const deleteDataConfirm = (id) => {
        Swal.fire({
            title: "Are you sure you want to delete this data?",
            text: "Any related data will also be deleted and cannot be recovered.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
        }).then(async (result) => {
            if (result.isConfirmed) {
                await handleDelete(id);
            }
        });
    };

    return {
        deleteDataConfirm
    };
}
