<template>
    <div>
        <div v-show="hasMicrosites || hasTenantsMoreThanOne || hasTenantsMoreThanOne" class="btn-group margin-top-05 homepage-toolbar" role="group" style="display: inherit">
            <select v-show="hasMicrosites" class="form-control select" v-model="micrositeId" @change="fetchNewData()">
                <option value="0">Select a MicroSite</option>
                <option v-for="microsite in siteMicrosites" :value="microsite.id">{{microsite.name}}</option>
            </select>
            <select v-show="hasTenantsMoreThanOne" class="form-control select" v-model="tenantId" @change="fetchNewData()">
                <option>Select a Tenant</option>
                <option v-for="tenant in siteTenants" :value="tenant.id">{{tenant.name}}</option>
            </select>
            <label class="hide" v-show="hasTenantsMoreThanOne" title="It will be inserted or deleted for all tenants if checks."><input type="checkbox" v-model="applicableForAllTenants" /> Effective for all tenants</label>
        </div>

        <div class="col-md-12">
            <div class="col-md-5">
                <div class="panel panel-success margin-top-20">
                    <div class="panel-heading">
                        <input class="hide" type="checkbox" @click="selectAllData('allCategories', $event.target)"  />
                        Drop in this panel to add in category site
                        <button @click="showUpdateAllDiv = !showUpdateAllDiv"  v-if="showUpdateAllBtn" title="Update in all categories" type="button" aria-label="More Info" class="btn btn-default js_info icon-btn pull-right" style="top: -7px; position: relative; right: -9px;">
                            <i aria-hidden="true" class="fa fa-ellipsis-v js_info"></i>
                        </button>
                    </div>
                    <div v-if="showUpdateAllDiv" class="panel-body" style="background-color:#f3f3f3">
                        <h4 class="text-danger">Be careful while using this.
                            Changing this dropdown will effect to all categories.
                        </h4>
                        <select class="form-control margin-bottom-05" v-model="globalTheme" @change="updateThemeToAllCategories()">
                            <option value="">Select a Theme</option>
                            <option v-for="theme in siteThemes" :value="theme.id">{{theme.name}}</option>
                        </select>
                    </div>
                    <div class="panel-sub-heading" style="padding:4px" v-if="showSearch('allCategories')">
                        <div class="input-group">
                            <span class="input-group-addon">Search</span>
                            <input type="text" v-model="searchKeyTenantCategory"  class="form-control" aria-describedby="basic-addon3" placeholder="Search by id or name">
                        </div>
                    </div>
                    <ul class="list-group category-items js_category" style="min-height: 200px" ref="droppableArea">
                        <li class="list-group-item js_item" v-for="tenantCategory in filterData('allCategories')"
                            :data-category-id="tenantCategory.category_id"
                            :data-theme-id="tenantCategory.theme_id"
                            :data-cache-category="tenantCategory.cache_category"
                            @click="setCurrentSelection($event)">
                           <input v-show="showCheckbox" type="checkbox" v-model="tenantCategory.selected" />
                            {{tenantCategory.category_name}}
                            <small class="text-info" title="Theme" v-show="tenantCategory.theme_id > 0"> - {{getThemeName(tenantCategory.theme_id)}}
                            </small>
                            <small class="text-danger" v-show="tenantCategory.theme_id <= 0"> <br />Theme is missing</small>
                            <span role="toolbar" class="btn-toolbar pull-right toolbar-category">
                                <span class="btn-group">
                                    <button title="Delete from this tenant" type="button" aria-label="Delete from this tenant" class="btn btn-default icon-btn js_delete">
                                        <span aria-hidden="true" class="fa fa-trash-o js_delete"></span>
                                    </button>
                                    <button title="More Info" type="button" aria-label="More Info" class="btn btn-default js_info icon-btn">
                                        <i aria-hidden="true" class="fa fa-ellipsis-v js_info"></i>
                                    </button>
                                    </span>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-5">
                <div class="panel panel-info margin-top-20">
                    <div class="panel-heading">
                        Drag and drop from here
                    </div>
                    <div class="panel-sub-heading" style="padding:4px" v-if="showSearch('allSiteCategories')">
                        <div class="input-group">
                            <span class="input-group-addon">Search</span>
                            <input type="text" v-model="searchKeyCategory"  class="form-control" aria-describedby="basic-addon3" placeholder="Search by id or name">
                        </div>
                    </div>
                    <ul class="list-group" id="draggableItems">
                        <li class="list-group-item js_item" v-for="category in filterData('allSiteCategories')" :data-category-id="category.category_id">
                            {{category.category_name}}
                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <div class="popover right" ref="popover">
            <div class="arrow"></div>
            <h3 class="popover-title">Set theme etc for this category <span class="pull-right fa fa-close hand" title="Close" aria-label="Close" @click="closePopup()"></span> </h3>
            <div class="popover-content">
                <select class="form-control margin-bottom-05" v-model="currentSelection.theme_id">
                    <option value="">Select a Theme</option>
                    <option v-for="theme in siteThemes" :value="theme.id">{{theme.name}}</option>
                </select>
                <input v-model="currentSelection.cache_category" class="form-control margin-bottom-05" type="text" name="cache_category" value="" placeholder="Enter cache category" />
                <div class="text-center">
                    <button class="btn btn-primary text-center" @click="setThemeEtc()">Save Settings</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import Sortable from 'sortablejs';
    import {Toast} from '../helpers/Common';

    export default {

        mounted() {
            this.init();

        },
        props: [
            'dataSiteId',
            'dataSiteTenants',
            'dataSiteCategories',
            'dataSiteThemes',
            'dataSiteMicrosites',
            'dataTenantId',
            'dataMicrositeId',
            'dataCategories'
        ],
        data() {
            return {
                checkAll:false,
                showCheckbox:false,
                siteTenants:(typeof this.dataSiteTenants === "undefined" || this.dataSiteTenants === "") ? [] : JSON.parse(this.dataSiteTenants),
                siteCategories:(typeof this.dataSiteCategories === "undefined" || this.dataSiteCategories === "") ? [] : JSON.parse(this.dataSiteCategories),
                siteThemes:(typeof this.dataSiteThemes === "undefined" || this.dataSiteThemes === "") ? [] : JSON.parse(this.dataSiteThemes),
                siteMicrosites:(typeof this.dataSiteMicrosites === "undefined" || this.dataSiteMicrosites === "") ? [] : JSON.parse(this.dataSiteMicrosites),
                categories:(typeof this.dataCategories === "undefined" || this.dataCategories === "") ? [] : JSON.parse(this.dataCategories),
                tenantId:(typeof this.dataTenantId === "undefined" || this.dataTenantId === "") ? 1 : parseInt(this.dataTenantId),
                micrositeId:(typeof this.dataMicrositeId === "undefined" || this.dataMicrositeId === "") ? 0 : parseInt(this.dataMicrositeId),
                searchKeyTenantCategory:'',
                searchKeyCategory:'',
                applicableForAllTenants:false,
                categoryId:0,
                currentSelection: {category_id: 0, theme_id:"", cache_category:""},
                allCategories:[],
                allCategoriesInfo:{},
                allSiteCategories:[],
                siteId:(typeof this.dataSiteId === "undefined") ? 1 : parseInt(this.dataSiteId),
                showUpdateAllBtn:true,
                showUpdateAllDiv:false,
                globalTheme:""
            }
        },
        computed: {
            hasTenantsMoreThanOne() {
                return this.siteTenants.length > 1;
            },
            hasMicrosites() {
                return this.siteMicrosites.length > 0;
            }
        },
        methods: {
            init() {
              this.makeData();
              this.enableSorting();
            },
            selectAllData(findIn="allCategories", holder) {
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
                if(categories.length > 0) {
                  categories.forEach(function (current) {

                      let category_id = current.category_id;
                      let category_name = current.lang.name || current.category_name;
                      let theme_id = current.theme_id;
                      let cache_category = current.cache_category;
                      let obj = {category_id,category_name,theme_id,cache_category,selected};
                      $this.allCategories.push(obj);
                      $this.allCategoriesInfo[category_id.toString()] = obj;
                  });
                }
                //Site Categories
                categories = this.siteCategories;
                //console.log("categories ", categories);
                if(categories.length > 0) {
                    categories.forEach(function (current) {
                        let category_id = current.category_id;
                        let category_name = current.name;
                        let theme_id = ""
                        let cache_category = "";
                        let obj = {category_id,category_name,theme_id,cache_category,selected};
                        $this.allSiteCategories.push(obj);
                    });
                }

                //console.log($this.allSiteCategories);
            },
            getWhere() {
                let where =  {
                    microsite_id:this.micrositeId,
                    tenant_id:this.tenantId,
                    category_id:this.categoryId,
                    site_id:this.siteId
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
                let tenantId = this.tenantId;
                let datas = [];
                for(let i=0; i<allCategories.length; i++) {
                    let current = allCategories[i];
                    //console.log("current ", current);
                    let categoryInfo = this.getCategoryInfo(current.getAttribute("data-category-id"), "allCategories");
                    //console.log("categoryInfo ", categoryInfo);
                    let data = {position:(i+1)};
                    let where = {category_id:categoryInfo.category_id, tenant_id:tenantId}; //microsite_id:
                    datas.push({where, data});
                }

                if(datas.length > 0) {
                    let url = AdminConfig.admin_path("category/updateIndex");
                    let postParams = {};
                    postParams.data = datas;
                    //postParams.applicableForAllTenants = "false";//$this.applicableForAllTenants;
                    this.saveNow(url, postParams).then(function (res) {
                        //console.log(res);
                        Toast.show($this, "Saved...");

                    }).catch(function (res) {
                        Toast.show($this, res.message);
                    });
                }

            },
            showSearch(findIn="allCategories") {
                let whereArr = (findIn==="allCategories") ? this.allCategories : this.allSiteCategories;
                return whereArr.length > 10;
            },
            filterData(findIn="allCategories") {
                let key = (findIn==="allCategories") ? this.searchKeyTenantCategory : this.searchKeyCategory;
                let whereArr = (findIn==="allCategories") ? this.allCategories : this.allSiteCategories;
                if(key !== "" && key != null) {

                    key = key.toLowerCase();

                    return whereArr.filter(function(current) {
                        let name = current.category_name.toLowerCase();
                        let id = current.category_id;
                        if(id.toString() === key.toString() || name.includes(key)) {
                            return current;
                        }
                    });

                } else {
                    // console.log("data", this.allData.data);
                    return (findIn==="allCategories") ? this.allCategories : this.allSiteCategories;
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
                if(span.classList.contains("js_info")) {
                    action = "info";
                }
                if(span.classList.contains("js_delete")) {
                    action = "delete";
                }
                if(action === "info") {
                    showInfo(target, this.$refs.popover);
                }


               // console.log(this.currentSelection);

                function showInfo(source, target, where="center-right") {
                    target.classList.remove(...["animated", "fadeOut", "hide"]);
                    target.style.display = "inline-block";
                    let src = source;
                    let tgt = target;
                    let srcOpt = src.getBoundingClientRect();
                    let tgtOpt = tgt.getBoundingClientRect();

                    let left = (srcOpt.left + srcOpt.width);
                    let top = (srcOpt.top + srcOpt.height) - (tgtOpt.height /2);

                    let leftMenu = document.getElementById("leftMenuPanel").getBoundingClientRect();
                    let topBar = document.getElementById("topNavBar").getBoundingClientRect();

                    left = left - leftMenu.width;
                    top = top - topBar.height - 20;

                    //left menu - top should be minus

                    target.classList.add(...["animated", "jello"]);

                 switch (where) {
                     case "center-right":
                         tgt.style.display = "inline-block";
                         tgt.style.position = "absolute";
                         tgt.style.left = left + "px";
                         tgt.style.top = top +"px";
                         break;
                 }
                }

            },
            closePopup() {
                this.$refs.popover.classList.add(...["animated", "fadeOut", "hide"]);
            },
            fetchNewData() {
                let where = this.getWhere();
                delete where.category_id;
                if(where.microsite_id == 0) {
                    delete where.microsite_id;
                };
                let url = "category/settings";
                window.location.href = AdminConfig.admin_path(url, where);
            },
            enableSorting() {
                let $this = this;
                this.$nextTick(function () {
                    let list = document.querySelectorAll(".js_category");
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
                    animation:200,
                    draggable: ".js_item",
                    group: {
                        name: 'modulesBox',
                        pull: "clone",
                        revertClone:true
                    },
                    sort:false,
                    ghostClass: ".js_category",
                    onEnd: function (/**Event*/evt) {

                        let to = evt.to;
                        let item = evt.item; // dragged HTMLElement

                        let categoryId = item.getAttribute("data-category-id");

                        if(categoryId) {
                            let categoryInfo = $this.getCategoryInfo(categoryId, "allSiteCategories");

                            //console.log("onEnd: categoryInfo ",categoryInfo);

                            let isAdded;
                            if(categoryInfo !== null) {
                                isAdded = $this.addCategory(categoryInfo);
                            }

                            item.parentNode.removeChild(item);

                            $this.highlightEagerDrop("remove");

                            //we can send feedback
                            if(isAdded === false) {
                                Toast.show($this, "Category is already added in this tenant...");
                            }
                        }

                    },
                    onStart: function (/**Event*/evt) {

                        $this.highlightEagerDrop("add");
                    }
                });
            },
            highlightEagerDrop(addRemove="add") {
                let js_hook_modules = document.querySelectorAll(".js_category");
                js_hook_modules.forEach(function(current) {
                    current.classList[addRemove]("module-drop-eager");
                });
            },
            getCategoryInfo(categoryId, findIn="allSiteCategories") {
                let found = null;
                let whereArr = (findIn === "allSiteCategories") ? this.allSiteCategories : this.allCategories;

                for(let i = 0; i<whereArr.length; i++) {
                    let current = whereArr[i];
                    if(parseInt(current.category_id) === parseInt(categoryId)) {
                        found = current;
                        break;
                    };
                };
                return found;
            },
            findIndex(categoryId, findIn="allCategories") {
                //allCategories is left site
                let index = -1;
                let whereArr = (findIn === "allCategories") ? this.allCategories : this.allSiteCategories;
                if(whereArr.length > 0 ) {
                    for(let i = 0; i<whereArr.length; i++) {
                        let current = whereArr[i];
                        if(parseInt(current.category_id) === parseInt(categoryId)) {
                            index = i;
                            break;
                        };
                    };
                };
                return index;
            },
            addCategory(category) {
                let $this = this;
                let index = this.findIndex(category.category_id);
                let isAdded = false;
                if(index === -1) {
                    //add in info
                    this.allCategoriesInfo[category.category_id.toString()] = {index:index, info:category};

                    //save in db
                    setTimeout(function() {
                        updateInDB();
                    }, 1);

                    isAdded = this.allCategories.push(category);

                    //console.log("isAdded 2 ",isAdded);

                };

                return isAdded;


                function updateInDB() {
                    let url = AdminConfig.admin_path("category/insertCategory");
                    let where = $this.getWhere();
                    let postParams = {};
                    delete category["name"];
                    category.tenant_id = where.tenant_id;
                    postParams.data = {
                        category_id:category.category_id,
                        tenant_id:where.tenant_id,
                        site_id:where.site_id
                        };
                    //postParams.applicableForAllTenants = "false";//$this.applicableForAllTenants;
                    $this.saveNow(url, postParams).then(function (res) {
                        console.log(res);
                        Toast.show($this, "Saved...");

                    }).catch(function(res) {
                        Toast.show($this, res.message);
                    });
                };

            },
            setThemeEtc() {
                let $this = this;
                let current = this.currentSelection;
                //set them in array
                let index = this.findIndex(current.category_id);

                if(index !== -1) {

                    this.allCategories[index].theme_id = current.theme_id;
                    this.allCategories[index].cache_category = current.cache_category;
                    this.categoryId = parseInt(current.category_id);

                    let postParams = {};
                    postParams.where = this.getWhere();
                    postParams.data = {
                        cache_category:(current.cache_category) || "",
                        theme_id:current.theme_id
                    };
                    let url = AdminConfig.admin_path("category/updateThemeAndEtc");
                    this.saveNow(url, postParams).then(function (res) {
                        console.log(res);
                        Toast.show($this, "Saved...");

                    }).catch(function(res) {
                        Toast.show($this, res.message);
                    });

                }
                this.closePopup();
            },
            removeCategory(id) {
                let $this = this;
                let index = this.findIndex(id);
                if(index !== -1) {
                    let categoryInfo = this.allCategories.splice(index, 1);
                    removeNow(categoryInfo[0]);
                    return categoryInfo;
                };


                function removeNow(categoryInfo) {
                    let url = AdminConfig.admin_path("category/deleteCategory");
                    let postParams = {};
                    let where = $this.getWhere();

                    postParams.where = {category_id:categoryInfo.category_id, tenant_id:where.tenant_id, microsite_id:where.microsite_id};

                    $this.saveNow(url, postParams).then(function (res) {
                        console.log(res);
                        Toast.show($this, "Saved...");

                    }).catch(function(res) {
                        Toast.show($this, res.message);
                    });
                }

            },
            getThemeName(id) {
                for(var i=0;i<this.siteThemes.length;i++) {
                    if(this.siteThemes[i].id == id) {
                        return this.siteThemes[i].name;
                    }
                }
                return "";
            },
            updateThemeToAllCategories() {
                let $this = this;
                let url = AdminConfig.admin_path("category/updateThemeForAllCategories");
                let categories = this.filterData('allCategories');

                if(this.globalTheme !== "") {
                    if(categories.length > 0 ) {
                        let where = this.getWhere();
                        delete  where.category_id;
                        delete  where.microsite_id;

                        let postParams = {
                            data:{theme_id:this.globalTheme},
                            where:where
                        };
                        Toast.show($this, "Please wait...");
                        this.saveNow(url, postParams).then(function (res) {
                            //console.log(res);
                            if(res.data["error"]) {
                                Toast.show($this, res.data.message, 5000);
                            } else {
                                Toast.show($this, "Saved");
                                for(var i=0;i<categories.length;i++) {
                                    categories[i].theme_id = $this.globalTheme;
                                    //console.log(categories[i]);
                                }

                            }

                        }).catch(function(res) {
                            Toast.show($this, res.message);
                        });
                    }
                }
            }
        }
    }

</script>
