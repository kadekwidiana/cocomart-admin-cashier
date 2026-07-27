import { ToastTopEnd } from "@/Utils/alert";
import { router } from "@inertiajs/react";
import { useState } from "react";
import {
    TRANSACTION_STATUS_OPTIONS,
    PICKUP_STATUS_OPTIONS,
    SHIPMENT_STATUS_OPTIONS,
} from "@/Constants/statusOptions";

const CONFIG = {
    transaction: {
        endpoint: (id) => `/transactions/${id}/status`,
        options: TRANSACTION_STATUS_OPTIONS,
        getCurrentStatus: (transaction) => transaction?.status,
    },
    pickup: {
        endpoint: (id) => `/transactions/${id}/pickup-status`,
        options: PICKUP_STATUS_OPTIONS,
        getCurrentStatus: (transaction) => transaction?.pickup?.status,
    },
    shipment: {
        endpoint: (id) => `/transactions/${id}/shipment-status`,
        options: SHIPMENT_STATUS_OPTIONS,
        getCurrentStatus: (transaction) => transaction?.shipment?.status,
    },
};

export default function useUpdateStatus(setOpenModal, type, transaction) {
    const config = CONFIG[type];

    const [status, setStatus] = useState(
        config.getCurrentStatus(transaction) ?? "",
    );
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState({});

    const handleChange = (e) => {
        setStatus(e.target.value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        setIsSubmitting(true);
        setErrors({});

        router.patch(
            config.endpoint(transaction.id),
            { status },
            {
                preserveScroll: true,
                onSuccess: () => {
                    ToastTopEnd.fire({
                        icon: "success",
                        title: "Status updated successfully",
                    });
                    setOpenModal(false);
                },
                onError: (errors) => {
                    setErrors(errors);
                    ToastTopEnd.fire({
                        icon: "error",
                        title: errors.message || "Terjadi kesalahan.",
                    });
                },
                onFinish: () => setIsSubmitting(false),
            },
        );
    };

    return {
        status,
        options: config.options,
        isSubmitting,
        errors,
        handleChange,
        handleSubmit,
    };
}