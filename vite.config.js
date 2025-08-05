import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    
    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                   
                    
                ],
                refresh: true,
            }),
        ],
        server: {
            host: env.VITE_HMR_HOST || '127.0.0.1',
            port: parseInt(env.VITE_HMR_PORT) || 5176,
            hmr: {
                host: env.VITE_HMR_HOST || '127.0.0.1',
                port: parseInt(env.VITE_HMR_PORT) || 5176,
            },
            cors: {
                origin: ['https://rem', 'https://worklite.ru', 'http://localhost', 'http://127.0.0.1'],
                credentials: true,
            },
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers': '*',
                'Access-Control-Allow-Credentials': 'true',
            },
        },
        preview: {
            cors: true,
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers': '*',
            },
        },
    };
});
