const mix = require('laravel-mix');
require('dotenv').config();
//mix.disableNotifications();

//backend

mix.js('resources/assets/be/js/app.js', 'public/assets/be/js')
    .sass('resources/assets/be/sass/app.scss', 'public/assets/be/css').vue({ version: 2 });
mix.js('resources/assets/be/js/error-handler.js', 'public/assets/be/js').vue({ version: 2 });
mix.js('resources/assets/be/js/map.js', 'public/assets/be/js').vue({ version: 2 });
mix.js('resources/assets/be/js/ie-polyfills.js', 'public/assets/be/js').vue({ version: 2 });
mix.copyDirectory('resources/assets/be/js/vendors/tinymce', 'public/assets/be/js/vendors/tinymce').vue({ version: 2 });
mix.copyDirectory('resources/assets/be/img', 'public/assets/be/img');

//Installer
mix.js('resources/assets/js/installer.js', 'public/assets/installer/js');



let themes = [
    {
        theme:{source:'basic', type:'theme'}, //folder
        assets: [
            {source:'js/app.js', target:'js', type:'js'},
            {source:'sass/app.scss', target:'css', type:'css'},
            {source:'img', target:'img', type:'copy'},
            {source:'fonts', target:'fonts', type:'copy'}
        ]
    }

];

let resourceDir = "resources/assets/fe";
let targetDir = "public/assets/fe";

for(let i=0; i<themes.length; i++) {

    let current = themes[i];
    console.log(current);
    let theme = current.theme.source;
    let assets = current.assets;
    for(let k in assets) {
        let type = assets[k]["type"];
        let currentKeyNode = assets[k];
        switch (type) {
            case "js":
                mix.js(`${resourceDir}/${theme}/${currentKeyNode.source}`, `${targetDir}/${theme}/${currentKeyNode.target}`).vue({ version: 2 });
                break;
            case "css":
                mix.sass(`${resourceDir}/${theme}/${currentKeyNode.source}`, `${targetDir}/${theme}/${currentKeyNode.target}`).options({processCssUrls: false});
                break;
            case "copy":
                mix.copyDirectory(`${resourceDir}/${theme}/${currentKeyNode.source}`, `${targetDir}/${theme}/${currentKeyNode.target}`);
                break;

        }
    }

}

