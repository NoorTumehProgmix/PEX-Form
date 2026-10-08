import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import cp from "vite-plugin-cp";

const CMSPath = "modules/Backend/resources/assets";
const outputDir = "build";
const cpOutputDir = "public/backend_assets";

export default defineConfig({
    plugins: [
        laravel({
            buildDirectory: outputDir,
            input: [`${CMSPath}/css/app.css`, `${CMSPath}/js/app.js`],
        }),
        cp({
            targets: [
                {
                    src: CMSPath + "/public/images",
                    dest: cpOutputDir + "/images",
                },
            ],
        }),
    ],
});
