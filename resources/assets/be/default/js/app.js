/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
let vue = require('vue').default;

window.Vue = vue;

Vue.config.devtools = false;
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.filter('humanize', function(value) {
    if (!value) return '';
    value = value.toString().replace(/_/g, " ");
    return value.charAt(0).toUpperCase() + value.slice(1);
});

import TopNav from '../../common-old/js/components/TopNav.vue';
import LeftNav from '../../common-old/js/components/LeftNav.vue';
import TabularView from '../../common-old/js/components/TabularView.vue';
import SearchBar from '../../common-old/js/components/SearchBar.vue';
import ActionBar from '../../common-old/js/components/ActionBar.vue';
import MenuSorter from '../../common-old/js/components/MenuSorter.vue';
import TimerButton from '../../common-old/js/library/TimerButton.vue';
import ModalBox from '../../common-old/js/library/ModalBox.vue';
import ToastBox from '../../common-old/js/library/ToastBox.vue';

//import SplitButton from '../../common-old/js/library/splitButton.vue';
import Pagination from '../../common-old/js/components/Pagination.vue';
import ModuleCreator from '../../common-old/js/components/ModuleCreator.vue';
import ModulePermission from '../../common-old/js/components/ModulePermission.vue';
import FrontEndModuleCreator from '../../common-old/js/components/FrontEndModuleCreator.vue';
import CmsModuleDropdown from '../../common-old/js/components/CmsModuleDropdown.vue';
import InfoBox from '../../common-old/js/library/InfoBox.vue';
import InfoxBoxes from '../../common-old/js/components/InfoxBoxes.vue';
import SiteWiseData from '../../common-old/js/components/SiteWiseData.vue';
import SiteWiseCopier from '../../common-old/js/components/SiteWiseCopier.vue';
import LeftMenuShowHide from '../../common-old/js/library/LeftMenuShowHide.vue';
import CategoryPlatform from '../../common-old/js/components/CategoryPlatform.vue';
import GloablSiteButton from '../../common-old/js/components/GloablSiteButton.vue';

import Homepage from '../../common-old/js/components/Homepage.vue';
import CategorySettings from '../../common-old/js/components/CategorySettings.vue';
import Loader from '../../common-old/js/library/Loader.vue';
import CopyPaste from '../../common-old/js/library/CopyPaste.vue';

import SiteButton from '../../common-old/js/components/SiteButton.vue';
import PlatformButton from '../../common-old/js/components/PlatformButton.vue';

import PassportClient from '../../common-old/js/components/passport/Clients.vue';
import PassportAuthorizedClients from '../../common-old/js/components/passport/AuthorizedClients.vue';
import PassportPersonalAccessTokens from '../../common-old/js/components/passport/PersonalAccessTokens.vue';

import LanguageCopier from "../../common-old/js/components/LanguageCopier.vue";
import SiteCloner from "../../common-old/js/components/SiteCloner.vue";


Vue.component('top-nav', TopNav);
Vue.component('admin-modules', LeftNav);
Vue.component('table-view', TabularView);
Vue.component('search-bar', SearchBar);
Vue.component('action-bar', ActionBar);

Vue.component('menu-sorter', MenuSorter);

Vue.component('timer-button', TimerButton);
Vue.component('modal-box', ModalBox);
Vue.component('toast-box', ToastBox);

//Vue.component('split-button', require('../../common-old/js/library/SplitButton); //@todo: temp

Vue.component('Pagination', Pagination);
Vue.component('module-creator', ModuleCreator);
Vue.component('module-permission', ModulePermission);
Vue.component('front-module-creator', FrontEndModuleCreator);

Vue.component('cms-module-dropdown', CmsModuleDropdown);

Vue.component('info-box', InfoBox);
Vue.component('info-boxes', InfoxBoxes);

Vue.component('site-wise', SiteWiseData);
Vue.component('site-wise-copier', SiteWiseCopier);
Vue.component('left-menu-toggle', LeftMenuShowHide);
Vue.component('category-platform', CategoryPlatform);
Vue.component('global-site-button', GloablSiteButton);
Vue.component('admin-homepage', Homepage);
Vue.component('admin-category-settings', CategorySettings);
Vue.component('admin-loader', Loader);
Vue.component('copy-paste', CopyPaste);
Vue.component('site-button', SiteButton);
Vue.component('platform-button', PlatformButton);


Vue.component('passport-clients', PassportClient);
Vue.component('passport-authorized-clients', PassportAuthorizedClients);
Vue.component('passport-personal-access-tokens',PassportPersonalAccessTokens);

Vue.component('language-copier', LanguageCopier);
Vue.component('site-cloner', SiteCloner);


const appVm = new Vue({
    el: '#app'
});
