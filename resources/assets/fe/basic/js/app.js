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


import Analytics from "../../common/js/utils/analytics";
import Subscribe from "../../common/js/components/subscribe";

window.HashtagCms = {};
window.HashtagCms.Subscribe = new Subscribe();
window.HashtagCms.Analytics = new Analytics();

