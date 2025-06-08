<template>
    <div class="row mt-2 pb-2 border-bottom">
        <div v-show="hasMicrosites" class="col-auto">
            <select v-model="microSiteId" class="form-select select" @change="fetchNewData()">
                <option value="0">Select a MicroSite</option>
                <option v-for="microsite in microsites" :value="microsite.id">{{ microsite.name }}</option>
            </select>
        </div>
        <div class="col-auto">
            <select v-model="categoryId" class="form-select select" @change="fetchNewData()">
                <option>Select a Category</option>
                <option v-for="category in categories" :value="category.category_id">{{ category.name }}</option>
            </select>
        </div>
        <div class="col-auto">
            <select v-show="hasPlatformMoreThanOne" v-model="platformId" class="form-select select"
                    @change="fetchNewData()">
                <option>Select a Platform</option>
                <option v-for="platform in platforms" :value="platform.id">{{ platform.name }}</option>
            </select>
        </div>
        <div class="col-auto" v-show="hasTheme">
            <span
                class="hand" title="Click to see theme info"
                @click="showInfo('theme', themeInfo.id)">Theme: <u>{{ themeInfo.name }}</u></span>
        </div>
        <div class="col d-flex justify-content-end">
            <div v-if="hasTheme" class="btn-group">
                <button v-if="canDelete" aria-label="Delete All Modules form this category" class="btn btn-danger" title="Delete All Modules form this category"
                        type="button"
                        @click="showHideDeleteAlert(true)"><span aria-hidden="true"
                                                                            class="fa fa-trash-o"></span></button>
                <button aria-label="Copy Data From" class="btn btn-outline-secondary" title="Copy Data From"
                        type="button" @click="showHideCopyAlert(true)"><span aria-hidden="true"
                                                                                 class="fa fa-copy"></span></button>
            </div>
        </div>
    </div>
    <div class="row mt-3" v-if="hasError">
        <div class="col left-zero">
            <div class="alert alert-danger col-md-8">
                <ul>
                    <li v-for="error in errors" v-html="error.message">

                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col col-9">
            <div class="row homepage-modules">
                <div v-for="hook in hooks" :ref="'panel_'+hook.info.id" class="col-4">
                    <div class="non-selectable shadow-sm card mb-3">
                        <div class="card-header hook-header" @dblclick="showHideHookPanel(hook.info.id)">
                            <span class="pull-right small"><span :class="'fa hand fa-expand'" title="Expand/Contract"
                                                                 @click="onlyMe(hook.info.id, $event)"></span> &nbsp;<span
                                class="fa fa-minus hand" title="Minimize/Maximize"
                                @click="showHideHookPanel(hook.info.id)"></span></span>
                            <a href="javascript:void(0)"
                               @click="showInfo('hook', hook.info.id)"> {{ hook.info.name }}</a>
                        </div>
                        <div :ref="'hook_panel_'+hook.info.id" class="card-body hook_modules js_modules">
                            <ul :data-hook-id="hook.info.id"
                                class="list-group list-group-flush modules-list js_modules js_hook_modules">
                                <template v-if="hook.modules.length > 0">
                                    <li v-for="module in hook.modules" :data-module-id="module.id" class="js_item">
                                        <a href="javascript:void(0)"
                                           @click="showInfo('module', module.id)">{{ module.name }}</a>
                                        <span v-if="canDelete" :data-hook-id="hook.info.id"
                                              class="js_delete delete hand pull-right fa fa-trash-o"
                                              title="Delete this module from the hook"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <div class="card-footer bg-gradient border-light" v-if="hasAnyModulesInAHook(hook) && canDelete">
                            <span
                                  @click="deleteAllModuleFromHook(hook.info.id)" class="js_delete_from_hook hand pull-right fa fa-trash-o"
                                  title="Delete all module from this hook" :data-hook-id="hook.info.id"></span>
                        </div>
                    </div>
                </div>
                <div v-if="hasTheme" ref="savePanel" class="clearboth text-center pt-5">
                    <button :class="saveButtonCss" type="button" @click="saveModules()">Save Now!</button>
                </div>
            </div>
        </div>
        <div class="col col-3" v-if="!hasError">
            <div class="panel-sub-heading">
                <div class="input-group">
                    <input v-model="searchKey" aria-describedby="basic-addon3" class="form-control"
                           placeholder="Search by id, name or alias" type="text">
                </div>
            </div>
            <ul id="draggableModules" class="modules-list all-modules-right">
                <li v-for="module in filterModules" :data-module-id="module.id" class="js_item"><span
                    v-html="module.name"></span> <span class="delete hand pull-right fa fa-trash-o"></span></li>
            </ul>
        </div>
    </div>

    <modal-box ref="copyBox" data-show-footer="true" :data-modal-css="modalCss">
        <template #title>
            Copy Data
        </template>
        <template #content>
            <div class="row">
                <div class="col-1 p-1">
                    <label class="col-md-3 d-flex">From:</label>
                </div>
                <div class="col-auto p-1" v-if="hasSiteMoreThanOne">
                    <select v-model="fromData.site_id" class="form-select select-sm">
                        <option value="0">Select a MicroSite</option>
                        <option v-for="site in allSites" :value="site.id">{{ site.name }}</option>
                    </select>
                </div>
                <div class="col-auto p-1" v-if="hasMicrosites">
                    <select v-model="fromData.microsite_id" class="form-select select-sm">
                        <option value="0">Select a Microsite</option>
                        <option v-for="microsite in microsites" :value="microsite.id">{{ microsite.name }}</option>
                    </select>
                </div>
                <div class="col-auto p-1" v-if="hasPlatformMoreThanOne">
                    <select v-model="fromData.platform_id" class="form-select select-sm">
                        <option value="0">Select a Platform</option>
                        <option v-for="platform in platforms" :value="platform.id">{{ platform.name }}</option>
                    </select>
                </div>
                <div class="col-auto p-1">
                    <select class="form-select select-sm" v-model="fromData.category_id">
                        <option value="0">Select a Category</option>
                        <option v-for="category in fromCategories" :value="category.category_id">{{ category.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-1 p-1">
                    <label class="col-md-3 d-flex">To:</label>
                </div>
                <div class="col-auto p-1" v-if="hasSiteMoreThanOne">
                    <select v-model="toData.site_id" class="form-select select-sm">
                        <option value="0">Select a MicroSite</option>
                        <option v-for="site in allSites" :value="site.id">{{ site.name }}</option>
                    </select>
                </div>
                <div class="col-auto p-1" v-if="hasMicrosites">
                    <select v-model="toData.microsite_id" class="form-select select-sm">
                        <option value="0">Select a MicroSite</option>
                        <option v-for="microsite in microsites" :value="microsite.id">{{ microsite.name }}</option>
                    </select>
                </div>
                <div class="col-auto p-1" v-if="hasPlatformMoreThanOne">
                    <select v-model="toData.platform_id" class="form-select select-sm">
                        <option value="0">Select a Platform</option>
                        <option v-for="platform in platforms" :value="platform.id">{{ platform.name }}</option>
                    </select>
                </div>
                <div class="col-auto p-1">
                    <select v-model="toData.category_id" class="form-select select-sm">
                        <option value="0">Select a Category</option>
                        <option v-for="category in toCategories" :value="category.category_id">{{ category.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div v-show="hasPlatformMoreThanOne" class="plr5 v-space">
                    <div class="alert alert-info">
                        Please do not select <strong>source platform</strong> if you want to copy modules in all platforms respectively.
                    </div>
                </div>
            </div>
        </template>
        <template #footer class="center-align">
            <div v-show="isWorking" class="loader">Please wait... <span class="fa fa-loader"></span></div>
            <button class="btn btn-success btn-from-submit" @click="copyDataFromCategory()">Copy Now</button>
            <button class="btn btn-outline-secondary" @click="showHideCopyAlert(false)">Cancel</button>
        </template>
    </modal-box>

    <modal-box ref="deleteBox" data-show-footer="true" data-title-css="alert-danger">
        <template #title>
            Warning!!!
        </template>
        <template #content>
            <span class="text-danger">Are you sure to delete all modules from this category? Can't be undone.</span>
            <div>
                <label v-show="hasPlatformMoreThanOne" class="p-2"
                       title="Delete from other platforms too">
                    <input v-model="applicableForAllPlatforms" type="checkbox"/> Delete from other platforms too</label>
            </div>
        </template>
        <template #footer class="center-align">
            <button class="btn btn-danger" @click="deleteAllFromCategory()">Yes</button>
            <button class="btn btn-outline-secondary" @click="showHideDeleteAlert(false)">No</button>
        </template>
    </modal-box>

    <info-popup ref="infoPopup"></info-popup>

</template>

<script>

import {Toast, Modal, Loader} from '../helpers/common';
import Sortable from 'sortablejs';
import InfoPopup from './info-popup.vue';
import ModalBox from "../library/modal-box.vue";


export default {
    components: {
        'info-popup': InfoPopup,
        'modal-box': ModalBox
    },
    mounted() {


        /*console.log("siteInfo", this.siteInfo);
        console.log("allModules", this.allModules);
        console.log("categoryModules", this.categoryModules);
        console.log("categoryInfo", this.categoryInfo);
        console.log("themeInfo", this.themeInfo);
        */

        this.init();
    },
    created() {
        //this.initData();
    },

    props: [
        'dataCategories',
        'dataMicrosites',
        'dataPlatforms',
        'dataSiteId',
        'dataMicrositeId',
        'dataCategoryId',
        'dataPlatformId',
        'dataSiteInfo',
        'dataHookInfo',
        'dataAllModules',
        'dataCategoryModules',
        'dataCategoryInfo',
        'dataThemeInfo',
        'dataUserRights',
        'dataIsModuleReadonly',
        'dataAllSites'
    ],
    computed: {
        hasSiteMoreThanOne() {
            //return true;
            return this.allSites.length > 1;
        },
        hasMicrosites() {
            //return true;
            return this.microsites.length > 0;
        },
        hasPlatformMoreThanOne() {
            //return true;
            return this.platforms.length > 1;
        },
        hasTheme() {
            return this.themeInfo && this.themeInfo["id"];
        },
        saveButtonCss() {
            let disabled = (this.enableDisableSave() === true) ? "" : " disabled";
            return "btn btn-success btn-form-submit" + disabled;
        },
        canEdit() {
            return this.userRights.indexOf("edit") >= 0 && this.isModuleReadonly === false;
        },
        canDelete() {
            return this.userRights.indexOf("delete") >= 0 && this.isModuleReadonly === false;
        },
        modalCss() {

          /*  if (this.hasSiteMoreThanOne && this.hasPlatformMoreThanOne) {
                return "modal-lg";
            }*/
            return "modal-lg";
        },
        fromCategories() {
            return this.categoriesData[this.fromData.site_id] || [];
        },
        toCategories() {
            return this.categoriesData[this.toData.site_id] || [];
        },
        hasError() {
            return this.errors.length > 0;
        },
        filterModules() {
            let key = this.searchKey;
            if (key !== "" && key != null) {

                key = key.toLowerCase();
                return this.allModules.filter((current)=> {
                    let alias = current.alias.toLowerCase();
                    let name = current.name.toLowerCase();
                    let id = current.id;
                    if (id.toString() === key || alias.includes(key) || name.includes(key)) {
                        return true;
                    }
                });

            } else {
                this.searchKey = '';
                // console.log("data", this.allData.data);
                return this.allModules;
            }
        }
    },
    data() {
        return {
            categoriesData: {},
            categories: (typeof this.dataCategories == "undefined" || this.dataCategories === "") ? [] : JSON.parse(this.dataCategories),
            microsites: (typeof this.dataMicrosites == "undefined" || this.dataMicrosites === "") ? [] : JSON.parse(this.dataMicrosites),
            platforms: (typeof this.dataPlatforms == "undefined" || this.dataPlatforms === "") ? [] : JSON.parse(this.dataPlatforms),
            siteId: (typeof this.dataSiteId == "undefined" || this.dataSiteId === "") ? 1 : parseInt(this.dataSiteId),
            microSiteId: (typeof this.dataMicrositeId == "undefined" || this.dataMicrositeId === "") ? 0 : parseInt(this.dataMicrositeId),
            platformId: (typeof this.dataPlatformId == "undefined" || this.dataPlatformId === "") ? 1 : parseInt(this.dataPlatformId),
            categoryId: (typeof this.dataCategoryId == "undefined" || this.dataCategoryId === "") ? 0 : parseInt(this.dataCategoryId),
            siteInfo: (typeof this.dataSiteInfo == "undefined" || this.dataSiteInfo === "") ? [] : JSON.parse(this.dataSiteInfo),
            hookInfo: (typeof this.dataHookInfo == "undefined" || this.dataHookInfo === "") ? [] : JSON.parse(this.dataHookInfo),
            allModules: (typeof this.dataAllModules == "undefined" || this.dataAllModules === "") ? [] : JSON.parse(this.dataAllModules),
            categoryModules: (typeof this.dataCategoryModules == "undefined" || this.dataCategoryModules === "") ? [] : JSON.parse(this.dataCategoryModules),
            categoryInfo: (typeof this.dataCategoryInfo == "undefined" || this.dataCategoryInfo === "") ? [] : JSON.parse(this.dataCategoryInfo),
            themeInfo: (typeof this.dataThemeInfo == "undefined" || this.dataThemeInfo === "") ? [] : JSON.parse(this.dataThemeInfo),
            hooks: [],
            hooksInfoCache: {},
            moduleInfoCache: {},
            noHookFound: false,
            errors: [],
            enableSave: false,
            searchKey: '',
            applicableForAllPlatforms: false,
            fromData: {site_id: 0, microsite_id: 0, platform_id: 0, category_id: 0},
            toData: {site_id: 0, microsite_id: 0, platform_id: 0, category_id: 0},
            isWorking: false,
            sortObj: {draggable: null},
            userRights: (this.dataUserRights ? JSON.parse(this.dataUserRights) : []),
            isModuleReadonly: (this.dataIsModuleReadonly === "1") ? true : false,
            allSites: (typeof this.dataAllSites == "undefined" || this.dataAllSites === "") ? [] : JSON.parse(this.dataAllSites),
            copyForAllPlatforms: false
        }
    },
    methods: {
        init() {

            if (this.categories.length === 0) {
                this.addErrorMessage("No category found!");
            }
            if (this.categoryInfo.length === 0) {
                this.addErrorMessage("There is a mismatch in site default category. Please fix that.");
            }
            //console.log('this.themeInfo["id"] ', this.themeInfo["id"])
            if (this.hasTheme) {
                this.parseTheme();
                this.populateModules();
                this.enableSorting();
                this.makeCategories();
            } else {
                let path = AdminConfig.admin_path("category/settings", {platform_id: this.platformId});
                this.addErrorMessage(`This category/platform/theme is not available in category_site table.
                         You need to drag and drop in <a href='${path}'>category settings</a>.`);
                this.addErrorMessage(`Dont' forget the set the theme there.`);
            }

        },
        makeCategories() {
            let totalSites = this.allSites.length;
            if (totalSites > 0) {
                for (let i = 0; i < totalSites; i++) {
                    let current = this.allSites[i];
                    this.categoriesData[current.id] = current.category;
                }
            }
            this.setDefaultFromTo();

        },
        setDefaultFromTo() {
            let totalSites = this.allSites.length;
            if (totalSites > 0) {
                this.fromData.site_id = this.allSites[0].id;
                this.toData.site_id = this.siteId; //current site id
            } else {
                this.fromData.site_id = this.siteId; //current site
                this.toData.site_id = this.siteId;
            }

            this.fromData.platform_id = this.platformId;
            this.toData.platform_id = this.platformId;
            this.toData.category_id = this.categoryId;
        },
        saveNow(url, data) {
            Loader.show(this);
            return new Promise((resolve, reject) => {
                axios.post(url, data)
                    .then(response => {
                        resolve(response);
                    }).catch(error => {
                    reject(error.response);
                }).finally(()=> {
                    Loader.hide(this);
                });
            });

        },
        getWhere() {
            let where = {
                site_id: this.siteId,
                microsite_id: this.microSiteId,
                platform_id: this.platformId,
                category_id: this.categoryId
            };
            return where;
        },
        saveModules() {
            if (this.enableDisableSave() === false) {
                return false;
            }
            //Save in DB
            let $this = this;
            //let currentData = (action == "add") ? this.allData.data : this.siteData;
            let postParams = {};

            //loop through all hooks
            let allHooks = document.querySelectorAll(".js_hook_modules");
            let datas = [];
            allHooks.forEach(function (current) {
                let allModules = current.querySelectorAll("li.js_item");
                let hook_id = current.getAttribute("data-hook-id");
                //console.log("hook_id: ", hook_id, allModules, allModules.length);
                if (allModules.length > 0) {
                    let modules = [];
                    for (let i = 0; i < allModules.length; i++) {
                        //console.log(allModules[i]);
                        let currentModule = allModules[i];
                        let module_id = currentModule.getAttribute("data-module-id");
                        modules.push({module_id: module_id, position: i + 1});
                    }

                    let data = {hook_id: hook_id, modules: modules};
                    datas.push(data);
                }
                //console.log(datas);
            });

            if (datas.length > 0) {

                let where = $this.getWhere();
                postParams.data = datas;
                postParams.where = where;
                postParams.applicableForAllPlatforms = false;
                this.saveNow(AdminConfig.admin_path("homepage/saveSettings"), postParams).then(function (res) {
                    //console.log(res);
                    Toast.show($this, "Saved...");
                    $this.enableDisableSave(false);

                }).catch(function (res) {
                    $this.showError(res);
                });
            }

        },
        populateModules() {
            //Display module on init
            let $this = this;
            let modules = this.categoryModules;

            if (modules.length > 0) {
                for (let i = 0; i < modules.length; i++) {
                    let current = modules[i];
                    let hook = $this.getHookInfoCache(current.hook_id);
                    try {
                        let moduleInfo = $this.getModuleInfo(current.module_id);
                        this.addModule(hook.hookIndex, moduleInfo);
                    } catch (e) {
                        console.error("@populateModules: " + e.message);
                        $this.showError("@populateModules: " + e.message);
                    }

                }
            } else {
                console.info("There is no module added for this category.");
                Toast.show(this,"There is no module added for this category. Drag from the right panel to the left boxes. ", 5000)
            }

        },
        parseTheme() {
            //console.log("this.themeInfo.length ",this.themeInfo);
            if (this.hasTheme) {
                let $this = this;
                let theme = this.themeInfo;
                let skeleton = theme.skeleton;
                let regx_ske = /(%{cms.+}%)/gmi; //find cms key
                let conventions = skeleton.match(regx_ske);
                let modules = {};
                if (conventions && conventions.length > 0) {
                    let regx_m = /(cms.+})/;
                    let realIndex = 0;
                    conventions.forEach(function (current, index) {
                        let strcms = current.replace("%{cms.", "");
                        strcms = strcms.replace("}%", "");
                        let cms_arr = strcms.split(".");
                        let conventionType = cms_arr[0]; //can be hook or can be module;
                        let conventionName = cms_arr[1];

                        if (conventionType === "hook") {
                            let hookInfo = $this.getHookInfo(conventionName);
                            if (hookInfo !== null) {
                                $this.addHook({name: conventionName, modules: [], info: hookInfo});
                                $this.setHookInfoCache(hookInfo.id, {info: hookInfo, hookIndex: realIndex});
                                realIndex++;
                            } else {
                                console.error("@parseTheme " + conventionName + ". You need to add in your site");
                            }
                        }
                        if (conventionType === "module") {

                        }

                    })
                } else {
                    this.addErrorMessage(`There is no hook defined in for this theme (${this.themeInfo.name}})`)
                }
            } else {
                this.addErrorMessage("There is no theme available for this category")
            }

        },
        addErrorMessage(message) {
            this.errors.push({message: message});
        },
        findModuleIndex(hookIndex, module) {

            if (this.getHook(hookIndex) !== null) {
                let modules = this.getHook(hookIndex).modules;
                let found = modules.findIndex(function (current) {
                    return module.id === current.id;
                });
                return found;
            }
            return -1;
        },
        removeModules(idOrWhere, byType = "module_id", hookId = null) {
            let $this = this;
            let where;
            if (Object.prototype.toString.call(idOrWhere) === "[object Object]") {
                where = idOrWhere; // where is passed by param
            } else {
                where = this.getWhere();
                where[byType] = parseInt(idOrWhere);
            }

            //hook and module
            if (hookId !== null) {
                where["hook_id"] = parseInt(hookId);
                //remove from the hook->module array
                let hookInfo = this.getHookInfoCache(hookId);
                let module = this.getModuleInfo(idOrWhere);
                let moduleIndex = this.findModuleIndex(hookInfo.hookIndex, module);
                //remove module - refactor it
                this.getHook(hookInfo.hookIndex).modules.splice(moduleIndex, 1);
            }
            let postParams = {};
            postParams.where = where;
            postParams.applicableForAllPlatforms = this.applicableForAllPlatforms;
            this.enableDisableSave(true);

            //Disable auto save on remove
            /*return this.saveNow(AdminConfig.admin_path("homepage/removeModules"), postParams).then(function (res) {
                console.log(res);
                //Toast.show($this, res.message);
                Toast.show($this, "Removed...");

            }).catch(function (res) {
                $this.showError(res, true);
            });*/
        },
        addModule(hookIndex, module) {
            //console.log("1: addModule ", hookIndex, module);
            let found = this.findModuleIndex(hookIndex, module);

            if (found === -1) {
                let hook = this.getHook(hookIndex);
                hook.modules.push(module);

                //$this.refreshRightSide();
                return hook.modules.length;
            }

            return false;
        },
        refreshRightSide() {
            this.allModules.push(this.allModules[0]);
            this.allModules.pop();
        },
        highlightEagerDrop(addRemove = "add") {
            let js_hook_modules = document.querySelectorAll(".js_hook_modules");
            js_hook_modules.forEach(function (current) {
                current.classList[addRemove]("module-drop-eager");
                current.closest("div.js_modules").classList[addRemove]("p-0", "m-0");
            })

        },
        setHookInfoCache(hookId, data) {
            return this.hooksInfoCache[hookId] = data;
        },
        getHookInfoCache(hookId) {
            return this.hooksInfoCache[hookId];
        },
        getHookInfo(hookValue, byKey = "alias") {
            let hooks = this.hookInfo;

            for (let i = 0; i < hooks.length; i++) {
                let current = hooks[i];
                if (current[byKey] === hookValue) {
                    return current;
                }
            }
            return null;
        },
        addHook(data) {
            this.hooks.push(data);
        },
        getHook(hookIndex) {
            return this.hooks[hookIndex] || null;
        },
        getModuleInfo(module_id) {
            if (this.moduleInfoCache[module_id]) {
                return this.moduleInfoCache[module_id];
            }
            let found = null;
            for (let i = 0; i < this.allModules.length; i++) {
                let current = this.allModules[i];
                if (current.id.toString() === module_id.toString()) {
                    this.moduleInfoCache[module_id] = current;
                    found = current;
                    break;
                }
            }
            return found;
        },
        enableDragging() {

            if (!this.canEdit) {
                return false;
            }
            let $this = this;

            //Right Side module
            let draggableModules = document.getElementById("draggableModules");
            this.sortObj.draggable = Sortable.create(draggableModules, {
                animation: 200,
                draggable: ".js_item",
                group: {
                    name: 'modulesBox'/*,
                    pull: 'clone',
                    revertClone: true*/
                },
                sort: false,
                ghostClass: "js_modules",
                onEnd: function (/**Event*/evt) {

                    let to = evt.to;
                    let item = evt.item; // dragged HTMLElement

                    let hookId = to.getAttribute("data-hook-id");
                    let moduleId = item.getAttribute("data-module-id");

                    if (hookId) {
                        let hookInfo = $this.getHookInfoCache(hookId);
                        //console.log(hookInfo);
                        let moduleInfo = $this.getModuleInfo(moduleId);

                        let isAdded = $this.addModule(hookInfo.hookIndex, moduleInfo);


                        //remove original dropped, because it is populated by reactive hook->modules
                        item.parentNode.removeChild(item);

                        $this.highlightEagerDrop("remove");

                        //we can send feedback
                        if (isAdded === false) {
                            Toast.show($this, "Module is already added in this hook...");
                        }
                    }

                    //Enable Drag

                    $this.enableDisableSave(true);

                },
                onStart: function (/**Event*/evt) {
                    //console.log("on start");
                    $this.highlightEagerDrop("add");
                }
            });

        },
        enableSorting() {
            if (!this.canEdit) {
                return false;
            }
            let $this = this;
            this.$nextTick(function () {
                let list = document.querySelectorAll(".js_modules");
                list.forEach(function (current) {
                    Sortable.create(current, {
                        animation: 200,
                        filter: ".js_delete",
                        group: {
                            name: 'droppable',
                            put: 'modulesBox'
                        },
                        onFilter: function (evt) {
                            let item = evt.item,
                                ctrl = evt.target;
                            let parent = item.parentNode;
                            let hookId = parent.getAttribute("data-hook-id");
                            let id = item.getAttribute("data-module-id")

                            if (Sortable.utils.is(ctrl, ".js_delete")) {  // Click on remove button
                                $this.removeModules(id, "module_id", hookId);
                            }
                        },
                        onUpdate: function (/**Event*/evt) {
                            //console.log("@onUpdate: ", evt);
                            $this.enableDisableSave(true);
                            //reindex
                        }
                    })
                })

                $this.enableDragging(true);

            });


        },
        deleteAllModuleFromHook(hookId) {
            let hookInfo = this.getHookInfoCache(hookId);
            let hook = this.getHook(hookInfo.hookIndex);
            hook.modules = [];
            this.removeModules(hookId, "hook_id");
        },
        hasAnyModulesInAHook(hook) {
            return (typeof hook.modules != "undefined" && hook.modules.length > 0);
        },
        enableDisableSave(val) {
            if (val === undefined) {
                return this.enableSave;
            }
            this.enableSave = val;
        },
        showHideDeleteAlert(isShow) {
            if (isShow) {
                Modal.open(this, "deleteBox");
            } else {
                Modal.close(this, "deleteBox");
            }

        },
        deleteAllFromCategory() {
            this.showHideDeleteAlert(false);

            let allHooks = this.hooks;
            for (let i = 0; i < allHooks.length; i++) {
                allHooks[i].modules = [];
            }
            this.removeModules(this.categoryId, "category_id");
        },
        showHideCopyAlert(isShow = true) {
            if (isShow) {

                this.setDefaultFromTo();
                Modal.open(this, "copyBox");

            } else {
                Modal.close(this, "copyBox");
            }

        },
        copyDataFromCategory() {
            this.isWorking = true;
            let $this = this;
            let fromData = this.fromData;
            let toData = this.toData;
            let data = {
                fromData,
                toData
            }
            let url = AdminConfig.admin_path("homepage/copyData");
            this.saveNow(url, data).then(function (res) {
                //console.log(res)
                if (res.data.error) {
                    Toast.show($this, res.data.message, 5000);
                } else if (res.data.success) {

                    $this.showHideCopyAlert(false);
                    Toast.show($this, "Copied. Reloading...");
                    window.location.href = window.location.href;

                }
                $this.isWorking = false;
            }).catch(function (res) {
                $this.showHideCopyAlert(false);
                $this.isWorking = false;
                $this.showError(res);
            });

        },
        setWorking() {

        },
        isValidModule(module) {
            let isValid = (module != null && typeof module != "undefined" && module.id > 0);
            if (isValid === false) {
                console.error("There is some error in db entries for this category. " +
                    "This can be fixed by deleting all modules and drag and drop again.");
            }
            return isValid;
        },
        fetchNewData() {
            let where = this.getWhere();
            delete where.site_id;
            if (where.microsite_id === 0) {
                delete where.microsite_id;
            }
            ;
            let url = "homepage/ui";
            window.location.href = AdminConfig.admin_path(url, where);
        },
        showInfo(type, id) {
            this.$refs.infoPopup.showInfo(type, id);
        },
        showHideHookPanel(id) {
            //console.log(id, this.$refs["hook_panel_"+id]);
            let target = this.$refs["hook_panel_" + id][0];
            if (target.className.indexOf("hide") === -1) {
                target.classList.add("hide");
            } else {
                target.classList.remove("hide");
            }

        },
        onlyMe(id, evt) {

            let $this = this;

            let target = this.$refs["panel_" + id][0];
            if (target.className.indexOf("fixed") === -1) {
                hideAllHooks(true);
                //show current and fixed it
                target.classList.remove("hide");
                target.classList.add("fixed");
                //evt.target.classList.add("fa-compress");
            } else {
                hideAllHooks(false);
            }


            function hideAllHooks(shouldHide) {
                let savePanel = $this.$refs.savePanel;

                //hide old one
                for (let i = 0; i < $this.hooks.length; i++) {
                    let current = $this.hooks[i];
                    let current_ele = $this.$refs["panel_" + current.info.id][0];

                    if (shouldHide === true) {
                        current_ele.classList.add("hide");
                        current_ele.classList.remove("fixed");
                    } else {
                        current_ele.classList.remove("hide");
                        current_ele.classList.remove("fixed");
                    }
                }

                if (shouldHide === true) {
                    savePanel.classList.add("hide");
                } else {
                    savePanel.classList.remove("hide");
                }

            }
        },
        showError(response, reload=false) {
            let message = '';
            if (typeof response == "string") {
                message = response;
            } else {
                message = (response && response["data"] && response["data"]["message"]) ? response.data.message : "Unknown error!";
            }
            Toast.show(this, message, 5000);
            if (reload) {
                setTimeout(()=> {
                    window.location.reload();
                }, 1000)
            }

        }


    }
}


</script>
