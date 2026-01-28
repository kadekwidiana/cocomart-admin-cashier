import { router, usePage } from '@inertiajs/react';
import { pickBy } from 'lodash';
import { useState } from 'react';

export default function useGetUsers() {
    const { users, filters } = usePage().props;

    const [isLoading, setIsLoading] = useState(false);
    const [params, setParams] = useState({
        perpage: filters?.perpage ?? 10,
        name: filters?.name ?? '',
        role: filters?.role ?? '',
        is_active: filters?.is_active ?? '',
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
            route('users.index'),
            paramsRequest,
            {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setIsLoading(false),
            }
        );
    };

    return {
        users,
        isLoading,
        params,
        handleChange,
        getData,
    };
}
