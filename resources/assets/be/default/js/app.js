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

import TopNav from '../../common/js/components/TopNav';
import LeftNav from '../../common/js/components/LeftNav';
import TabularView from '../../common/js/components/TabularView';
import SearchBar from '../../common/js/components/SearchBar';
import ActionBar from '../../common/js/components/ActionBar';
import MenuSorter from '../../common/js/components/MenuSorter';
import TimerButton from '../../common/js/library/TimerButton';
import ModalBox from '../../common/js/library/ModalBox';
import ToastBox from '../../common/js/library/ToastBox';

//import SplitButton from '../../common/js/library/SplitButton';
import Pagination from '../../common/js/components/Pagination';
import ModuleCreator from '../../common/js/components/ModuleCreator';
import ModulePermission from '../../common/js/components/ModulePermission';
import FrontEndModuleCreator from '../../common/js/components/FrontEndModuleCreator';
import CmsModuleDropdown from '../../common/js/components/CmsModuleDropdown';
import InfoBox from '../../common/js/library/InfoBox';
import InfoxBoxes from '../../common/js/components/InfoxBoxes';
import SiteWiseData from '../../common/js/components/SiteWiseData';
import SiteWiseCopier from '../../common/js/components/SiteWiseCopier';
import LeftMenuShowHide from '../../common/js/library/LeftMenuShowHide';
import CategoryPlatform from '../../common/js/components/CategoryPlatform';
import GloablSiteButton from '../../common/js/components/GloablSiteButton';

import Homepage from '../../common/js/components/Homepage';
import CategorySettings from '../../common/js/components/CategorySettings';
import Loader from '../../common/js/library/Loader';
import CopyPaste from '../../common/js/library/CopyPaste';

import SiteButton from '../../common/js/components/SiteButton';
import PlatformButton from '../../common/js/components/PlatformButton';

import PassportClient from '../../common/js/components/passport/Clients';
import PassportAuthorizedClients from '../../common/js/components/passport/AuthorizedClients';
import PassportPersonalAccessTokens from '../../common/js/components/passport/PersonalAccessTokens';

import LanguageCopier from "../../common/js/components/LanguageCopier";
import SiteCloner from "../../common/js/components/SiteCloner";


Vue.component('top-nav', TopNav);
Vue.component('admin-modules', LeftNav);
Vue.component('table-view', TabularView);
Vue.component('search-bar', SearchBar);
Vue.component('action-bar', ActionBar);

Vue.component('menu-sorter', MenuSorter);

Vue.component('timer-button', TimerButton);
Vue.component('modal-box', ModalBox);
Vue.component('toast-box', ToastBox);

//Vue.component('split-button', require('../../common/js/library/SplitButton); //@todo: temp

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
