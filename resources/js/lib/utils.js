import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs) {
    return twMerge(clsx(inputs));
}

export const FINEPAYMENTSTATUS = {
    PENDING: 'tertunda',
    SUCCESS: 'sukses',
    FAILED: 'gagal',
};

export function flashMessage(params) {
    return params.props.flash_message;
}

export function formatToRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}

export const message = {
    401: {
        title: 'Unauthorized',
        description: 'Sorry, you are not authorized to access this page.',
        status: '401',
    },
    403: {
        title: 'Forbidden',
        description: 'Sorry, you do not have permission to access this page.',
        status: '403',
    },
    404: {
        title: 'Page Not Found',
        description:
            'Sorry, the page you are looking for could not be found. Please check the URL or go back to the homepage.',
        status: '404',
    },
    429: {
        title: 'Too Many Requests',
        description: 'Sorry, you have made too many requests. Please try again later.',
        status: '429',
    },
    503: {
        title: 'Service Unavailable',
        description: 'Sorry, the service is currently unavailable. Please try again later.',
        status: '503',
    },
    500: {
        title: 'Server Error',
        description: 'Oops, an error occurred on our server. Please try again later.',
        status: '500',
    },
};
