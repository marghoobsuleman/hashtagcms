/**
 * Created by marghoob.suleman on 8/23/17.
 */

const CLIPBOARD_KEY = "htcms_cps";


import {Fx} from "./fx";
import {EventBus} from "./event-bus";

/**
 * Toast
 * Dependecy: library/toastBox.vue
 *
 */
export class Toast {

    static show(vm, message="", timeout=1000, position) {
        vm.$root.$refs.globalToaster.show(message, timeout, position);
    }
    static hide(vm) {
        vm.$root.$refs.globalToaster.hide();
    }
}

/**
 * Loader
 * Dependecy: library/loader.vue
 *
 */
export class Loader {

    static show(vm, message=null, position=null) {
        vm.$root.$refs.globalLoader.show(message, position);
    }
    static hide(vm) {
        vm.$root.$refs.globalLoader.hide();
    }
}



/**
 * Modal
 * Dependency: library/modalBox.vue
 *
 */
export class Modal {

    static show(vm, message="", position, callback, timeout) {
        vm.$root.$refs.globalModalBox.show(message, position, callback, timeout);
    }
    static hide(vm) {
        vm.$root.$refs.globalModalBox.hide();
    }
    static open(vm=null, modalRef=null, callback=null) {
        if(vm != null && modalRef != null) {
            vm.$refs[modalRef].open(callback);
        }
    }
    static close(vm=null, modalRef=null) {
        if(vm != null && modalRef != null) {
            vm.$refs[modalRef].hide();
        }
    }

}


/**
 * queryBuilder
 * Get Param from query
 *
 */
export class queryBuilder {

    static get cache() {
        return {};
    };

    static get(param, custom) {

        if (this.cache[param]) {
            return this.cache[param];
        }
        let query = (typeof (custom) == "undefined") ? window.location.search.substring(1) : custom;
        let query_arr = query.split("&");
        let all = {};
        for (let i = 0; i < query_arr.length; i++) {

            let current = query_arr[i].split("=");
            let key = current[0];
            let value = current[1];
            if(current.length>2) {
                current.shift();
                value = current.join("=");
            }
            all[key] = value;

            if (key === param) {
                let val = decodeURIComponent(value);
                this.cache[param] = val;
                return val;
            }
        }
        return (param==null) ? all : "";
    }
    static all(custom) {
        return this.get(null, custom);
    }

}

/**
 * Shared class
 */
export class Storage {

    constructor() {
       this.obj = {};
       this.counter = 1;
    };

    nextCounter() {
        return this.counter++;
    }

    store(name, value) {
        this.obj[name] = value;
    }

    fetch(name) {
        return this.obj[name];
    }

    clear(name) {
        return delete this.obj[name];
    }

}

export class Utils {

    serializeFormArray(form) {
        form = (typeof form == 'string') ? document.getElementById(form) : form;
        let field, l, s = [];
        if (typeof form == 'object' && form.nodeName === "FORM") {
            let len = form.elements.length;
            for (let i=0; i<len; i++) {
                field = form.elements[i];
                if (field.name && !field.disabled && field.type !== 'file' && field.type !== 'reset' && field.type !== 'submit' && field.type !== 'button') {
                    if (field.type === 'select-multiple') {
                        l = form.elements[i].options.length;
                        for (j=0; j<l; j++) {
                            if(field.options[j].selected)
                                s[s.length] = { name: field.name, value: field.options[j].value };
                        }
                    } else if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
                        s[s.length] = { name: field.name, value: field.value };
                    }
                }
            }
        }
        return s;
    }
}

export class Fetcher {

    get(url) {
        return axios.get(url);
    }
}


export class LeftMenu {

    constructor() {
        this.visible = true;
        this.oldWidth = null;
    }
    static init() {

        let leftElem = document.querySelector(".js_left_panel");
        let display = leftElem.style.display;

        if(display === "" || display === "inline-block") {
            EventBus.emit('left-menu-on-show');
            this.visible = true;
        } else {
            EventBus.emit('left-menu-on-hide');
            this.visible = false;
        }
    }
    static isVisible() {
        return this.visible;
    }
    static toggleShow(show) {

        let css = ["hidden-md", "hidden-xs"];
        let leftElem = document.querySelector(".js_left_panel");
        let rightElem = document.querySelector(".js_right_panel");

        let display = leftElem.style.display;
        let width = leftElem.clientWidth;

        //if it's visible then hide it.
        if((display === "" || display === "inline-block") && width !== 0) {
            this.visible = false;
            leftElem.style.display = "none";
            EventBus.emit('left-menu-on-hide');

        } else {
            this.visible = true;
            leftElem.style.display = "";
            EventBus.emit('left-menu-on-show');
        }

    }
}

function TitleCase(value) {
    value = value.replace(/\.|_/g, " ");
    return value.charAt(0).toUpperCase() + value.slice(1);
}


/**
 * Copy to clipboard
 * @param text
 * @constructor
 */
export function CopyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text);
    } else {
        window.localStorage.setItem(CLIPBOARD_KEY, text);
        const el = document.createElement('textarea');
        el.value = text;
        el.style.position = 'absolute';
        el.style.left = '-99999px';
        el.style.top = '-99999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    }
}

/**
 * Paste from clipboard
 * @returns {Promise<unknown>}
 * @constructor
 */
export async function PasteFromClipboard() {
    try {
        const permission = await navigator.permissions.query({ name: 'clipboard-read' });
        if (permission.state === 'denied') {
            throw new Error('Not allowed to read clipboard.');
        }
        return navigator.clipboard.read();
    }
    catch (error) {
        return new Promise((resolve, reject) => {
            if (window.localStorage.getItem(CLIPBOARD_KEY)) {
                let data = window.localStorage.getItem(CLIPBOARD_KEY);
                resolve(data);
            } else {
                reject(null);
            }

        });
    }
}

/**
 * Check if string is a json
 * @param str
 * @returns {boolean}
 * @constructor
 */
export function IsJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
