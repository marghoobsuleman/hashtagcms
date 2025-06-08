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


import AdminConfig from '../../common/js/helpers/admin-config';
window.AdminConfig = new AdminConfig();

import {Toast} from "../../common/js/helpers/common";
window.ToastGloabl = Toast;

//Some Common Lib
import {Storage, Fetcher} from '../../common/js/helpers/common';


window.log = console.log;
window.Store = new Storage();
window.Fetcher = new Fetcher();


import { createApp } from 'vue';
import TopNav from '../../common/js/components/top-nav.vue';
import LeftNav from '../../common/js/components/left-nav.vue';
import InfoBox from '../../common/js/library/info-box.vue';
import InfoBoxes from '../../common/js/components/info-boxes.vue';
import TabularView from '../../common/js/components/tabular-view.vue';
import SearchBar from '../../common/js/components/search-bar.vue';
import ActionBar from '../../common/js/components/action-bar.vue';
import PaginationView from '../../common/js/components/pagination.vue';
import ToastBox from '../../common/js/library/toast-box.vue';
import Loader from '../../common/js/library/loader.vue';
import ModalBox from '../../common/js/library/modal-box.vue';
import ModulePermission from '../../common/js/components/module-permission.vue';
import MenuSorter from '../../common/js/components/menu-sorter.vue';
import SiteWiseData from '../../common/js/components/sitewise-data.vue';
import SiteWiseCopier from '../../common/js/components/sitewise-copier.vue';
import TitleBar from "../../common/js/components/title-bar.vue";
import LanguageCopier from "../../common/js/components/language-copier.vue";
import ModuleCreator from '../../common/js/components/module-creator.vue'; //will work on it
import FrontEndModuleCreator from '../../common/js/components/frontend-module-creator.vue';
import CopyPaste from '../../common/js/library/copy-paste.vue';
import SiteCloner from "../../common/js/components/site-cloner.vue";
import TimerButton from '../../common/js/library/timer-button.vue';
import CategoryPlatform from '../../common/js/components/category-platform.vue';
import CategorySettings from '../../common/js/components/category-settings.vue';
import LeftMenuShowHide from '../../common/js/library/left-menu-show-hide.vue';
import Homepage from '../../common/js/components/homepage.vue';
import ImageGallery from "../../common/js/components/image-gallery.vue";

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

