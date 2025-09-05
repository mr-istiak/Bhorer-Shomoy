/** @type {import('vite').UserConfig} */
import path from 'path';
import chokidar from 'chokidar';
import { glob } from 'fs/promises';
import { exec } from 'child_process';
import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite'
import { promisify } from 'util';
const execAsync = promisify(exec);

export default defineConfig({
    root: path.resolve(__dirname), // project root
    server: {
        host: true,        // Listen on all network interfaces
        port: 5173,        // default port
        strictPort: true,  // fail if port is in use
        cors: true,  
        watch: {
            usePolling: true,
            interval: 100,
            ignored: ['**/node_modules/**'],
        },
        fs: {
            allow: [
                path.resolve(__dirname), // Allow whole project root
            ],
        },
    },
    resolve: {
        alias: [
            { find: '@', replacement: path.resolve(__dirname, 'src/js') }
        ]
    },
    build: {
        outDir: path.resolve(__dirname, 'public/build'),
        copyPublicDir: false,
        emptyOutDir: true,
        manifest: true, // Generate a manifest.json file for the build. (default: false) https://vitejs.dev/config/#build-manifestanifest: true,
        rollupOptions: {
            input: {
                app: path.resolve(__dirname, 'src/js/app.ts'),
                style: path.resolve(__dirname, 'src/css/app.css'),
            },
            output: {
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
            }
        },
    },
    plugins: [
        tailwindcss(),
        {
            name: 'watch-php',
            async configureServer(server) {
                // Watch all PHP files in the project recursively 
                const phpFiles = [];
                for await (const phpFile of  glob('src/**/*.php', { cwd: process.cwd(), ignore: '**/node_modules/**' })) {
                    phpFiles.push(phpFile);
                } 
                const phpWatcher = chokidar.watch(phpFiles, {
                    ignoreInitial: true,
                    usePolling: true,
                    interval: 100,
                    cwd: process.cwd(), // relative to project root
                });

                phpWatcher.on('all',async () => {
                    try {
                        // Run the artisan command
                        await execAsync('php artisan htmlblade:build', {
                            cwd: '/home/mristiak/sites/upanel.bhorershomoy'
                        });
                        // Trigger Vite full reload after build finishes
                        server.ws.send({
                            type: 'full-reload',
                            path: '*',
                        });
                        console.log('✅ Blade build complete, browser reloaded.');
                    } catch (err) {
                        console.error('❌ Error running blade build:', err);
                    }
                });
            }
        }
    ]
});
