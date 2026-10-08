import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import cp from 'vite-plugin-cp';

import postcssRTLCSS from 'postcss-rtlcss';
import {Mode, Source} from 'postcss-rtlcss/options';

const FrontEndPath = 'modules/Frontend/resources/assets';
const outputDir = 'front';
const cpOutputDir = 'public/assets';

export default defineConfig({
    plugins: [
        laravel({
            buildDirectory: outputDir,
            input: [
                `${FrontEndPath}/js/jquery.js`,
                `${FrontEndPath}/js/home.js`,
                `${FrontEndPath}/css/home.css`,
                `${FrontEndPath}/js/register.js`,
                `${FrontEndPath}/css/register.css`,
                `${FrontEndPath}/js/inner.js`,
                `${FrontEndPath}/css/inner.css`
            ],
        }),
        cp({
            targets: [
                {src: FrontEndPath + '/fonts', dest: cpOutputDir + '/fonts', flatten: false},
                {src: FrontEndPath + '/images', dest: cpOutputDir + '/images', flatten: false},
            ]
        }),
    ],
    assetsInclude: ['**/*.png'],
    css: {
        postcss: {
            plugins: [
                postcssRTLCSS({
                    mode: Mode.combined,
                    source: Source.ltr,
                    processKeyFrames: true
                }),
            ],
        },
    },
});
