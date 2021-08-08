require('./bootstrap');
window.Vue = require('vue').default;

import SiteInstaller from './installer/Installer';
Vue.component('site-installer', SiteInstaller);


const app = new Vue({
    el: '#app'
});
