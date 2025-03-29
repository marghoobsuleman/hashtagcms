const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');

const fs = require('fs');

let package_dir = "";

function makeArrays(themes, resourceDir, targetDir) {
    let entries = {};
    let copies = [];
    for(let i=0; i<themes.length; i++) {
        let current = themes[i];
        let theme = current.theme.source;
        let assets = current.assets;
        for(let k in assets) {
            let type = assets[k]["type"];
            let currentKeyNode = assets[k];
            switch (type) {
                case "js":
                case "css":
                    entries[`${targetDir}/${theme}/${currentKeyNode.target}`] = `./${resourceDir}/${theme}/${currentKeyNode.source}`;
                    break;
                case "copy":
                    copies.push({ from: `${resourceDir}/${theme}/${currentKeyNode.source}`, to: `${targetDir}/${theme}/${currentKeyNode.target}` });
                    break;

            }
        }
    }
    return {entries, copies};
}

let themesForFrontend = [
    {
        theme:{source:'basic', type:'theme'}, //folder
        assets: [
            {source:'js/app.js', target:'js/app', type:'js'},
            {source:'sass/app.scss', target:'css/app', type:'css'},
            {source:'img', target:'img', type:'copy'},
            {source:'fonts', target:'fonts', type:'copy'}
        ]
    }
];

let themesForBackend = [
    {
        theme:{source:'neo', type:'theme'}, //folder
        assets: [
            {source:'js/app.js', target:'js/app', type:'js'},
            {source:'js/dashboard.js', target:'js/dashboard', type:'js'},
            {source:'js/error-handler.js', target:'js/error-handler', type:'js'},
            {source:'js/editor.js', target:'js/editor', type:'js'},
            {source:'sass/app.scss', target:'css/app', type:'css'},
            {source:'../common/js/vendors', target:'js/vendors', type:'copy'},
            {source:'img', target:'img', type:'copy'}
        ]
    }
];



let toBeBuildF = makeArrays(themesForFrontend, `resources/assets${package_dir}/fe`, `public/assets${package_dir}/fe`);
let toBeBuildB =  makeArrays(themesForBackend, `resources/assets${package_dir}/be`, `public/assets${package_dir}/be`);
//add installer
toBeBuildB.entries[`public/assets/installer/js/installer`] = `./resources/assets${package_dir}/js/installer.js`;
console.log({...toBeBuildB.entries, ...toBeBuildF.entries});
console.log("Please wait. Building...");
module.exports = {
    stats: {
        all: true,
        errors: true,
        errorDetails: true
    },
    mode: 'development',
    entry: {...toBeBuildB.entries, ...toBeBuildF.entries},
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname),
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: 'babel-loader'
            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    'vue-style-loader',
                    MiniCssExtractPlugin.loader,
                    // Load the CSS, set url = false to prevent following urls to fonts and images.
                    { loader: "css-loader", options: { url: false, importLoaders: 1 } },
                    // Add browser prefixes and minify CSS.
                    { loader: 'postcss-loader', options: { postcssOptions: { plugins: [autoprefixer(), cssnano()], }, }},
                    // Load the SCSS/SASS
                    { loader: 'sass-loader'}
                ],
            }
        ]
    },
    plugins: [
        //new CleanWebpackPlugin(),
        new VueLoaderPlugin(),
        new MiniCssExtractPlugin({
            filename: '[name].css',
        }),
        new CopyWebpackPlugin({
            patterns: [...toBeBuildB.copies, ...toBeBuildF.copies]
        }),
        {
            apply: (compiler) => {
                compiler.hooks.done.tap('everythingIsDone', (compilation) => {
                    console.log("All Done. Cheer!")
                });

            }
        },
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
        extensions: ['.js', '.vue']
    },
};
