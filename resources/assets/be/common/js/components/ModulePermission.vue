<template>
    <div>
        <div v-if="superAdmin" class="alert alert-info">
            <strong>{{ userModules.name }}</strong> is admin or super admin. No need to provide module access.
        </div>
        <div v-if="superAdmin" class="row">
            <div class="form-group center-align">
                <a :href="dataBackUrl" class="btn btn-outline-primary">Okay :)</a>
            </div>
        </div>
        <form v-if="!superAdmin" :action="permissionSaveUrl" class="form-horizontal" method="post" role="form"
              v-on:submit.prevent="saveData">
            <div class="checkbox title">
                <h4>
                    <label>
                        <input v-model="selectAllModule" type="checkbox" @click="selectAll()"/> Select
                    </label>
                    modules for <span class="text-info">{{ userModules.name }} {{ userModules.last_name }}</span>
                </h4>
            </div>
            <ul class="list-unstyled list-permission">
                <li class="clearfix" v-for="module in form.cmsModuleData">
                    <div v-if="isParent(module)" class="row p-2">
                        <div class="col">
                            <input @click="selectMe(module)" class="form-check-input" type="checkbox" v-model="module.selected" :id="'check_'+module.id" :data-id="module.id" data-is-parent="true"  />
                            <label class="form-check-label" :for="'check_'+module.id">&nbsp;{{ module.name }}</label>
                        </div>
                        <div class="col-auto">
                            <label> <input :id="'check_readonly_'+module.id"  v-model="module.readonly" type="checkbox" @click="selectReadOnly(module, $event)" data-is-readonly-checkbox="true" /> Read Only</label>
                        </div>
                    </div>
                    <ul v-if="hasChild(module)" class="list-unstyled clearfix" style="margin-left:20px">
                        <li v-for="child in module.child" class="clearfix">
                            <div class="row p-2">
                                <div class="col">
                                    <input @click="selectMe(child, module)" class="form-check-input" type="checkbox" v-model="child.selected" :id="'check_'+child.id" :data-id="child.id" data-is-parent="false"  />
                                    <label class="form-check-label" :for="'check_'+child.id" data-is-readonly="true">&nbsp;{{ child.name }}</label>
                                </div>
                                <div class="col-auto">
                                    <label> <input :id="'check_readonly_'+child.id"  v-model="child.readonly" type="checkbox" @click="selectReadOnly(child, $event, true)" data-is-readonly-checkbox="true" /> Read Only</label>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="row">
                <div v-if="errorMessage!==''" class="alert alert-danger">{{ errorMessage }}</div>
                <div class="form-group center-align">
                    <input class="btn btn-success btn-from-submit" name="submit" type="submit" value="Save"/> <a :href="dataBackUrl" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>
</template>

<script>

import Form from '../helpers/form';
import {Toast} from '../helpers/common';
import {EventBus} from "../helpers/event-bus";

export default {
    mounted() {

        this.form.cmsModuleData = this.allModules;
        this.form.userId = this.userModules.id;
        this.showSuperAdmin();
        EventBus.on('on-paste', (data)=> {
            this.handlePaste(data);
        });

    },
    props: [
        'dataCmsModules',
        'dataUserModules',
        'dataControllerName',
        'dataBackUrl',
        'dataIsSuperAdmin'
    ],
    data() {
        return {
            userModules: (this.dataUserModules) ? JSON.parse(this.dataUserModules) : [],
            form: new Form({
                cmsModuleData: [],
                userId: 0
            }),
            selectAllModule: false,
            superAdmin: false,
            errorMessage: ''
        }
    },
    computed: {
        allModules() {
            let userModules = this.userModules.cmsmodules;

            let modules = (this.dataCmsModules) ? JSON.parse(this.dataCmsModules) : [];
            //console.log(modules);
            let filterModules = [];
            if (modules.length > 0) {
                for (let i = 0; i < modules.length; i++) {
                    let current = modules[i];
                    let found = findModule(current);
                    current.selected = (found) ? true : false;
                    current.readonly = (found.readonly === 1);
                    if (current.parent_id === 0 && current.child.length > 0) {
                        for (let c = 0; c < current.child.length; c++) {
                            let child = current.child[c];
                            let foundChild = findModule(child);
                            child.selected = (foundChild) ? true : false;
                            child.readonly = (foundChild.readonly === 1);
                        }
                    }
                    if (current.parent_id === 0) {
                        filterModules.push(current);
                    }
                }
            }
            return filterModules;

            function findModule(currentModule) {

                if (userModules.length > 0) {

                    let found = userModules.find(function (current) {
                        return current.module_id.toString() === currentModule.id.toString();
                    });

                    return (found === undefined) ? false : found;

                }
                return false;
            }
        },
        permissionSaveUrl() {

            return AdminConfig.admin_path(this.dataControllerName + "/saveModulePermissions");
        },
        csrfToken() {
            return window.Laravel.csrfToken;
        }
    },
    methods: {
        handlePaste(data) {
            let pastedData;
            pastedData = JSON.parse(data);
            this.form.cmsModuleData.forEach((item, index)=> {
                let id = item.id;
                let status = isCheckBoxSelected(id);
                if (status.selected) {
                    item.selected = true;
                    item.readonly = status.readonly;
                }
                if (item.child.length > 0) {
                    item.child.forEach((childItem, childIndex)=> {
                        let childId = childItem.id;
                        let statusChild = isCheckBoxSelected(childId);
                        if(statusChild.selected) {
                            childItem.selected = true;
                            childItem.readonly = statusChild.readonly;
                        }
                    })
                }
            });

            function isCheckBoxSelected(id) {
                for (let key in pastedData) {
                    let ele = document.getElementById("check_"+id);
                    let readonlyEle = document.getElementById("check_readonly_"+id);
                    if (ele && ele.checked) {
                        return {selected:true, readonly:readonlyEle.checked};
                    }
                }
                return {selected:false, readonly:false};
            }
        },
        hasChild(data) {
            return (data.child && data.child.length > 0);
        },
        isParent(data) {
            return data.parent_id === 0;
        },
        selectReadOnly(current, event, isChild=false) {
            let isChecked = event.target.checked;
            if (current.selected!==true) {
                current.selected = isChecked;
            }
            //current.readonly = (isChecked) ? 1 : 0;
            if (isChild === true) {
                //selected parent
            }
        },
        selectAll() {

            let shouldSelect = (this.selectAllModule === false);

            for (let i = 0; i < this.form.cmsModuleData.length; i++) {
                let current = this.form.cmsModuleData[i];
                current.selected = shouldSelect;

                this.selectMe(current, undefined, shouldSelect);
            }
        },
        selectMe(current, parentModule, forcedSelectAll = null) {

            let shouldSelect = (current.selected === false); //show previous state

            //if forcing
            if (forcedSelectAll !== null) {
                shouldSelect = forcedSelectAll;
            }

            if (current.child && current.child.length > 0) {
                current.child.map(function (c) {
                    c.selected = shouldSelect;
                    //reset readonly
                    if (!shouldSelect) {
                        c.readonly = false;
                    }
                })
            }

            if (shouldSelect !== true) {
                current.readonly = false;
            }

            //check if parent should be selected
            if (parentModule) {
                let isAnySelected = parentModule.child.find(function (current) {
                    //console.log("current.selected === true", current.selected === true)
                    return current.selected === true;
                });

                if (shouldSelect || isAnySelected) {
                    parentModule.selected = true;
                }
            }

            //Check if one or more is unchecked. reset select all tab
            if (shouldSelect === false) {
                this.selectAllModule = false;
            }

        },
        showSuperAdmin() {
            if (this.dataIsSuperAdmin.toString() === "1" || this.dataIsSuperAdmin.toString() === "true") {
                this.superAdmin = true;
            }
        },
        saveData() {

            this.form.post(this.permissionSaveUrl, false)
                .then(response => this.afterFormSaved(response))
                .catch(response => this.afterFormSaved(response));
        },
        afterFormSaved(response) {

            if (response.isSaved === true) {
                Toast.show(this, "Saved Successfully. ");
            } else {
                Toast.show(this, "There is some error.");
            }
        },
        goBack() {
            window.location = this.dataBackUrl;
        }
    }
}

</script>
