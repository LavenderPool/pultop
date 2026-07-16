import { createInertiaApp } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        if (name.startsWith('admin/auth/')) {
            return null;
        }

        if (name.startsWith('admin/')) {
            return AdminLayout;
        }

        return null;
    },
    strictMode: true,
    progress: {
        color: '#4B5563',
    },
});
