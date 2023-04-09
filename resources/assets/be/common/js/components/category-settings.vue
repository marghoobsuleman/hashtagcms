<template>
    <div>
        <div v-show="hasMicrosites || hasPlatformsMoreThanOne || hasPlatformsMoreThanOne" class="row">
            <div v-show="hasMicrosites" class="col-auto">
                <select v-model="micrositeId" class="form-select select" @change="fetchNewData()">
                    <option value="0">Select a MicroSite</option>
                    <option v-for="microsite in siteMicrosites" :value="microsite.id">{{ microsite.name }}</option>
                </select>
            </div>
            <div class="col-auto">
                <select v-show="hasPlatformsMoreThanOne" v-model="platformId" class="form-select select"
                        @change="fetchNewData()">
                    <option>Select a Platform</option>
                    <option v-for="platform in sitePlatforms" :value="platform.id">{{ platform.name }}</option>
                </select>
            </div>
            <div class="col-auto">
                <label v-show="hasPlatformsMoreThanOne"
                       title="It will be inserted or deleted for all platforms if checks.">
                    <input v-model="applicableForAllPlatforms" name="applicableForAll" type="checkbox"/>
                    Enable add/delete for all platforms</label>
            </div>
        </div>
        <div class="row mt-3" v-if="!canEdit">
            <div class="col-auto">
                <p class="alert alert-warning">You have read only access.</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-4">
                <div class="panel panel-success shadow border">
                    <div class="panel-heading p-4">
                        <input class="hide" type="checkbox" @click="selectAllData('allCategories', $event.target)"/>
                        Drop in below panel to enable the category for this site.
                        <button v-if="showUpdateAllBtn" aria-label="More Info"
                                class="btn btn-default js_info icon-btn pull-right"
                                style="top: -7px; position: relative; right: -9px;" title="Update in all categories"
                                type="button"
                                @click="showUpdateAllDiv = !showUpdateAllDiv">
                            <i aria-hidden="true" class="fa fa-ellipsis-v js_info"></i>
                        </button>
                    </div>
                    <div v-if="showUpdateAllDiv" class="panel-body p-3" style="background-color:#f3f3f3">
                        <p class="text-danger">Be careful while using this.
                            Changing this dropdown will effect to all categories.
                        </p>
                        <select v-model="globalTheme" class="form-select margin-bottom-05"
                                @change="updateThemeToAllCategories()">
                            <option value="">Select a Theme</option>
                            <option v-for="theme in siteThemes" :value="theme.id">{{ theme.name }}</option>
                        </select>
                    </div>
                    <div v-if="showSearch('allCategories')" class="panel-sub-heading" style="padding:4px">
                        <div class="input-group">
                            <span class="input-group-addon">Search</span>
                            <input v-model="searchKeyPlatformCategory" aria-describedby="basic-addon3"
                                   class="form-control"
                                   placeholder="Search by id or name" type="text">
                        </div>
                    </div>
                    <ul ref="droppableArea" class="list-group category-items js_category" style="min-height: 200px">
                        <li v-for="platformCategory in filterData('allCategories')"
                            :data-cache-category="platformCategory.cache_category"
                            :data-category-id="platformCategory.category_id"
                            :data-theme-id="platformCategory.theme_id"
                            class="list-group-item js_item"
                            @click="setCurrentSelection($event)">
                            <input v-show="showCheckbox" v-model="platformCategory.selected" type="checkbox"/>
                            {{ platformCategory.category_name }}
                            <small v-show="platformCategory.theme_id > 0" class="text-info" title="Theme"> -
                                {{ getThemeName(platformCategory.theme_id) }}
                            </small>
                            <small v-show="platformCategory.theme_id <= 0" class="text-danger"> <br/>Theme is
                                missing</small>
                            <span class="btn-toolbar pull-right toolbar-category" role="toolbar">
                                <span class="btn-group">
                                    <button v-if="canDelete" aria-label="Delete from this platform"
                                            class="btn btn-default icon-btn js_delete"
                                            title="Delete from this platform"
                                            type="button">
                                        <span aria-hidden="true" class="fa fa-trash-o js_delete"></span>
                                    </button>
                                    <button v-if="canEdit" aria-label="More Info" class="btn btn-default js_info icon-btn"
                                            title="More Info"
                                            type="button">
                                        <i aria-hidden="true" class="fa fa-ellipsis-v js_info"></i>
                                    </button>
                                    </span>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-3">
                <div class="panel panel-info margin-top-20">
                    <div class="panel-heading p-4">
                        Drag and drop from here
                    </div>
                    <div v-if="showSearch('allSiteCategories')" class="panel-sub-heading" style="padding:4px">
                        <div class="input-group">
                            <span class="input-group-addon">Search</span>
                            <input v-model="searchKeyCategory" aria-describedby="basic-addon3" class="form-control"
                                   placeholder="Search by id or name" type="text">
                        </div>
                    </div>
                    <ul id="draggableItems" class="list-group">
                        <li v-for="category in filterData('allSiteCategories')" :data-category-id="category.category_id"
                            class="list-group-item js_item">
                            {{ category.category_name }}
                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <div ref="popover" class="popover hide bs-popover-end shadow" data-popper-placement="right" role="tooltip">
            <p class="popover-header">Set theme etc for this category  <span aria-label="Close"
                                                            class="pull-right fa fa-close close"
                                                            title="Close"
                                                            @click="closePopup()"></span></p>
            <div class="popover-body">
                <select v-model="currentSelection.theme_id" class="form-select mb-2">
                    <option value="">Select a Theme</option>
                    <option v-for="theme in siteThemes" :value="theme.id">{{ theme.name }}</option>
                </select>
                <input v-model="currentSelection.cache_category" class="form-control mb-2"
                       name="cache_category"
                       placeholder="Enter cache category" type="text" />
                <div class="text-center">
                    <button class="btn btn-primary text-center" @click="setThemeEtc()">Save Settings</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import Sortable from 'sortablejs';
import {Toast} from '../helpers/common';

export default {

    mounted() {
        this.init();

    },
    props: [
        'dataSiteId',
        'dataSitePlatforms',
        'dataSiteCategories',
        'dataSiteThemes',
        'dataSiteMicrosites',
        'dataPlatformId',
        'dataMicrositeId',
        'dataCategories',
        'dataUserRights'
    ],
    data() {
        return {
            checkAll: false,
            showCheckbox: false,
            sitePlatforms: (typeof this.dataSitePlatforms === "undefined" || this.dataSitePlatforms === "") ? [] : JSON.parse(this.dataSitePlatforms),
            siteCategories: (typeof this.dataSiteCategories === "undefined" || this.dataSiteCategories === "") ? [] : JSON.parse(this.dataSiteCategories),
            siteThemes: (typeof this.dataSiteThemes === "undefined" || this.dataSiteThemes === "") ? [] : JSON.parse(this.dataSiteThemes),
            siteMicrosites: (typeof this.dataSiteMicrosites === "undefined" || this.dataSiteMicrosites === "") ? [] : JSON.parse(this.dataSiteMicrosites),
            categories: (typeof this.dataCategories === "undefined" || this.dataCategories === "") ? [] : JSON.parse(this.dataCategories),
            platformId: (typeof this.dataPlatformId === "undefined" || this.dataPlatformId === "") ? 1 : parseInt(this.dataPlatformId),
            micrositeId: (typeof this.dataMicrositeId === "undefined" || this.dataMicrositeId === "") ? 0 : parseInt(this.dataMicrositeId),
            searchKeyPlatformCategory: '',
            searchKeyCategory: '',
            applicableForAllPlatforms: false,
            categoryId: 0,
            currentSelection: {category_id: 0, theme_id: "", cache_category: ""},
            allCategories: [],
            allCategoriesInfo: {},
            allSiteCategories: [],
            siteId: (typeof this.dataSiteId === "undefined") ? 1 : parseInt(this.dataSiteId),
            showUpdateAllBtn: true,
            showUpdateAllDiv: false,
            globalTheme: "",
            userRights:(this.dataUserRights ? JSON.parse(this.dataUserRights) : [])
        }
    },
    computed: {
        hasPlatformsMoreThanOne() {
            return this.sitePlatforms.length > 1;
        },
        hasMicrosites() {
            return this.siteMicrosites.length > 0;
        },
        canEdit() {
            return this.userRights.indexOf("edit") >=0;
        },
        canDelete() {
            return this.userRights.indexOf("delete") >=0;
        },
    },
    methods: {
        init() {
            this.makeData();
            if (this.canEdit) {
                this.enableSorting();
            }

        },
        selectAllData(findIn = "allCategories", holder) {
            let $this = this;
            let whereArr = (findIn === "allCategories") ? this.allCategories : this.allSiteCategories;
            let selected = holder.checked;
            whereArr.forEach(function (current) {
                $this.$set(current, "selected", selected);
            })
        },
        makeData() {
            let $this = this;
            //this.allCategories = this.categories;
            let categories = this.categories;
            let selected = false;
            if (categories.length > 0) {
                categories.forEach(function (current) {

                    let category_id = current.category_id;
                    let category_name = current.lang.name || current.category_name;
                    let theme_id = current.theme_id;
                    let cache_category = current.cache_category;
                    let obj = {category_id, category_name, theme_id, cache_category, selected};
                    $this.allCategories.push(obj);
                    $this.allCategoriesInfo[category_id.toString()] = obj;
                });
            }
            //Site Categories
            categories = this.siteCategories;
            //console.log("categories ", categories);
            if (categories.length > 0) {
                categories.forEach(function (current) {
                    let category_id = current.category_id;
                    let category_name = current.name;
                    let theme_id = ""
                    let cache_category = "";
                    let obj = {category_id, category_name, theme_id, cache_category, selected};
                    $this.allSiteCategories.push(obj);
                });
            }

            //console.log($this.allSiteCategories);
        },
        getWhere() {
            let where = {
                microsite_id: this.micrositeId,
                platform_id: this.platformId,
                category_id: this.categoryId,
                site_id: this.siteId
            };
            return where;
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
        updateIndex() {
            let $this = this;
            let allCategories = document.querySelectorAll(".js_category li");
            let platformId = this.platformId;
            let datas = [];
            for (let i = 0; i < allCategories.length; i++) {
                let current = allCategories[i];
                //console.log("current ", current);
                let categoryInfo = this.getCategoryInfo(current.getAttribute("data-category-id"), "allCategories");
                //console.log("categoryInfo ", categoryInfo);
                let data = {position: (i + 1)};
                let where = {category_id: categoryInfo.category_id, platform_id: platformId}; //microsite_id:
                datas.push({where, data});
            }

            if (datas.length > 0) {
                let url = AdminConfig.admin_path("category/updateIndex");
                let postParams = {};
                postParams.data = datas;
                //postParams.applicableForAllPlatforms = $this.applicableForAllPlatforms;
                this.saveNow(url, postParams).then(function (res) {
                    //console.log(res);
                    Toast.show($this, "Saved...");

                }).catch(function (res) {
                    Toast.show($this, res.data.message, 2000);
                });
            }

        },
        showSearch(findIn = "allCategories") {
            let whereArr = (findIn === "allCategories") ? this.allCategories : this.allSiteCategories;
            return whereArr.length > 10;
        },
        filterData(findIn = "allCategories") {
            let key = (findIn === "allCategories") ? this.searchKeyPlatformCategory : this.searchKeyCategory;
            let whereArr = (findIn === "allCategories") ? this.allCategories : this.allSiteCategories;
            if (key !== "" && key != null) {

                key = key.toLowerCase();

                return whereArr.filter(function (current) {
                    let name = current.category_name.toLowerCase();
                    let id = current.category_id;
                    if (id.toString() === key.toString() || name.includes(key)) {
                        return current;
                    }
                });

            } else {
                // console.log("data", this.allData.data);
                return (findIn === "allCategories") ? this.allCategories : this.allSiteCategories;
            }
        },
        setCurrentSelection(evt) {
            let target = evt.currentTarget;
            let span = evt.target;

            let category_id = target.getAttribute("data-category-id");
            let theme_id = target.getAttribute("data-theme-id");

            let categoryInfo = this.getCategoryInfo(category_id, "allCategories");

            this.currentSelection.theme_id = theme_id;
            this.currentSelection.category_id = category_id;
            this.currentSelection.cache_category = categoryInfo.cache_category;

            let action;
            if (span.classList.contains("js_info")) {
                action = "info";
                showInfo(target, this.$refs.popover);
            }
            if (span.classList.contains("js_delete")) {
                action = "delete";
            }


            // console.log(this.currentSelection);

            function showInfo(source, target, where = "center-right") {
                target.classList.remove(...["animated", "fadeOut", "hide"]);
                let src = source;
                let tgt = target;
                let srcOpt = src.getBoundingClientRect();
                let tgtOpt = tgt.getBoundingClientRect();

                let left = (srcOpt.left + srcOpt.width);
                let top = (srcOpt.top + srcOpt.height) - (tgtOpt.height / 2);

                target.classList.add(...["animated", "jello"]);

                switch (where) {
                    case "center-right":
                        tgt.style.position = "absolute";
                        tgt.style.left = left + "px";
                        tgt.style.top = top + "px";
                        break;
                }
            }

        },
        closePopup() {
            this.$refs.popover.classList.add(...["animated", "fadeOut", "hide"]);
            setTimeout(()=> {

            });
        },
        fetchNewData() {
            let where = this.getWhere();
            delete where.category_id;
            if (where.microsite_id === 0) {
                delete where.microsite_id;
            }
            let url = "category/settings";
            window.location.href = AdminConfig.admin_path(url, where);
        },
        enableSorting() {
            let $this = this;
            this.$nextTick(function () {
                let list = document.querySelectorAll(".js_category");
                list.forEach(function (current) {
                    Sortable.create(current, {
                        animation: 200,
                        filter: ".js_delete",
                        group: {
                            name: 'droppable',
                            put: 'modulesBox',
                        },
                        onFilter: function (evt) {
                            let item = evt.item,
                                ctrl = evt.target;
                            let id = item.getAttribute("data-category-id");

                            if (Sortable.utils.is(ctrl, ".js_delete")) {  // Click on remove button
                                $this.removeCategory(id);
                            }
                        },
                        onUpdate: function (/**Event*/evt) {
                            //console.log("onUpdate ", evt);
                            $this.updateIndex();
                        }
                    })
                })

            });

            //Right Side module
            let draggableModules = document.getElementById("draggableItems");

            Sortable.create(draggableModules, {
                animation: 200,
                draggable: ".js_item",
                group: {
                    name: 'modulesBox',
                    pull: "clone",
                    revertClone: true
                },
                sort: false,
                ghostClass: ".js_category",
                onEnd: function (/**Event*/evt) {

                    let to = evt.to;
                    let item = evt.item; // dragged HTMLElement

                    let categoryId = item.getAttribute("data-category-id");

                    if (categoryId) {
                        let categoryInfo = $this.getCategoryInfo(categoryId, "allSiteCategories");

                        //console.log("onEnd: categoryInfo ",categoryInfo);

                        let isAdded;
                        if (categoryInfo !== null) {
                            isAdded = $this.addCategory(categoryInfo);
                        }

                        item.parentNode.removeChild(item);

                        $this.highlightEagerDrop("remove");

                        //we can send feedback
                        if (isAdded === false) {
                            Toast.show($this, "Category is already added in this platform...");
                        }
                    }

                },
                onStart: function (/**Event*/evt) {

                    $this.highlightEagerDrop("add");
                }
            });
        },
        highlightEagerDrop(addRemove = "add") {
            let js_hook_modules = document.querySelectorAll(".js_category");
            js_hook_modules.forEach(function (current) {
                current.classList[addRemove]("module-drop-eager");
            });
        },
        getCategoryInfo(categoryId, findIn = "allSiteCategories") {
            let found = null;
            let whereArr = (findIn === "allSiteCategories") ? this.allSiteCategories : this.allCategories;

            for (let i = 0; i < whereArr.length; i++) {
                let current = whereArr[i];
                if (parseInt(current.category_id) === parseInt(categoryId)) {
                    found = current;
                    break;
                }
            }
            return found;
        },
        findIndex(categoryId, findIn = "allCategories") {
            //allCategories is left site
            let index = -1;
            let whereArr = (findIn === "allCategories") ? this.allCategories : this.allSiteCategories;
            if (whereArr.length > 0) {
                for (let i = 0; i < whereArr.length; i++) {
                    let current = whereArr[i];
                    if (parseInt(current.category_id) === parseInt(categoryId)) {
                        index = i;
                        break;
                    }
                    ;
                }
                ;
            }
            ;
            return index;
        },
        addCategory(category) {
            let $this = this;
            let index = this.findIndex(category.category_id);
            let isAdded = false;
            if (index === -1) {
                //add in info
                this.allCategoriesInfo[category.category_id.toString()] = {index: index, info: category};

                //save in db
                setTimeout(function () {
                    updateInDB();
                }, 1);

                isAdded = this.allCategories.push(category);

                //console.log("isAdded 2 ",isAdded);

            }

            return isAdded;


            function updateInDB() {
                let url = AdminConfig.admin_path("category/insertCategory");
                let where = $this.getWhere();
                let postParams = {};
                delete category["name"];
                category.platform_id = where.platform_id;
                postParams.data = {
                    category_id: category.category_id,
                    platform_id: where.platform_id,
                    site_id: where.site_id
                };
                postParams.applicableForAllPlatforms = $this.applicableForAllPlatforms;
                $this.saveNow(url, postParams).then(function (res) {
                    Toast.show($this, "Saved...");

                }).catch(function (res) {
                    Toast.show($this, res.data.message, 2000);
                });
            }

        },
        setThemeEtc() {
            let $this = this;
            let current = this.currentSelection;
            //set them in array
            let index = this.findIndex(current.category_id);

            if (index !== -1) {

                this.allCategories[index].theme_id = current.theme_id;
                this.allCategories[index].cache_category = current.cache_category;
                this.categoryId = parseInt(current.category_id);

                let postParams = {};
                postParams.where = this.getWhere();
                postParams.data = {
                    cache_category: (current.cache_category) || "",
                    theme_id: current.theme_id
                };
                postParams.applicableForAllPlatforms = $this.applicableForAllPlatforms;

                let url = AdminConfig.admin_path("category/updateThemeAndEtc");
                this.saveNow(url, postParams).then(function (res) {
                    Toast.show($this, "Saved...");

                }).catch(function (res) {
                    Toast.show($this, res.data.message, 2000);
                });

            }
            this.closePopup();
        },
        removeCategory(id) {
            let $this = this;
            let index = this.findIndex(id);
            if (index !== -1) {
                let categoryInfo = this.allCategories.splice(index, 1);
                removeNow(categoryInfo[0]);
                return categoryInfo;
            }


            function removeNow(categoryInfo) {
                let url = AdminConfig.admin_path("category/deleteCategory");
                let postParams = {};
                let where = $this.getWhere();

                postParams.where = {
                    category_id: categoryInfo.category_id,
                    microsite_id: where.microsite_id
                };

                if (!$this.applicableForAllPlatforms) {
                    postParams.where.platform_id = where.platform_id;
                }
                $this.saveNow(url, postParams).then(function (res) {

                    Toast.show($this, "Saved...");

                }).catch(function (res) {
                    Toast.show($this, res.data.message, 2000);
                });
            }

        },
        getThemeName(id) {
            for (let i = 0; i < this.siteThemes.length; i++) {
                if (this.siteThemes[i].id == id) {
                    return this.siteThemes[i].name;
                }
            }
            return "";
        },
        updateThemeToAllCategories() {
            let $this = this;
            let url = AdminConfig.admin_path("category/updateThemeForAllCategories");
            let categories = this.filterData('allCategories');

            if (this.globalTheme !== "") {
                if (categories.length > 0) {
                    let where = this.getWhere();
                    delete where.category_id;
                    delete where.microsite_id;

                    let postParams = {
                        data: {theme_id: this.globalTheme},
                        where: where
                    };
                    Toast.show($this, "Please wait...");
                    this.saveNow(url, postParams).then(function (res) {
                        //console.log(res);
                        if (res.data["error"]) {
                            Toast.show($this, res.data.message, 5000);
                        } else {
                            Toast.show($this, "Saved");
                            for (let i = 0; i < categories.length; i++) {
                                categories[i].theme_id = $this.globalTheme;
                                //console.log(categories[i]);
                            }

                        }

                    }).catch(function (res) {
                        Toast.show($this, res.data.message, 2000);
                    });
                }
            }
        }
    }
}

</script>
