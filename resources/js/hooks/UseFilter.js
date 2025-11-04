import { router } from '@inertiajs/react';
import pkg from 'lodash';
import { useCallback, useEffect } from 'react';

export function useFilter({ route, values, only, wait = 300 }) {
    const { debounce, pickBy } = pkg;

    const reload = useCallback(
        debounce((query) => {
            router.get(route, pickBy(query), {
                preserveState: true,
                preserveScroll: true,
                only: only,
            });
        }, wait),
        [],
    );

    useEffect(() => reload(values), [values, reload]);

    return { values };
}
