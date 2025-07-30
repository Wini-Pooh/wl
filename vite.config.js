import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    
    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/css/mobile-utilities.css',
                    'resources/css/mobile-projects.css',
                    'resources/css/home-page.css',
                    'resources/js/app.js',
                    'public/js/home-page.js',
                ],
                refresh: true,
            }),
        ],
        
    };
});
