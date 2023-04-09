<template>
    <div class="clearfix">
        <template v-if="!isZeroResult()">
            <div v-if="layoutType === 'grid'" class="row">
                <div v-for="row in rows" :id="'row_'+row.id" :key="row.key" class="col">
                    <table class="table border shadow-sm mt-2 table-grid">
                        <tr v-for="(fields, index) in headings" :class="getCssForRow(fields, index)">
                            <td :class="'header head_'+getFieldName(fields)">
                                {{ filterFieldsName(getFieldName(fields)) }}
                            </td>
                            <td>
                                <span v-if="!isActionFieldKey(getFieldName(fields, 'key'))"
                                      v-html="getFieldValue(row, getFieldName(fields, 'key'), fields)"></span>
                                <div v-if="isActionFieldKey(getFieldName(fields, 'key'))" class="actions"
                                     v-html="getActionValue(row)"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <table v-if="layoutType === 'table'" class="table border shadow-sm mt-2 table-grid">
                <thead>
                <tr>
                    <th v-for="fields in headings" :class="'header head_'+filterSnakeCase(getFieldName(fields))">
                        {{ filterFieldsName(getFieldName(fields)) }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in rows" :id="'row_'+row.id" :key="row.key" class="list-table-row">
                    <td v-for="fields in headings">
                        <div v-if="isActionFieldKey(getFieldName(fields, 'key'))" class="actions"
                             v-html="getActionValue(row)">
                        </div>
                        <span v-else v-html="getFieldValue(row, getFieldName(fields, 'key'), fields)"></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </template>
        <delete-popup ref="deleteBox" :data-show-footer="true" :data-show-modal="false" @on-close="deleteNow">
            <template v-slot:title>Alert</template>
            <template v-slot:content>
                <span class="text-danger">{{ alertMessage.current }}</span>
            </template>

            <template v-slot:footer>
                <div class="row">
                    <div class="col-auto">
                        <div class="form-check pt-2">
                            <input id="flexCheckDefault" v-model="preventDeleteBox" class="form-check-input" type="checkbox"
                                   value="">
                            <label class="form-check-label" for="flexCheckDefault">
                                Don't ask again
                            </label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button v-show="isDelete" class="btn btn-default" data-dismiss="modal" type="button"
                                @click="deleteNow(0)">No
                        </button>
                        <button v-show="isDelete" class="btn btn-primary" type="button" @click="deleteNow(1)">Yes
                        </button>
                        <button v-show="!isDelete" class="btn btn-primary" type="button" @click="closeDialog()">Okay
                        </button>
                    </div>
                </div>
            </template>
        </delete-popup>
        <info-popup ref="infoPopup"></info-popup>

        <div v-if="isZeroResult()" class="row">
            <div class="col">
                <div v-html="noResultsFoundText"></div>
            </div>
        </div>
    </div>
</template>

<script>

import {Toast, queryBuilder} from '../helpers/common';
import {Fx} from '../helpers/fx';
import {EventBus} from "../helpers/event-bus";
import ModalBox from "../library/modal-box.vue";
import InfoPopup from './Info-popup.vue';

export default {
    components: {
        'info-popup': InfoPopup,
        'delete-popup': ModalBox
    },
    mounted() {
        this.highlight();
        this.bindAction();
        //already uppercase
        if (this.showDeletePopup === "FALSE") {
            this.preventDeleteBox = true;
        }

        EventBus.on('list-view-hide-spinner', ()=> {
            this.showHideSpinner(undefined, "false");
        });

        EventBus.on('info-popup-open', ()=> {
            this.removeAllSpinners();
        });

        //console.log("initing tablular view");

    },
    created() {

        //console.log("created");
        //console.log(this.headings);
        //console.log("this.moreActions ",this.moreActions);
        //console.log("this.actionFields ",this.actionFields);
        //console.log("created tablular view");
    },
    props: [
        'dataHeaders',
        'dataList',
        'dataActionFields',
        'dataMoreActionFields',
        'dataControllerName',
        'dataActionAsAjax',
        'dataActionCss',
        'dataUserRights',
        'dataMakeFieldAsLink',
        'dataShowDeletePopup',
        'dataEditHandledByOtherComponent',
        'dataMinResultsNeeded',
        'dataLayoutType',
        'dataNoResultsFoundText'
    ],

    data() {

        return {
            /*headings:(this.dataHeaders ? JSON.parse(this.dataHeaders) : []),*/
            //rows: (this.dataList ? JSON.parse(this.dataList) : []),
            actionFields: ((this.dataActionFields && this.dataActionFields.toString() !== "null") ? JSON.parse(this.dataActionFields) : []),
            userRights: (this.dataUserRights ? JSON.parse(this.dataUserRights) : []),
            moreActions: ((this.dataMoreActionFields && this.dataMoreActionFields.toString() !== "null") ? JSON.parse(this.dataMoreActionFields) : []),
            actionAjax: ((this.dataActionAsAjax && this.dataActionAsAjax.toString() !== "null") ? JSON.parse(this.dataActionAsAjax) : this.getDefaultValue('action_as_ajax', [])),
            actionIconCss: ((this.dataActionCss && this.dataActionCss.toString() !== "null") ? JSON.parse(this.dataActionCss) : this.getDefaultValue('action_icon_css', [])),
            alertMessage: {
                current: "Are you sure to delete this?",
                delete: "Are you sure to delete this?",
                permission: "Sorry! You don't have permission to delete this."
            },
            isDelete: true,
            currentActionItem: null,
            preventDeleteBox: false,
            makeFieldAsLink: (this.dataMakeFieldAsLink ? JSON.parse(this.dataMakeFieldAsLink) : []),
            fieldAsLinkCache: {},
            showDeletePopup: (this.dataShowDeletePopup ? this.dataShowDeletePopup.toString().toUpperCase() : "FALSE"),
            editHandledByOtherComponent: (typeof this.dataEditHandledByOtherComponent !== "undefined" || (this.dataEditHandledByOtherComponent && this.dataEditHandledByOtherComponent.toString() === "true")),
            minResultsNeeded: (typeof this.dataMinResultsNeeded === "undefined" || this.dataMinResultsNeeded === "") ? -1 : parseInt(this.dataMinResultsNeeded),
            layoutType: (typeof this.dataLayoutType === "undefined" || this.dataLayoutType === "") ? "table" : this.dataLayoutType,
            noResultsFoundText: this.dataNoResultsFoundText,
            spinnerCss:["fa-spinner", "fa-pulse", "fa-fw"]
        }
    },
    computed: {
        headings() {
            let heading = (this.dataHeaders ? JSON.parse(this.dataHeaders) : []);

            let allHeadings = [];
            if (heading.length > 0) {
                heading.forEach(function (current, index) {
                    let label = current.label || current;
                    let key = current.key || current;
                    let isImage = !!(current.isImage && current.isImage.toString() === "true");
                    let showAllScopes = !!(current.showAllScopes && current.showAllScopes.toString() === "true");
                    allHeadings.push({label, key, isImage, showAllScopes});
                })
            }

            // console.log("allHeadings ",allHeadings)
            return allHeadings;
        },
        rows() {
            return (this.dataList ? JSON.parse(this.dataList) : [])
        }
    },
    methods: {
        isActionFieldKey(key) {
            //will have edit/delete/approve etc
            key = (key === undefined) ? "" : key.toLowerCase();
            return (key === 'action' || key === 'moreactions');
        },
        isMakeFieldAsLink(key) {

            //Making Cache for good performance

            if (this.fieldAsLinkCache[key] !== undefined) {
                return this.fieldAsLinkCache[key];
            }

            //console.log("searching... "+key);

            let found = this.makeFieldAsLink.find(function (current, index) {
                if (current.key === key || current.action === key) {
                    return current;
                }
            });
            this.fieldAsLinkCache[key] = (found === undefined) ? -1 : found;
            return this.fieldAsLinkCache[key];

        },

        getFieldName(key, prop, asFilterLabel) {
            prop = (prop) ? prop : 'label';
            return (typeof key == "string") ? key : (key[prop] === undefined) ? key : key[prop];
        },
        getFieldValue(row, key, fields) {
            //console.log("calling ", key, row.id);
            let $this = this;

            //console.log("key", key, fields);

            //data has something like lang.name
            if (key.includes(".")) {

                let extract = key.split(".");
                let scope = extract[0];
                let fieldName = extract[1];

                scope = row[scope];

                switch (Object.prototype.toString.call(scope)) {
                    case "[object Undefined]":
                        scope = "";
                        break;
                    case "[object Array]":
                        scope = (scope.length > 0) ? (fields.showAllScopes) ? getScopeAllVals(scope, fieldName) : scope[0][fieldName] : scope[fieldName];
                        break;
                    case "[object Object]":
                        scope = scope[fieldName] || "";
                        break;
                }

                return scope;
            }

            //get value
            return getActualValue(row, key, fields);

            /**
             * is action
             * @param searchKey
             * @returns {T}
             */
            function isAction(searchKey) {

                return $this.makeFieldAsLink.find(function (current, index) {
                    if (current.key === searchKey) {
                        return current;
                    }
                });

            }

            /**
             * Get all scrope values
             * @param scope
             * @param fieldName
             */
            function getScopeAllVals(scope, fieldName) {
                let values = [];
                if (scope.length > 0 ) {
                    for (let i=0,len=scope.length;i<len;i++) {
                        values.push(scope[i][fieldName]);
                    }
                }
                return values.join(",");
            }

            /**
             * Get actual value
             * @param row
             * @param forKey
             * @param fields
             * @returns {*}
             */
            function getActualValue(row, forKey, fields) {

                let withIcon = true;
                //var actionAlias = {"id":"edit", "publish_status":"publish"};
                let value = "";

                let actionAlias = $this.isMakeFieldAsLink(forKey);

                // check if this is action field
                if (actionAlias !== undefined && actionAlias !== -1) {
                    //console.log(current=="edit" && this.actionFields.indexOf("edit")!=-1);
                    let current = forKey;
                    let actionName = actionAlias.action || current;

                    //console.log("actionName ",actionName, " ", current);
                    let title = actionName.charAt(0).toUpperCase() + actionName.slice(1);
                    //var path = (actionName=="edit") ? `${row.id}/${actionName}` : `${actionName}/${row.id}`;
                    let path = `${actionName}/${row.id}`;
                    path = AdminConfig.admin_path(`${$this.dataControllerName}/${path}`);

                    let iconOrValue = row[forKey];
                    let isAjaxCss = "";
                    let status = "";

                    // check if this is ajax : actionAjax
                    if ($this.isAjax(forKey)) {

                        isAjaxCss = $this.getAjaxCss(current);

                        isAjaxCss += " " + forKey + "_" + row[forKey]; //kind of "publish_1" or "publish_0";

                        if (withIcon) {
                            let icon_css = actionAlias["css_" + row[forKey]] || "";
                            iconOrValue = `<i class="js_icon ${icon_css}"></i>`;
                        }

                        //if this user has no rights - disable it
                        if (!$this.can(actionName)) {
                            isAjaxCss += " disabled";
                        }
                        status = row[forKey];
                    }

                    value = `<a class="${isAjaxCss}" data-value="${status}" data-rowid="${row.id}" data-action="${actionName}" title="${title}" href="${path}">${iconOrValue}</a>`;

                    //if edit and
                    if (forKey === "id" && $this.actionFields.indexOf("edit") === -1) {
                        value = iconOrValue;
                    }


                } else {
                    value = row[forKey];
                    let media_path = AdminConfig.get("media_path")
                    value = (fields.isImage === false) ? value : (value !== '' && value != null) ? getImgOrVideo(value, media_path) : value;
                }

                function getImgOrVideo(v, mediaPath) {
                    let imgs = ["apng", "avif", "gif", "jpg","jpeg", "jfif", "pjpeg", "pjp", "png", "svg", "webp", "bmp", "ico", "cur", "tif", "tiff"];
                    let vids = ["mp4", "mov", "ogg"];
                    let ext = v.substring(v.lastIndexOf(".")+1);
                    let str = "";
                    if(vids.indexOf(ext) >= 0) {
                        str = `<a class="table-links" href="${mediaPath}/${v}" target="_blank"><video height="30"><source src="${mediaPath}/${v}"></video></a>`;
                    } else if (imgs.indexOf(ext) >= 0) {
                        str = `<a class="table-links" href="${mediaPath}/${v}" target="_blank"><img height="30" src='${mediaPath}/${v}' /></a>`;
                    } else {
                        str = `<a class="table-links" href="${mediaPath}/${v}" target="_blank">${v}</a>`;
                    }
                    return str;
                }

                return value;

            }

        },
        getActionValue(row, key) {

            let html = [];

            //this is last column things - edit/delete etc
            if (this.actionFields.length > 0) {

                for (let i = 0; i < this.actionFields.length; i++) {
                    let current = this.actionFields[i];
                    let isAjax = this.getAjaxCss(current);
                    let title = current.charAt(0).toUpperCase() + current.slice(1);

                    let path = "";

                    if (current === "edit") {
                        //path = AdminConfig.admin_path(`${this.dataControllerName}/${row.id}/${current}`);
                        path = AdminConfig.admin_path(`${this.dataControllerName}/${current}/${row.id}`);
                    } else if (current === "delete") {

                        //console.log("this.minResultsNeeded <= this.rows.length ", this.minResultsNeeded , this.rows.length)

                        if (this.minResultsNeeded !== -1 && this.minResultsNeeded >= this.rows.length) {
                            console.info(`Hiding delete button, because ${this.minResultsNeeded} record(s) needed.`);
                        } else {
                            path = AdminConfig.admin_path(`${this.dataControllerName}/destroy/${row.id}`);
                        }
                    } else {
                        path = AdminConfig.admin_path(`${this.dataControllerName}/${current}/${row.id}`);
                    }

                    if (path !== "") {
                        let icon = `<i class="js_icon ${this.getIconCSS(current)}"></i>`;
                        let link = `<a class="${isAjax}" data-rowid="${row.id}" data-action="${current}" title="${title}" href="${path}" class="${current}">${icon}</a>`;
                        html.push(link);
                    }

                }
            }

            if (this.moreActions.length > 0) {
                for (let i = 0; i < this.moreActions.length; i++) {
                    let current = this.moreActions[i];

                    let isAjax = this.getAjaxCss(current);
                    let css = current["css"] || "";
                    let attributes = "";
                    if (current.hrefAttributes) {
                        for (let attr in current.hrefAttributes) {
                            if (current.hrefAttributes.hasOwnProperty(attr)) {
                                attributes += `${attr}='${current.hrefAttributes[attr]}' `;
                            }
                        }
                    }
                    let action = current.action;
                    let value = row[current.action_append_field] || "";
                    let title = current.label.charAt(0).toUpperCase() + current.label.slice(1);
                    let path = AdminConfig.admin_path(`${this.dataControllerName}/${action}/${value}`);
                    let icon = `<i class="js_icon ${current.icon_css}"></i>`;
                    let link = `<a class="${isAjax} ${css}" data-rowid="${row.id}" title="${title}" href="${path}" data-action="${action}" ${attributes}>${icon}</a>`;
                    html.push(link);
                }
            }

            return html.join("&nbsp;");
        },
        can(rights) {
            return (this.userRights.indexOf(rights) >= 0);
        },
        isAjax(action) {

            return (this.actionAjax.indexOf(action) >= 0);
        },
        getAjaxCss(action) {

            let css = "";
            let action_css = (Object.prototype.toString.call(action) === "[object Object]") ? action.action : action;
            if (this.isAjax(action)) {
                css = "js_action js_ajax js_" + action_css;
            } else {
                css = "js_action js_" + action_css;
            }

            return css;
        },
        getDefaultValue(key, dv) {

            return window.Laravel.htcmsAdminConfig(key) || dv;
        },
        getIconCSS(key) {
            let action_icon_css = this.getDefaultValue("action_icon_css", "");

            return action_icon_css[key] || "";
        },
        getTotalColumns() {
            return this.headings.length;
        },
        getTotalRows() {
            return this.rows.length;
        },
        isZeroResult() {
            return this.rows.length === 0;
        },
        highlight() {
            let id = queryBuilder.get("id");
            if (id !== "") {
                let row = "row_" + id;
                Fx.scrollWinTo("#"+row, function () {
                    Fx.highlight(row);
                });
            }
        },
        deleteNow(isOk = 1) {
            let $this = this;
            let rowid = -1;
            try {
                if (isOk === 1) {

                    let target = this.currentActionItem;
                    if (this.can("delete")) {
                        rowid = "row_" + target.getAttribute("data-rowid");
                        let href = target.getAttribute("href");
                        //Delete in db
                        this.doAjax("delete", href, feedback);

                    } else {

                        //sorry you don't have permission.
                        Toast.show($this, "Sorry! You don't have permission to delete.");
                        //some issue - will fix this
                        setTimeout(function () {
                            $this.showHideSpinner($this.currentActionItem, false);
                        }, 1000);
                    }
                } else {
                    $this.showHideSpinner($this.currentActionItem, false);
                }
            } catch (e) {
                $this.showHideSpinner($this.currentActionItem, false);
                Toast.show($this, "I don't understand what you are trying to do.", 5000);
            }

            //After delete
            function feedback(res) {

                if (res.status === 200) {
                    //delete from index
                    let index = $this.rows.findIndex(function (current) {
                        return current.id.toString() === res.data.id.toString();
                    });
                    if (index >= 0) {
                        Toast.show($this, "Record has been deleted", 2000);
                        if (document.getElementById(rowid)) {
                            document.getElementById(rowid).remove();
                        }
                        $this.rows.splice(index, 1);
                        $this.bindAction();
                        EventBus.emit('pagination-on-delete');
                    }

                } else {
                    $this.showHideSpinner($this.currentActionItem, false);
                    Toast.show($this, "Ooops! Got some error!");
                }

            }

            this.closeDialog();
        },
        showHideSpinner(event, show = true) {
            let css = this.spinnerCss;
            if (event) {
                let current = (event instanceof HTMLAnchorElement) ? event : event.currentTarget;
                this.removeAllSpinners(); //remove old one
                if (show === true) {
                    current.firstElementChild.classList.add(...css);
                }
            } else {
                //remove all spinner
                this.removeAllSpinners();
            }
        },
        removeAllSpinners() {
            let css = this.spinnerCss;
            let allSpinners = document.querySelectorAll(".fa-spinner");
            if (allSpinners.length > 0) {
                allSpinners.forEach(function (current) {
                    current.classList.remove(...css);
                })
            }
        },
        closeDialog() {
            this.$refs.deleteBox.close();
            //this.currentActionItem = null;
        },
        doAjax(methodType = "get", url, success, fail) {

            let $this = this;
            return axios[methodType](url)
                .then(function (res) {

                    if (success) {
                        success.apply(this, arguments);
                        //success(res);

                    }

                })
                .catch(function (res) {
                    if (fail) {
                        //fail(res);
                        fail.apply(fail, arguments);
                    } else {
                        //show default message
                        Toast.show($this, res.message, 5000);
                    }

                    $this.showHideSpinner($this.currentActionItem, false);

                });
        },
        initAjaxAction(event) {

            //@todo: action needs to handle in separate module
            event.preventDefault();
            event.stopPropagation();

            let $this = this;
            let current = event.currentTarget;

            //console.log("current ",current);
            let dataset =  current.dataset;
            let action = dataset.action;
            let status = dataset["value"];
            let href = current.getAttribute("href");
            if (status) {
                href = href + "/" + status;
            }

            this.currentActionItem = current;

            switch (action) {
                //handle special case
                case 'delete':
                    this.alertMessage.current = this.alertMessage.delete;
                    this.isDelete = true;
                    //prevent delete box is true then don't show delete box popup
                    if (this.preventDeleteBox === false) {
                        this.openDeleteAlert(current);
                    } else {
                        this.deleteNow(1);
                    }
                    //this.openPermissionAlert();
                    break;
                case 'showinfo':
                    let whatinfo = dataset.info;
                    let editable = dataset.editable;
                    let rowid = dataset.rowid;
                    let excludeFields = dataset.excludefields || [];
                    this.showInfo(whatinfo, rowid, excludeFields, editable);
                    break;

                default:

                    this.doAjax("get", href, function (res) {

                        $this.showHideSpinner(current, false);

                        let actionFieldProp = $this.isMakeFieldAsLink(action);

                        //remove old class
                        let icon = current.getElementsByClassName("js_icon");
                        let icon_css = actionFieldProp["css_" + status];
                        if (icon.length > 0) {
                            icon = icon[0];
                            icon.classList.remove(...(icon_css.split(" ")));
                        }
                        //set new value
                        current.setAttribute("data-value", res.data.status);
                        //css handling
                        icon_css = actionFieldProp["css_" + res.data.status];
                        icon.classList.add(...icon_css.split(" "));

                    }, function (res) {

                        $this.showHideSpinner(current, false);
                        Toast.show($this, res?.response?.data?.message || "Unknown Error!", 3000);
                    });
                    break;
            }

        },
        bindAction() {

            let allElement = this.$el.getElementsByClassName("js_action");

            //console.log(allElement);

            for (let i = 0; i < allElement.length; i++) {
                let current = allElement[i];
                if (current.classList.contains("js_ajax")) {
                    current.addEventListener("click", this.initAjaxAction, false);
                }
                if (current.classList.contains("js_action")) {
                    current.addEventListener("click", this.showHideSpinner, false);
                }
            }

            //handle edit
            if (this.editHandledByOtherComponent === true) {
                let editElements = this.$el.querySelectorAll("a[data-action='edit']");
                editElements.forEach(function (current, index) {
                    current.addEventListener("click", function (event) {
                        event.preventDefault();
                        EventBus.emit('list-view-pre-edit', this);
                    })
                })
            }


        },
        openDeleteAlert(current) {
            this.$refs.deleteBox.open(current);
        },
        openPermissionAlert() {
            this.alertMessage.current = this.alertMessage.permission;
            this.isDelete = false;
            this.currentActionItem = null;
            this.$refs.deleteBox.open();
        },
        filterFieldsName(value) {
            value = value.replace(/\.|_/g, " ");
            return value.charAt(0).toUpperCase() + value.slice(1);
        },
        filterSnakeCase(value) {
            return value.replace(/\s|\./g, "_").toLowerCase();
        },
        getCssForRow: function (fields, index) {
            if (fields.length - 1 === index) {
                return "active";
            }
            return "";
        },
        showInfo(type, id, excludeFields, editable) {
            this.$refs.infoPopup.showInfo(type, id, excludeFields, editable);
        }
    }
}

</script>
