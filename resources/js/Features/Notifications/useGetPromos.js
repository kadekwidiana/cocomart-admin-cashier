import { router, usePage } from '@inertiajs/react';
import { pickBy } from 'lodash';
import { useState } from 'react';

export default function useGetNotifications() {
    const { notifications, filters } = usePage().props;

    const [isLoading, setIsLoading] = useState(false);
    const [params, setParams] = useState({
        perpage: filters?.perpage ?? 10,
        title: filters?.title ?? '',
        type: filters?.type ?? ''
    });

    const handleChange = (key, value) => {
        setParams((prev) => ({
            ...prev,
            [key]: value,
        }));
    };

    const getData = () => {
        setIsLoading(true);

        const paramsRequest = pickBy(params, (v) => v !== '' && v !== null);

        router.get(
            route('notifications.index'),
            paramsRequest,
            {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setIsLoading(false),
            }
        );
    };

    return {
        notifications,
        isLoading,
        params,
        handleChange,
        getData,
    };
}
