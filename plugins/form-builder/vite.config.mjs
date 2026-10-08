import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const PluginPath = __dirname +'/src/resources/assets';
const outputDir = 'plugins/form-builder';

export default defineConfig({
    plugins: [
        laravel({
            buildDirectory: outputDir,
            input: [`${PluginPath}/app.js`,`${PluginPath}/app.css`],
        }),
    ],
});
