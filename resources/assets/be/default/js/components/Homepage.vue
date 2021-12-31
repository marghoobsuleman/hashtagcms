<template>
    <div>
        <div class="btn-group margin-top-05 homepage-toolbar" role="group" style="display: inherit">
            <select v-show="hasMicrosites" class="form-control select" v-model="microSiteId" @change="fetchNewData()">
                <option value="0">Select a MicroSite</option>
                <option v-for="microsite in microsites" :value="microsite.id">{{microsite.name}}</option>
            </select>
            <select class="form-control select" v-model="categoryId" @change="fetchNewData()">
                <option>Select a Category</option>
                <option v-for="category in categories" :value="category.category_id">{{category.name}}</option>
            </select>
            <select v-show="hasTenantMoreThanOne" class="form-control select" v-model="tenantId" @change="fetchNewData()">
                <option>Select a Tenant</option>
                <option v-for="tenant in tenants" :value="tenant.id">{{tenant.name}}</option>
            </select>
            <label v-show="hasTenantMoreThanOne" title="It will be inserted or deleted for all tenants if checks."><input type="checkbox" v-model="applicableForAllTenants" /> Effective for all tenants</label>

            <div v-show="hasTheme" style="margin-left: 10px; display: inline"> <span @click="showInfo('theme', themeInfo.id)" class="hand" title="Click to see theme info"> Theme: {{themeInfo.name}}</span></div>
            <div class="pull-right text-right">
                <div class="btn-group" v-if="hasTheme">
                    <button v-if="canDelete" @click="showHideDeleteAlert(true)" type="button" class="btn btn-danger" aria-label="Delete All Modules form this category" title="Delete All Modules form this category"><span class="fa fa-trash-o" aria-hidden="true"></span></button>
                    <button @click="showHideCopyAlert(true)" type="button" class="btn btn-default" aria-label="Copy Data From" title="Copy Data From"><span class="fa fa-copy" aria-hidden="true"></span></button>
                </div>

            </div>

        </div>
        <div class="row margin-top-05">
            <div  class="col-md-9 left-zero" v-if="errors.length > 0">
                <div class="alert alert-danger col-md-8">
                    <ul>
                        <li v-for="error in errors" v-html="error.message">

                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-9 left-zero">
                <div class="col-md-6" v-for="hook in hooks" :ref="'panel_'+hook.info.id">
                    <div class="panel panel-success">
                        <div class="panel-heading" @dblclick="showHideHookPanel(hook.info.id)">
                            <h3 class="panel-title">
                                <a href="javascript:void(0)" @click="showInfo('hook', hook.info.id)">{{hook.info.name}}</a>
                                <div class="pull-right small">
                                    <span :class="'fa hand fa-expand'" title="Expand/Contract" @click="onlyMe(hook.info.id, $event)"></span> &nbsp;<span title="Minimize/Maximize" class="fa fa-minus hand" @click="showHideHookPanel(hook.info.id)" ></span>
                                </div>
                            </h3>
                        </div>
                        <div class="panel-body hook_modules" :ref="'hook_panel_'+hook.info.id">
                            <ul class="modules-list js_modules js_hook_modules" style="min-height: 100px" :data-hook-id="hook.info.id">
                                <li v-if="isValidModule(module)" v-for="module in hook.modules"  class="js_item" :data-module-id="module.id">
                                    <a href="javascript:void(0)" @click="showInfo('module', module.id)">{{module.name}}</a>
                                    <span v-if="canDelete" class="js_delete delete hand pull-right fa fa-trash-o" title="Delete this module from the hook" :data-hook-id="hook.info.id"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="panel-footer pb-4">
                            <span v-if="hasAnyModulesInAHook(hook) && canDelete" @click="deleteAllModuleFromHook(hook.info.id)" class="js_delete_from_hook hand pull-right fa fa-trash-o" title="Delete all module from this hook" :data-hook-id="hook.info.id"></span>
                        </div>
                    </div>
                </div>
                <div class="clearboth text-center" v-if="hasTheme" ref="savePanel">

                    <button type="button" :class="saveButtonCss" @click="saveModules()">Save Now!</button>

                </div>
            </div>
            <div class="col-md-3">
                <div class="panel-sub-heading" style="padding:4px">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon3">Search</span>
                        <input placeholder="Search by id, name or alias" type="text"  class="form-control" aria-describedby="basic-addon3" v-model="searchKey">
                    </div>
                </div>
                <ul class="modules-list all-modules-right" id="draggableModules">
                    <li v-for="module in filterModules()" class="js_item" :data-module-id="module.id"> <span v-html="module.name"></span>  <span class="delete hand pull-right fa fa-trash-o"></span></li>
                </ul>
            </div>
        </div>

        <modal-box ref="copyBox" data-show-footer="true" :data-width="modalWidth">
            <div slot="title">
                Copy Data
            </div>
            <div slot="content">
                <div class="row">
                    <div class="plr5">
                        <label class="col-md-3">From:</label>

                        <select v-if="hasSiteMoreThanOne" class="form-control select" v-model="fromData.site_id">
                            <option value="0">Select a MicroSite</option>
                            <option v-for="site in allSites" :value="site.id">{{site.name}}</option>
                        </select>
                        <select v-if="hasMicrosites" class="form-control select" v-model="fromData.microsite_id">
                            <option value="0">Select a MicroSite</option>
                            <option v-for="microsite in microsites" :value="microsite.id">{{microsite.name}}</option>
                        </select>
                        <select v-if="hasTenantMoreThanOne" class="form-control select" v-model="fromData.tenant_id">
                            <option value="0">Select a Tenant</option>
                            <option v-for="tenant in tenants" :value="tenant.id">{{tenant.name}}</option>
                        </select>
                        <select class="form-control select" v-model="fromData.category_id">
                            <option value="0">Select a Category</option>
                            <option v-for="category in fromCategories" :value="category.category_id">{{category.name}}</option>
                        </select>
                    </div>
                    <div class="plr5 v-space">
                        <label class="col-md-3">To:</label>
                        <select v-if="hasSiteMoreThanOne" class="form-control select" v-model="toData.site_id">
                            <option value="0">Select a MicroSite</option>
                            <option v-for="site in allSites" :value="site.id">{{site.name}}</option>
                        </select>
                        <select v-if="hasMicrosites" class="form-control select" v-model="toData.microsite_id">
                            <option value="0">Select a MicroSite</option>
                            <option v-for="microsite in microsites" :value="microsite.id">{{microsite.name}}</option>
                        </select>
                        <select v-if="hasTenantMoreThanOne" class="form-control select" v-model="toData.tenant_id">
                            <option value="0">Select a Tenant</option>
                            <option v-for="tenant in tenants" :value="tenant.id">{{tenant.name}}</option>
                        </select>
                        <select class="form-control select" v-model="toData.category_id">
                            <option value="0">Select a Category</option>
                            <option v-for="category in toCategories" :value="category.category_id">{{category.name}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div slot="footer" class="center-align">
                <div class="loader" v-show="isWorking">Please wait... <span class="fa fa-loader"></span> </div>
                <button class="btn btn-success" @click="copyDataFromCategory()">Copy Now</button>
                <button @click="showHideCopyAlert(false)" class="btn btn-default">Cancel</button>
            </div>
        </modal-box>

        <modal-box ref="deleteBox" data-title-css="alert-danger" data-show-footer="true">
            <div slot="title">
                Warning!!!
            </div>
            <div slot="content">
                Are you sure to delete all modules from this category? Can't be undone.
            </div>
            <div slot="footer" class="center-align">
                <button class="btn btn-danger" @click="deleteAllFromCategory()">Yes</button>
                <button @click="showHideDeleteAlert(false)" class="btn btn-default">No</button>
            </div>
        </modal-box>

        <info-popup ref="infoPopup"></info-popup>

    </div>
</template>

<script>

    import {Toast, Modal, Loader} from '../helpers/Common';
    import Sortable from 'sortablejs';
    import InfoPopup from './InfoPopup';


    export default {
        components: {
            'info-popup': InfoPopup
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
            'dataTenants',
            'dataSiteId',
            'dataMicrositeId',
            'dataCategoryId',
            'dataTenantId',
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
            hasTenantMoreThanOne() {
                //return true;
                return this.tenants.length > 1;
            },
            hasTheme() {
                return this.themeInfo && this.themeInfo["id"];
            },
            saveButtonCss() {
                let disabled = (this.enableDisableSave() === true) ? "" : " disabled";
                return "btn btn-success btn-lg"+disabled;
            },
            canEdit() {
                return this.userRights.indexOf("edit") >=0 && this.isModuleReadonly==false;
            },
            canDelete() {
                return this.userRights.indexOf("delete") >=0 && this.isModuleReadonly==false;
            },
            modalWidth() {

                if(this.hasSiteMoreThanOne && this.hasTenantMoreThanOne) {
                    return "800px";
                }
                return "";
            },
            fromCategories() {
              return this.categoriesData[this.fromData.site_id] || [];
            },
            toCategories() {
                return this.categoriesData[this.toData.site_id] || [];
            }
        },
        data() {
            return {
                categoriesData:{},
                categories:(typeof this.dataCategories == "undefined" || this.dataCategories == "") ? [] : JSON.parse(this.dataCategories),
                microsites:(typeof this.dataMicrosites == "undefined" || this.dataMicrosites == "") ? [] : JSON.parse(this.dataMicrosites),
                tenants:(typeof this.dataTenants == "undefined" || this.dataTenants == "") ? [] : JSON.parse(this.dataTenants),
                siteId:(typeof this.dataSiteId == "undefined" || this.dataSiteId == "") ? 1 : parseInt(this.dataSiteId),
                microSiteId:(typeof this.dataMicrositeId == "undefined" || this.dataMicrositeId == "") ? 0 : parseInt(this.dataMicrositeId),
                tenantId:(typeof this.dataTenantId == "undefined" || this.dataTenantId == "") ? 1 : parseInt(this.dataTenantId),
                categoryId:(typeof this.dataCategoryId == "undefined" || this.dataCategoryId == "") ? 0 : parseInt(this.dataCategoryId),
                siteInfo:(typeof this.dataSiteInfo == "undefined" || this.dataSiteInfo == "") ? [] : JSON.parse(this.dataSiteInfo),
                hookInfo:(typeof this.dataHookInfo == "undefined" || this.dataHookInfo == "") ? [] : JSON.parse(this.dataHookInfo),
                allModules:(typeof this.dataAllModules == "undefined" || this.dataAllModules == "") ? [] : JSON.parse(this.dataAllModules),
                categoryModules:(typeof this.dataCategoryModules == "undefined" || this.dataCategoryModules == "") ? [] : JSON.parse(this.dataCategoryModules),
                categoryInfo:(typeof this.dataCategoryInfo == "undefined" || this.dataCategoryInfo == "") ? [] : JSON.parse(this.dataCategoryInfo),
                themeInfo:(typeof this.dataThemeInfo == "undefined" || this.dataThemeInfo == "") ? [] : JSON.parse(this.dataThemeInfo),
                hooks:[],
                hooksInfoCache:{},
                moduleInfoCache:{},
                noHookFound:false,
                errors:[],
                enableSave:false,
                searchKey:'',
                applicableForAllTenants:false,
                fromData:{site_id:0, microsite_id:0, tenant_id:0, category_id:0},
                toData:{site_id:0, microsite_id:0, tenant_id:0, category_id:0},
                isWorking:false,
                sortObj:{draggable:null},
                userRights:(this.dataUserRights ? JSON.parse(this.dataUserRights) : []),
                isModuleReadonly: (this.dataIsModuleReadonly == "1") ? true : false,
                allSites:(typeof this.dataAllSites == "undefined" || this.dataAllSites == "") ? [] : JSON.parse(this.dataAllSites)
            }
        },
        methods: {
            init() {

                if(this.categories.length == 0) {
                    this.addErrorMessage("No category found!");
                }
                if(this.categoryInfo.length === 0) {
                    this.addErrorMessage("There is a mismatch in site default category. Please fix that.");
                }
                //console.log('this.themeInfo["id"] ', this.themeInfo["id"])
                if(this.hasTheme) {
                    this.parseTheme();
                    this.populateModules();
                    this.enableSorting();
                    this.makeCategories();
                } else {
                    let path = AdminConfig.admin_path("category/settings", {tenant_id: this.tenantId});
                    this.addErrorMessage(`This category/tenant/theme is not available in category_site table.
                         You need to drag and drop in category <a href='${path}'>settings</a>.`);
                }

            },
            makeCategories() {
                let totalSites = this.allSites.length;
                if (totalSites > 0) {
                    for(let i=0;i<totalSites;i++) {
                        let current = this.allSites[i];
                        this.categoriesData[current.id] = current.category;
                    }
                }
                this.setDefaultFromTo();

            },
            setDefaultFromTo() {
                let totalSites = this.allSites.length;
                if (totalSites > 0) {
                    this.fromData.site_id =  this.allSites[0].id;
                    this.toData.site_id = this.siteId; //current site id
                } else {
                    this.fromData.site_id = this.siteId; //current site
                    this.toData.site_id = this.siteId;
                }

                this.fromData.tenant_id = this.tenantId;
                this.toData.tenant_id = this.tenantId;
                this.toData.category_id = this.categoryId;
            },
            filterModules() {

                let key = this.searchKey;
                if(key !== "" && key != null) {

                    key = key.toLowerCase();

                    return this.allModules.filter(function(current) {
                        let alias = current.alias.toLowerCase();
                        let name = current.name.toLowerCase();
                        let id = current.id;

                        if(id.toString() === key || alias.includes(key) || name.includes(key)) {
                            return true;
                        }
                    });


                } else {
                    this.searchKey = '';
                    // console.log("data", this.allData.data);
                    return this.allModules;
                }
            },
            saveNow(url, data) {

                return new Promise((resolve, reject) => {
                    axios.post(url, data)
                        .then(response => {
                            resolve(response);
                        }).catch(error => {
                            reject(error.response);
                    });
                });

            },
            getWhere() {
                let where =  {
                    site_id:this.siteId,
                    microsite_id:this.microSiteId,
                    tenant_id:this.tenantId,
                    category_id:this.categoryId
                };
                return where;
            },
            saveModules() {
                if(this.enableDisableSave() === false) {
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
                    if(allModules.length > 0) {
                        let modules = [];
                        for(let i=0;i<allModules.length;i++) {
                            //console.log(allModules[i]);
                            let currentModule = allModules[i];
                            let module_id = currentModule.getAttribute("data-module-id");
                            modules.push({module_id:module_id, position:i+1});
                        }

                        let data = {hook_id:hook_id, modules: modules};
                        datas.push(data);
                    }
                    //console.log(datas);
                });

                if(datas.length > 0) {

                    let where = $this.getWhere();
                    postParams.data = datas;
                    postParams.where = where;
                    postParams.applicableForAllTenants = this.applicableForAllTenants;
                    this.saveNow(AdminConfig.admin_path("homepage/saveSettings"), postParams).then(function (res) {
                        //console.log(res);
                        Toast.show($this, "Saved...");
                        $this.enableDisableSave(false);

                    }).catch(function(res) {
                        $this.showError(res);
                    });
                }

            },
            populateModules() {
                //Display module on init
                let $this = this;
                let modules = this.categoryModules;

                if(modules.length > 0) {
                    for(let i=0;i<modules.length;i++) {
                        let current = modules[i];
                        let hook = $this.getHookInfoCache(current.hook_id);
                        try{
                            let moduleInfo = $this.getModuleInfo(current.module_id);
                            this.addModule(hook.hookIndex, moduleInfo);
                        } catch (e) {
                            console.error("@populateModules: "+e.message);
                            $this.showError("@populateModules: "+e.message);
                        }

                    }
                } else {
                    console.info("There is no module added for this category.");
                }

            },
            parseTheme() {
                //console.log("this.themeInfo.length ",this.themeInfo);
                if(this.hasTheme) {
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
                this.errors.push({message:message});
            },
            findModuleIndex(hookIndex, module) {

                if(this.getHook(hookIndex) !== null) {
                    let modules = this.getHook(hookIndex).modules;
                    let found = modules.findIndex(function (current) {
                        return module.id == current.id;
                    });
                    return found;
                };
                return -1;
            },
            removeModules(idOrWhere, byType="module_id", hookId=null) {
                let $this = this;
                let where;
                if(Object.prototype.toString.call(idOrWhere)  === "[object Object]") {
                  where = idOrWhere; // where is passed by param
                } else {
                    where = this.getWhere();
                    where[byType] = parseInt(idOrWhere);
                };

                //hook and module
                if(hookId !== null) {
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
                postParams.applicableForAllTenants = this.applicableForAllTenants;
                return this.saveNow(AdminConfig.admin_path("homepage/removeModules"), postParams).then(function (res) {
                    console.log(res);
                    //Toast.show($this, res.message);
                    Toast.show($this, "Removed...");

                }).catch(function(res) {
                    $this.showError(res);
                });
            },
            addModule(hookIndex, module) {
                //console.log("1: addModule ", hookIndex, module);
                let found = this.findModuleIndex(hookIndex, module);

                if(found === -1 ){
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
            highlightEagerDrop(addRemove="add") {
                let js_hook_modules = document.querySelectorAll(".js_hook_modules");
                js_hook_modules.forEach(function(current) {
                    current.classList[addRemove]("module-drop-eager");
                })

            },
            setHookInfoCache(hookId, data) {
                return this.hooksInfoCache[hookId] = data;
            },
            getHookInfoCache(hookId) {
                return this.hooksInfoCache[hookId];
            },
            getHookInfo(hookValue, byKey="alias") {
                let hooks = this.hookInfo;

                for(let i=0;i<hooks.length;i++) {
                    let current = hooks[i];
                    if(current[byKey] == hookValue) {
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
                if(this.moduleInfoCache[module_id]) {
                    return this.moduleInfoCache[module_id];
                }
                let found = null;
                for(let i=0; i<this.allModules.length;i++) {
                    let current = this.allModules[i];
                    if(current.id.toString() === module_id.toString()) {
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
                this.sortObj.draggable =  Sortable.create(draggableModules, {
                    animation:200,
                    draggable: ".js_item",
                    group: {
                        name: 'modulesBox',
                        pull: 'clone',
                        revertClone:true
                    },
                    sort:false,
                    ghostClass: "js_modules",
                    onEnd: function (/**Event*/evt) {

                        let to = evt.to;
                        let item = evt.item; // dragged HTMLElement

                        let hookId = to.getAttribute("data-hook-id");
                        let moduleId = item.getAttribute("data-module-id");

                        if(hookId) {
                            let hookInfo = $this.getHookInfoCache(hookId);
                            //console.log(hookInfo);
                            let moduleInfo = $this.getModuleInfo(moduleId);

                            let isAdded = $this.addModule(hookInfo.hookIndex, moduleInfo);


                            //remove original dropped, because it is populated by reactive hook->modules
                            item.parentNode.removeChild(item);

                            $this.highlightEagerDrop("remove");

                            //we can send feedback
                            if(isAdded === false) {
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
                            animation:200,
                            filter: ".js_delete",
                            group: {
                                name: 'droppable',
                                put: 'modulesBox',
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
                                console.log("@onUpdate: ", evt);
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
                return (typeof hook.modules !="undefined" && hook.modules.length > 0);
            },
            enableDisableSave(val) {
                if(val === undefined) {
                    return this.enableSave;
                };
                this.enableSave = val;
            },
            showHideDeleteAlert(isShow) {
                if(isShow) {
                    Modal.open(this, "deleteBox");
                } else {
                    Modal.close(this, "deleteBox");
                }

            },
            deleteAllFromCategory() {
                this.showHideDeleteAlert(false);

                let allHooks = this.hooks;
                for(let i=0;i<allHooks.length;i++) {
                    allHooks[i].modules = [];
                };
                this.removeModules(this.categoryId, "category_id");
            },
            showHideCopyAlert(isShow=true) {
                if(isShow) {

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
                this.saveNow(url, data).then(function(res) {
                    console.log(res)
                    if(res.data.error) {
                        Toast.show($this, res.data.message, 5000);
                    } else if(res.data.success) {

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
                if(isValid === false)  {
                    console.error("There is some error in db entries for this category. " +
                        "This can be fixed by deleting all modules and drag and drop again.");
                }
                return isValid;
            },
            fetchNewData() {
                let where = this.getWhere();
                delete where.site_id;
                if(where.microsite_id === 0) {
                    delete where.microsite_id;
                };
                let url = "homepage/ui";
                window.location.href = AdminConfig.admin_path(url, where);
            },
            showInfo(type, id) {
                this.$refs.infoPopup.showInfo(type, id);
            },
            showHideHookPanel(id) {
                let target = this.$refs["hook_panel_"+id][0];
                if(target.className.indexOf("hide") === -1) {
                    target.classList.add("hide");
                } else {
                    target.classList.remove("hide");
                }

            },
            onlyMe(id, evt) {

                let $this = this;

                let target = this.$refs["panel_"+id][0];
                if(target.className.indexOf("fixed") === -1) {
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
                    for(let i=0;i<$this.hooks.length;i++) {
                        let current = $this.hooks[i];
                        let current_ele = $this.$refs["panel_"+current.info.id][0];

                        if(shouldHide === true) {
                            current_ele.classList.add("hide");
                            current_ele.classList.remove("fixed");
                        } else {
                            current_ele.classList.remove("hide");
                            current_ele.classList.remove("fixed");
                        }
                    }

                    if(shouldHide === true) {
                        savePanel.classList.add("hide");
                    } else {
                        savePanel.classList.remove("hide");
                    }

                }
            },
            showError(response) {
                let message = '';
                if(typeof response == "string") {
                    message = response;
                } else {
                    message = (response && response["data"] && response["data"]["message"]) ? response.data.message : "Unknown error!";
                }
                Toast.show(this, message, 5000);
            }


        }
    }

</script>
