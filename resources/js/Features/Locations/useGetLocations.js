import { router, usePage } from '@inertiajs/react';
import { pickBy } from 'lodash';
import { useState } from 'react';

export default function useGetLocations() {
    const { locations, filters } = usePage().props;

    const [isLoading, setIsLoading] = useState(false);
    const [params, setParams] = useState({
        name: filters?.name ?? '',
        code: filters?.code ?? '',
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
            route('locations.index'),
            paramsRequest,
            {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setIsLoading(false),
            }
        );
    };

    return {
        locations,
        isLoading,
        params,
        handleChange,
        getData,
    };
}
