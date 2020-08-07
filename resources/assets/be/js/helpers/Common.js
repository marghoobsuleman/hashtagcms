/**
 * Created by marghoob.suleman on 8/23/17.
 */

import {FX} from "./Fx";
import {EventBus} from "./event-bus";

/**
 * Toast
 * Dependecy: library/ToastBox.vue
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
 * Dependecy: library/Loader.vue
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
 * Dependecy: library/ModalBox.vue
 *
 */
export class Modal {

    static show(vm, message="", position, callback, timeout) {
        vm.$root.$refs.globalModalBox.show(message, position, callback, timeout);
    }
    static hide(vm) {
        vm.$root.$refs.globalModalBox.hide();
    }
    static open(vm=null, modelref=null, callback=null) {
        if(vm != null && modelref != null) {
            vm.$refs[modelref].open(callback);
        }
    }
    static close(vm=null, modelref=null) {
        if(vm != null && modelref != null) {
            vm.$refs[modelref].hide();
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
        };
        var query = (typeof (custom) == "undefined") ? window.location.search.substring(1) : custom;
        var query_arr = query.split("&");
        var all = {};
        for (var i = 0; i < query_arr.length; i++) {

            var current = query_arr[i].split("=");
            var key = current[0];
            var value = current[1];
            if(current.length>2) {
                current.shift();
                value = current.join("=");
            };
            all[key] = value;

            if (key == param) {
                var val = decodeURIComponent(value);
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
        var field, l, s = [];
        if (typeof form == 'object' && form.nodeName == "FORM") {
            var len = form.elements.length;
            for (var i=0; i<len; i++) {
                field = form.elements[i];
                if (field.name && !field.disabled && field.type != 'file' && field.type != 'reset' && field.type != 'submit' && field.type != 'button') {
                    if (field.type == 'select-multiple') {
                        l = form.elements[i].options.length;
                        for (j=0; j<l; j++) {
                            if(field.options[j].selected)
                                s[s.length] = { name: field.name, value: field.options[j].value };
                        }
                    } else if ((field.type != 'checkbox' && field.type != 'radio') || field.checked) {
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

        let leftElem = document.getElementById("mainLeftContent");
        let display = leftElem.style.display;

        if(display == "" || display == "inline-block") {
            EventBus.$emit('left-menu-on-show');
            this.visible = true;
        } else {
            EventBus.$emit('left-menu-on-hide');
            this.visible = false;
        }
    }
    static isVisible() {
        return this.visible;
    }
    static toggleShow(show) {

        let css = ["hidden-md", "hidden-xs"];
        let leftElem = document.getElementById("mainLeftContent");
        let rightElem = document.getElementById("mainRightContent");

        let display = leftElem.style.display;
        let width = leftElem.clientWidth;

        //if it's visible then hide it.
        if((display == "" || display == "inline-block") && width !== 0) {
            this.visible = false;
            this.oldWidth = leftElem.style.width;
            FX.slideLeft("mainLeftContent", function () {
                leftElem.classList.remove(...css);
                //set right side 100%
                rightElem.classList.remove("col-md-10");
                rightElem.classList.add("col-md-12");
            });
            EventBus.$emit('left-menu-on-hide');

        } else {
            this.visible = true;
            rightElem.classList.remove("col-md-12");

            //back to normal size of right side
            rightElem.classList.add("col-md-10");

            leftElem.style.display = "inline-block";
            //leftElem.style.width = "16.66666667%";
            leftElem.classList.remove(...css);
            if(this.oldWidth != null) {
                leftElem.style.width = this.oldWidth;
            }
            EventBus.$emit('left-menu-on-show');
        }

    }
}


