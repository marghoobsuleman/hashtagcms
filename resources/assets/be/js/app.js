/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
let vue = require('vue').default;

window.Vue = vue;

Vue.config.devtools = true;
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

import TopNav from './components/TopNav';
import LeftNav from './components/LeftNav';
import TabularView from './components/TabularView';
import SearchBar from './components/SearchBar';
import ActionBar from './components/ActionBar';
import MenuSorter from './components/MenuSorter';
import TimerButton from './library/TimerButton';
import ModalBox from './library/ModalBox';
import ToastBox from './library/ToastBox';

//import SplitButton from './library/SplitButton';
import Pagination from './components/Pagination';
import ModuleCreator from './components/ModuleCreator';
import ModulePermission from './components/ModulePermission';
import FrontEndModuleCreator from './components/FrontEndModuleCreator';
import CmsModuleDropdown from './components/CmsModuleDropdown';
import InfoBox from './library/InfoBox';
import InfoxBoxes from './components/InfoxBoxes';
import SiteWiseData from './components/SiteWiseData';
import SiteWiseCopier from './components/SiteWiseCopier';
import LeftMenuShowHide from './library/LeftMenuShowHide';
import CategoryTenant from './components/CategoryTenant';
import GloablSiteButton from './components/GloablSiteButton';

import Homepage from './components/Homepage';
import CategorySettings from './components/CategorySettings';
import Loader from './library/Loader';
import CopyPaste from './library/CopyPaste';

import SiteButton from './components/SiteButton';
import TenantButton from './components/TenantButton';

import PassportClient from './components/passport/Clients';
import PassportAuthorizedClients from './components/passport/AuthorizedClients';
import PassportPersonalAccessTokens from './components/passport/PersonalAccessTokens';

import LanguageCopier from "./components/LanguageCopier";

Vue.component('top-nav', TopNav);
Vue.component('admin-modules', LeftNav);
Vue.component('table-view', TabularView);
Vue.component('search-bar', SearchBar);
Vue.component('action-bar', ActionBar);

Vue.component('menu-sorter', MenuSorter);

Vue.component('timer-button', TimerButton);
Vue.component('modal-box', ModalBox);
Vue.component('toast-box', ToastBox);

//Vue.component('split-button', require('./library/SplitButton); //@todo: temp

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
Vue.component('category-tenant', CategoryTenant);
Vue.component('global-site-button', GloablSiteButton);
Vue.component('admin-homepage', Homepage);
Vue.component('admin-category-settings', CategorySettings);
Vue.component('admin-loader', Loader);
Vue.component('copy-paste', CopyPaste);
Vue.component('site-button', SiteButton);
Vue.component('tenant-button', TenantButton);


Vue.component('passport-clients', PassportClient);
Vue.component('passport-authorized-clients', PassportAuthorizedClients);
Vue.component('passport-personal-access-tokens',PassportPersonalAccessTokens);

Vue.component('language-copier', LanguageCopier);


const appVm = new Vue({
    el: '#app'
});
