require('./jquery.min');
require('./bootstrap.min');

let vue = require('vue');

window.Vue = vue;
Vue.config.devtools = true;

//Axios
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';



import Subscribe from "./components/Subscribe";

import AlertMessage from "../../../js/components/AlertMessage";
import Comments from "../../../js/components/Comments";
import CommentsList from "../../../js/components/CommentsList";


Vue.component('subscribe-panel', Subscribe);
Vue.component('alert-message', AlertMessage);
Vue.component('comment-box', Comments);
Vue.component('comments-list', CommentsList);


const appVm = new Vue({
    el: '#app'
});

/** do not remove this **/
new Vue({
    el: '#__hashtagcms__'
});
