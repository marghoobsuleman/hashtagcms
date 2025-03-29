export default class Analytics {

    constructor() {

    }

    init(data) {
        this.readCounter(data);
    }
    readCounter(data) {

        this.submit("post", "/analytics/publish", data);
    }

    /**
     * Submit request.
     *
     * @param {string} requestType
     * @param {string} url
     * @param data
     */
    submit(requestType, url, data) {
        /*data = new Blob([JSON.stringify(data)], {type : 'application/json'});
        data.csrfToken = window.Laravel.csrfToken;
        console.log("data",data);
        navigator.sendBeacon(url, data);*/
        return new Promise((resolve, reject) => {
            axios[requestType](url, data)
                .then(response => {
                    resolve(response.data);
                }).catch(error => {
                    reject(error.response.data);
            });
        });
    }

    trackEventView (category, action ,value, cb) {
        try {
            //very very old ga
            _gaq.push(['_trackEvent', category, action, value]);
        } catch(e) {
        }
        if ( typeof ga != "undefined") {
            try {
                ga('send', {
                    hitType : 'event',
                    eventCategory : category,
                    eventAction : action,
                    eventLabel : value
                });
            } catch(e) {
            }
        }
        if (cb) {
            cb.apply(this, arguments);
        }
    }

    /**
     * Track Page view
     * @param value
     * @param cb
     */
    trackPageView (value, cb) {
        try {
            //Very very old ga
            _gaq.push(['_trackPageview', value]);
        } catch(e) {
        }

        if ( typeof ga != "undefined") {
            try {
                ga('send',
                    {hitType: 'pageview',
                        page: value
                    });
            } catch(e) {}
        }

        if (cb) {
            cb.apply(this, arguments);
        }
    }

}
