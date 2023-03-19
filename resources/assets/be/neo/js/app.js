import pluralize0 from "pluralize";
window.pluralize = pluralize0;

/** Axios setup **/
import axios from "axios";
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    axios.defaults.headers.common['Authorization'] = 'Bearer '+token.content;
    axios.defaults.withCredentials = true;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
window.axios = axios;


import AdminConfig from '../../common/js/helpers/adminConfig';
window.AdminConfig = new AdminConfig();

import {Toast} from "../../common/js/helpers/common";
window.ToastGloabl = Toast;

//Some Common Lib
import {Storage, Fetcher} from '../../common/js/helpers/common';


window.log = console.log;
window.Store = new Storage();
window.Fetcher = new Fetcher();


import { createApp } from 'vue';
import TopNav from '../../common/js/components/topNav.vue';
import LeftNav from '../../common/js/components/leftNav.vue';
import InfoBox from '../../common/js/library/infoBox.vue';
import InfoBoxes from '../../common/js/components/infoxBoxes.vue';
import TabularView from '../../common/js/components/tabularView.vue';
import SearchBar from '../../common/js/components/searchBar.vue';
import ActionBar from '../../common/js/components/actionBar.vue';
import PaginationView from '../../common/js/components/pagination.vue';
import ToastBox from '../../common/js/library/toastBox.vue';
import Loader from '../../common/js/library/loader.vue';
import ModalBox from '../../common/js/library/modalBox.vue';
import ModulePermission from '../../common/js/components/modulePermission.vue';
import MenuSorter from '../../common/js/components/menuSorter.vue';
import SiteWiseData from '../../common/js/components/siteWiseData.vue';
import SiteWiseCopier from '../../common/js/components/siteWiseCopier.vue';
import TitleBar from "../../common/js/components/titleBar.vue";
import LanguageCopier from "../../common/js/components/languageCopier.vue";
import ModuleCreator from '../../common/js/components/moduleCreator.vue'; //will work on it
import FrontEndModuleCreator from '../../common/js/components/frontEndModuleCreator.vue';
import CopyPaste from '../../common/js/library/copyPaste.vue';
import SiteCloner from "../../common/js/components/siteCloner.vue";
import TimerButton from '../../common/js/library/timerButton.vue';
import CategoryPlatform from '../../common/js/components/categoryPlatform.vue';
import CategorySettings from '../../common/js/components/categorySettings.vue';
import LeftMenuShowHide from '../../common/js/library/leftMenuShowHide.vue';
import Homepage from '../../common/js/components/homepage.vue';
import ImageGallery from "../../common/js/components/imageGallery.vue";

const app = createApp({
    components: {
        'top-nav':TopNav,
        'title-bar':TitleBar,
        'admin-modules':LeftNav,
        'info-box':InfoBox,
        'info-boxes':InfoBoxes,
        'table-view':TabularView,
        'search-bar':SearchBar,
        'action-bar':ActionBar,
        'pagination-view':PaginationView,
        'toast-box': ToastBox,
        'admin-loader': Loader,
        'modal-box': ModalBox,
        'module-permission': ModulePermission,
        'menu-sorter': MenuSorter,
        'site-wise': SiteWiseData,
        'site-wise-copier': SiteWiseCopier,
        'site-cloner': SiteCloner,
        'language-copier':LanguageCopier,
        'module-creator': ModuleCreator,
        'front-module-creator': FrontEndModuleCreator,
        'copy-paste': CopyPaste,
        'timer-button': TimerButton,
        'category-platform': CategoryPlatform,
        'category-settings': CategorySettings,
        'left-menu-toggle': LeftMenuShowHide,
        'page-manager': Homepage,
        'image-gallery': ImageGallery

    }
}).mount('#app');

window.Vue = app;

