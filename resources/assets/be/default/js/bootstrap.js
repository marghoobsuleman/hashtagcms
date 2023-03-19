
try {
    //require('bootstrap-sass');
} catch (e) {}

import AdminConfig from '../../common-old/js/helpers/AdminConfig';
import Velocity from '../../common-old/js/vendors/velocity.min';
window.pluralize = require('pluralize');
window._ = require('lodash'); //used in passport
import {Storage, Fetcher} from '../../common-old/js/helpers/Common';
window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.AdminConfig = new AdminConfig();
window.Velocity = Velocity;

window.log = console.log;
window.Store = new Storage();
window.Fetcher = new Fetcher();

/**
 * Next we will register the CSRF Token as a common-old header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive Api for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });
