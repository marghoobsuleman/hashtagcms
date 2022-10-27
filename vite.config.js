import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
const path = require('path');
import { viteStaticCopy } from 'vite-plugin-static-copy'


let configBe = {
    build: {
        commonjsOptions: {
            transformMixedEsModules: true
        }
    },
    plugins: [
        laravel(['resources/assets/be/neo/sass/app.scss', 'resources/assets/be/neo/js/app.js']),
        vue({
            template: {
                transformAssetUrls: {
                    // The Vue plugin will re-write asset URLs, when referenced
                    // in Single File Components, to point to the Laravel web
                    // server. Setting this to `null` allows the Laravel plugin
                    // to instead re-write asset URLs to point to the Vite
                    // server instead.
                    base: null,

                    // The Vue plugin will parse absolute URLs and treat them
                    // as absolute paths to files on disk. Setting this to
                    // `false` will leave absolute URLs un-touched so they can
                    // reference assets in the public directory as expected.
                    includeAbsolute: false,
                }
            }
        }),
        viteStaticCopy({
            targets: [
                {
                    src: path.resolve(__dirname, 'resources/assets/be/neo/img'),
                    dest: 'assets'
                }
            ]
        })
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap')
        }
    }
};

export default defineConfig(configBe);