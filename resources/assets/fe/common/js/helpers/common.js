export class AppConfig {
    constructor(data) {
        this.configData = data;
    }

    setConfigData(data) {
        this.configData = data;
    }

    getValue(key, defaultVal) {
        return this.configData[key] || defaultVal;
    }

    getMedia(path) {
        let media = this.getValue("media");
        return media.http_path+"/"+path;
    }
};

//create a function to call an api using ajax
