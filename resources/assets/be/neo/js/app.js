/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
//require('./bootstrap');
//import './bootstrap?raw';
import AdminConfig from '../../common-vue3/js/helpers/AdminConfig';
window.AdminConfig = new AdminConfig();

import {Storage, Fetcher} from '../../common-vue3/js/helpers/Common';
window.log = console.log;
window.Store = new Storage();
window.Fetcher = new Fetcher();


import { createApp } from 'vue';

//window.Vue = vue;

//Vue.config.devtools = false;
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


import TopNav from '../../common-vue3/js/components/TopNav.vue';
import LeftNav from '../../common-vue3/js/components/LeftNav.vue';
import InfoBox from '../../common-vue3/js/library/InfoBox.vue';
import InfoBoxes from '../../common-vue3/js/components/InfoxBoxes.vue';
import TabularView from '../../common-vue3/js/components/TabularView.vue';
import SearchBar from '../../common-vue3/js/components/SearchBar.vue';
import ActionBar from '../../common-vue3/js/components/ActionBar.vue';
import Pagination from '../../common-vue3/js/components/Pagination.vue';
// import MenuSorter from '../../common-vue3/js/components/MenuSorter';
// import TimerButton from '../../common-vue3/js/library/TimerButton';
// import ModalBox from '../../common-vue3/js/library/ModalBox';
// import ToastBox from '../../common-vue3/js/library/ToastBox';

// import ModuleCreator from '../../common-vue3/js/components/ModuleCreator';
// import ModulePermission from '../../common-vue3/js/components/ModulePermission';
// import FrontEndModuleCreator from '../../common-vue3/js/components/FrontEndModuleCreator';
// import CmsModuleDropdown from '../../common-vue3/js/components/CmsModuleDropdown';
// import InfoBox from '../../common-vue3/js/library/InfoBox';
// import InfoxBoxes from '../../common-vue3/js/components/InfoxBoxes';
// import SiteWiseData from '../../common-vue3/js/components/SiteWiseData';
// import SiteWiseCopier from '../../common-vue3/js/components/SiteWiseCopier';
// import LeftMenuShowHide from '../../common-vue3/js/library/LeftMenuShowHide';
// import CategoryPlatform from '../../common-vue3/js/components/CategoryPlatform';
// import GloablSiteButton from '../../common-vue3/js/components/GloablSiteButton';
// import Homepage from '../../common-vue3/js/components/Homepage';
// import CategorySettings from '../../common-vue3/js/components/CategorySettings';
// import Loader from '../../common-vue3/js/library/Loader';
// import CopyPaste from '../../common-vue3/js/library/CopyPaste';
// import SiteButton from '../../common-vue3/js/components/SiteButton';
// import PlatformButton from '../../common-vue3/js/components/PlatformButton';
// import PassportClient from '../../common-vue3/js/components/passport/Clients';
// import PassportAuthorizedClients from '../../common-vue3/js/components/passport/AuthorizedClients';
// import PassportPersonalAccessTokens from '../../common-vue3/js/components/passport/PersonalAccessTokens';
// import LanguageCopier from "../../common-vue3/js/components/LanguageCopier";
// import SiteCloner from "../../common-vue3/js/components/SiteCloner";


    //# Vue.component('top-nav', TopNav);
    // Vue.component('admin-modules', LeftNav);
    // Vue.component('table-view', TabularView);
    // Vue.component('search-bar', SearchBar);
    // Vue.component('action-bar', ActionBar);
//
// Vue.component('menu-sorter', MenuSorter);
//
// Vue.component('timer-button', TimerButton);
// Vue.component('modal-box', ModalBox);
// Vue.component('toast-box', ToastBox);
//
// //Vue.component('split-button', require('../../common-vue3/js/library/SplitButton); //@todo: temp
//
    // Vue.component('Pagination', Pagination);
// Vue.component('module-creator', ModuleCreator);
// Vue.component('module-permission', ModulePermission);
// Vue.component('front-module-creator', FrontEndModuleCreator);
//
// Vue.component('cms-module-dropdown', CmsModuleDropdown);
//
// Vue.component('info-box', InfoBox);
// Vue.component('info-boxes', InfoxBoxes);
//
// Vue.component('site-wise', SiteWiseData);
// Vue.component('site-wise-copier', SiteWiseCopier);
// Vue.component('left-menu-toggle', LeftMenuShowHide);
// Vue.component('category-platform', CategoryPlatform);
// Vue.component('global-site-button', GloablSiteButton);
// Vue.component('admin-homepage', Homepage);
// Vue.component('admin-category-settings', CategorySettings);
// Vue.component('admin-loader', Loader);
// Vue.component('copy-paste', CopyPaste);
// Vue.component('site-button', SiteButton);
// Vue.component('platform-button', PlatformButton);
//
//
// Vue.component('passport-clients', PassportClient);
// Vue.component('passport-authorized-clients', PassportAuthorizedClients);
// Vue.component('passport-personal-access-tokens',PassportPersonalAccessTokens);
//
// Vue.component('language-copier', LanguageCopier);
// Vue.component('site-cloner', SiteCloner);
// Vue.component('Pagination', Pagination);
createApp({
    components: {
        'top-nav':TopNav,
        'admin-modules':LeftNav,
        'info-box':InfoBox,
        'info-boxes':InfoBoxes,
        'table-view':TabularView,
        'search-bar':SearchBar,
        'action-bar':ActionBar,
        'Pagination':Pagination

    }
}).mount('#app');

console.log("I am from app.js");