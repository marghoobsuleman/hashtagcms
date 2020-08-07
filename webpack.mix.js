const mix = require('laravel-mix');
require('dotenv').config();
mix.disableNotifications();

//backend
mix.js('resources/assets/be/js/app.js', 'public/assets/be/js')
    .sass('resources/assets/be/sass/app.scss', 'public/assets/be/css');
mix.js('resources/assets/be/js/error-handler.js', 'public/assets/be/js');
mix.js('resources/assets/be/js/map.js', 'public/assets/be/js');
mix.js('resources/assets/be/js/ie-polyfills.js', 'public/assets/be/js');
mix.copyDirectory('resources/assets/be/js/vendors/tinymce', 'public/assets/be/js/vendors/tinymce');

//Installer
mix.js('resources/assets/js/installer.js', 'public/assets/installer/js');


//Theme config
let themes = [
            {
                theme:{source:'basic', type:'theme'}, //folder
                assets: [
                    {source:'js/app.js', target:'js', type:'js'},
                    {source:'js/vendors', target:'js/vendors', type:'copy'},
                    {source:'sass/app.scss', target:'css', type:'css'},
                    {source:'img', target:'img', type:'copy'}
                ]
            }

            ];

for(let i=0; i<themes.length; i++) {

    let current = themes[i];
    let theme = current.theme.source;
    let assets = current.theme.assets
    console.log(theme);
    for(var k in assets) {
        let type = assets[k]["type"];
        let currentKeyNode = assets[k];
        switch (type) {
            case "js":
                mix.js(`resources/assets/fe/${theme}/${currentKeyNode.source}`, `public/assets/fe/${theme}/${currentKeyNode.target}`);
                break;
            case "css":
                mix.sass(`resources/assets/fe/${theme}/${currentKeyNode.source}`, `public/assets/fe/${theme}/${currentKeyNode.target}`).options({processCssUrls: false});
                break;
            case "copy":
                mix.copyDirectory(`resources/assets/fe/${theme}/${currentKeyNode.source}`, `public/assets/fe/${theme}/${currentKeyNode.target}`);
                break;

        }
    }

}

if(process.env.NODE_ENV == "development") {
    //only for dev
    mix.copyDirectory('public/assets', '../../../public/assets/hashtagcms');
}

