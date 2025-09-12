import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/auth/login.css',
                'resources/css/auth/register.css',
                'resources/css/auth/reset-password.css',
                'resources/css/auth/new-password.css',
                'resources/css/auth/verify-otp.css',
                'resources/js/auth/login.js',
                'resources/js/auth/register.js',
                'resources/js/auth/reset-password.js',
                'resources/js/auth/new-password.js',
                'resources/js/auth/verify-otp.js',
                'resources/css/homepage.css',
                'resources/js/homepage.js',
            ],
            refresh: true,
        }),
    ],
});