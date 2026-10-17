import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/user/home.css',
                'resources/css/user/profile.css',
                'resources/css/user/address.css',
                'resources/css/user/my-promotions.css',
                'resources/css/user/product-detail.css',
                'resources/css/admin/auth.css',
                'resources/css/admin/dashboard.css',
                'resources/css/admin/overview.css',
                'resources/css/admin/category.css',
                'resources/css/admin/promotion.css',
                'resources/js/admin/category.js',
                'resources/js/admin/promotion.js',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
