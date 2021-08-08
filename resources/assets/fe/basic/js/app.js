//Axios
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Analytics from "../../../js/utils/Analytics";
import Subscribe from "../../../js/components/Subscribe";

window.HashtagCms = {};
window.HashtagCms.Subscribe = new Subscribe();
window.HashtagCms.Analytics = new Analytics();

