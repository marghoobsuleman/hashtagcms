/**
 * Created by marghoob.suleman on 8/23/17.
 */

export default class AdminConfig {
    constructor() {
        this.appConfig = (window.Laravel && window.Laravel.adminConfig) || {};
    }

    get(key, defaultVal) {
        return this.appConfig[key] || defaultVal;
    }

    admin_path(path, params=null) {
        let qParamStr = "";
        if(params !== null && Object.prototype.toString.call(params) === "[object Object]") {
            let qParam = [];
            Object.entries(params).forEach(([key, value]) => qParam.push(key+"="+value));
            qParamStr = "?"+qParam.join("&");
        }
        return this.get("base_path")+"/"+path+qParamStr;
    }

    admin_asset(path) {
        return this.get("app_url")+"/"+this.get("assets_path")+"/"+path;
    }
};


