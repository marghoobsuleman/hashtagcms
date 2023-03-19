<template>
    <div>
        <form id="addEditFrm" :action="saveURL" class="form-group" method="post" role="form"
              v-on:keyup="hideErrorMessage($event)" v-on:submit.prevent="createModule">

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="name">Name</label>
                <div class="col-sm-7">
                    <input @blur="fillCopiedData" id="name" v-model="form.name" class="form-control" name="name" placeholder="Module name" required
                           type="text"/>
                    <label><input v-model="conventional" type="checkbox" @click="goConventional()"/> Go
                        Conventional</label>
                    <div class="text text-danger">{{ this.errors.name }}</div>
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="alias">Alias</label>
                <div class="col-sm-7">
                    <input id="alias" v-model="form.alias" class="form-control" name="alias" placeholder="Alias" required
                           type="text"/>
                    <div class="text text-danger">{{ this.errors.alias }}</div>
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="view_name">View Name</label>
                <div class="col-sm-7">
                    <input id="view_name" v-model="form.view_name" class="form-control" name="view_name" placeholder="View name"
                           required
                           type="text"/>
                    <div class="text text-danger">{{ this.errors.view_name }}</div>
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="cache_group">Cache Group</label>
                <div class="col-sm-7">
                    <input id="cache_group" v-model="form.cache_group" class="form-control" maxlength="60" name="cache_group"
                           placeholder="Cache Category"
                           type="text"/>

                    <div class="text text-danger">{{ this.errors.cache_group }}</div>
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="individual_cache">Is Individual Cache?</label>
                <label class="col-sm-7">
                    <input id="individual_cache" v-model="form.individual_cache" name="individual_cache"
                           type="checkbox"/>
                </label>
            </div>


            <div class="form-group row mb-3">
                <label class="col-sm-2" for="data_type">Data Type</label>
                <div class="col-sm-7">
                    <select id="data_type" v-model="form.data_type"
                            class="form-select select-big" name="data_type" @change="checkForService(form.data_type)">
                        <option value="">Select</option>
                        <option v-for="dataType in allDataTypes" :value="dataType">
                            {{ dataType }}
                        </option>
                    </select>
                    <div class="text text-danger">{{ this.errors.data_type }}</div>
                    <div v-show="form.data_type != '' && this.dataTypesInfo[form.data_type]" class="alert alert-info"
                         v-html="this.dataTypesInfo[form.data_type] || '' "></div>
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="data_handler">Data Handler</label>
                <label class="col-sm-7">
                    <textarea id="data_handler" v-model="form.data_handler" class="form-control input"
                              mb-2s="5" name="data_handler"
                              placeholder="Query or Service URL or filteration for this module" row v-bind:style="fluidCol"/>
                </label>
            </div>

            <div v-show="showServiceForm">
                <div class="form-group row mb-3">
                    <label class="col-sm-2" for="method_type">Method Type</label>
                    <div class="col-sm-7">
                        <select id="method_type" v-model="form.method_type" class="form-select select-big"
                                name="method_type">
                            <option value="">Select</option>
                            <option v-for="methodType in allMethodTypes" :value="methodType.value">
                                {{ methodType.name }}
                            </option>
                        </select>
                        <div class="text text-danger">{{ this.errors.data_type }}</div>
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label class="col-sm-2" for="service_params">Service Params</label>
                    <div class="col-sm-7">
                        <input id="service_params" v-model="form.service_params" class="form-control" name="service_params"
                               placeholder="Service Params"
                               type="text"/>
                        <div class="text text-danger">{{ this.errors.service_params }}</div>
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label class="col-sm-2" for="service_params">Service Headers</label>
                    <div class="col-sm-7">
                  <textarea id="headers" v-model="form.headers" class="form-control input" name="headers" placeholder="Service header as json"
                            rows="5"/>
                        <div class="text text-danger">{{ this.errors.headers }}</div>
                    </div>
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="data_key_map">Data Key Map</label>
                <label class="col-sm-7">
                    <textarea id="data_key_map" v-model="form.data_key_map"
                              class="form-control input"
                              name="data_key_map" placeholder="key to be replaced in comma seperated. ie: :site_id, :lang_id " rows="5"
                              v-bind:style="fluidCol"/>
                </label>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="is_seo_module">Use this module as seo module</label>
                <label class="col-sm-7">
                    <input id="is_seo_module" v-model="form.is_seo_module" name="is_seo_module" type="checkbox"/>
                </label>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="is_mandatory">Is Mandatory?</label>
                <label class="col-sm-7">
                    <input id="is_mandatory" v-model="form.is_mandatory" name="is_mandatory" type="checkbox"/>
                </label>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="shared">Is Shared?</label>
                <label class="col-sm-7">
                    <input id="shared" v-model="form.shared" name="shared" type="checkbox"/>
                </label>
            </div>
            <div class="form-group row mb-3">
                <label class="col-sm-2" for="linked_module">Linked Module Alias</label>
                <label class="col-sm-7">
                    <input id="linked_module" v-model="form.linked_module" name="linked_module" type="text" class="form-control" />
                </label>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="live_edit">Live Edit?</label>
                <label class="col-sm-7">
                    <input id="live_edit" v-model="form.live_edit" name="live_edit" type="checkbox"/>
                </label>
            </div>

            <div v-show="showQueryForm" class="form-group row mb-3">
                <fieldset class="border">
                    <legend>
                        Query Service Properties
                    </legend>
                    <div class="form-group">

                        <label class="col-sm-2" for="query_statement" v-bind:style="sFont">Query</label>

                        <label class="col-sm-7">
                            <textarea id="query_statement" v-model="form.query_statement" class="form-control input" name="query_statement"
                                      rows="5" v-bind:style="fluidCol"/>
                        </label>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-sm-2" for="query_as" v-bind:style="sFont">Get query data as</label>
                        <div class="col-sm-7">
                            <select id="query_as" v-model="form.query_as" class="form-select select-big"
                                    name="query_as">
                                <option value="">Select</option>
                                <option v-for="queryData in allQueryDataTypes" :value="queryData.value">
                                    {{ queryData.name }}
                                </option>
                            </select>
                            <div class="text text-danger">{{ this.errors.query_as }}</div>
                        </div>
                    </div>

                </fieldset>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2" for="description">Description or different database connection name if it is a
                    query module</label>
                <label class="col-sm-7">
                    <textarea id="description" v-model="form.description" class="form-control input" name="description"
                              rows="5" v-bind:style="fluidCol"/>
                </label>
            </div>
            <div v-show="this.showAllUpdateSection" class="form-group row mb-3">
                <label class="col-sm-2" for="update_inAllSites">Update in all sites?</label>
                <label class="col-sm-7">
                    <input id="update_inAllSites" v-model="form.update_inAllSites" name="update_inAllSites"
                           type="checkbox"/>
                    <div class="alert alert-info">
                        Be careful by clicking this checkbox. Content will be updated in all sites based on alias.
                        If you are changing alias it will be updated only in this site.
                    </div>
                </label>
            </div>
            <div class="row mb-3">
                <div v-show="typeof errorMessage === 'string' && errorMessage !== '' " class="alert alert-danger">
                    {{ errorMessage }}
                </div>
                <div class="form-group center-align">
                    <input class="btn btn-success btn-from-submit" name="submit" type="submit" value="Save"/> <a :href="dataBackUrl" class="btn btn-outline-secondary">Back</a>

                </div>
            </div>
        </form>
    </div>
</template>


<script>
import {Toast, PasteFromClipboard, IsJson} from '../helpers/common';
import Form from '../helpers/form';

export default {


    mounted() {
        if (this.dataActionPerformed === "edit") {
            this.setFormData(this.formData);
        } else {
            this.form.site_id = this.dataSiteId;
        }
    },
    props: [
        'dataControllerName',
        'dataBackUrl',
        'dataSite',
        'dataResults',
        'dataActionPerformed',
        'dataDataTypes',
        'dataDataTypesInfo',
        'dataFormAction',
        'dataSiteId'
    ],
    data() {
        return {
            sFont: {
                'font-size': '14px'
            },
            fluidCol: {
                'width': '100%'
            }, allQueryDataTypes: [
                {'name': 'param', 'value': 'param'},
                {'name': 'data', 'value': 'data'},
            ],
            conventional: false,
            formData: (typeof this.dataResults != "undefined") ? JSON.parse(this.dataResults) : [],
            siteData: (typeof this.dataSite !== "undefined") ? JSON.parse(this.dataSite) : [],
            showExtraData: {},
            allDataTypes: (typeof this.dataDataTypes != "undefined") ? JSON.parse(this.dataDataTypes) : [],
            allMethodTypes: [
                {'name': 'GET', 'value': 'GET'},
                {'name': 'POST', 'value': 'POST'}
            ],
            form: new Form({
                id: 0,
                name: "",
                icon_css: "",
                alias: "",
                view_name: "",
                cache_group: "",
                data_type: "",
                method_type: "",
                data_handler: "",
                data_key_map: "",
                query_statement: "",
                query_as: "",
                description: "",
                site_id: 1,
                is_seo_module: 0,
                individual_cache: 0,
                shared: 0,
                update_inAllSites: 0,
                is_mandatory: 0,
                service_params: "",
                actionPerformed: this.dataActionPerformed,
                backURL: this.dataBackUrl,
                headers: '',
                live_edit:0,
                linked_module:''
            }),
            errors: {},
            cacheData: {},
            sortable: null,
            sortingInterval: -1,
            errorMessage: '',
            dataTypesInfo: (typeof this.dataDataTypesInfo != "undefined") ? JSON.parse(this.dataDataTypesInfo) : [],
            saveURL: this.dataFormAction
        }
    },
    computed: {
        showQueryForm: function () {
            return this.form.data_type.toLowerCase().indexOf('queryservice') > -1;
        },
        showServiceForm: function () {
            return this.form.data_type.toLowerCase().indexOf('service') > -1;
        },
        showAllUpdateSection: function () {

            return (this.dataActionPerformed === 'edit' && this.siteData.length > 1);
        }

    },
    methods: {
        fillCopiedData() {
            PasteFromClipboard().then((res)=> {
                if (IsJson(this.form.name)) {
                    this.setFormData(JSON.parse(this.form.name));
                }
            }).catch(res=> {
                console.log("unable to paste")
            });
        },
        goConventional(arg) {
            //wanted to go conventional
            if (this.conventional === false) {
                let name = this.form.name;
                this.form.title = name;
                this.form.alias = ("MODULE_" + name.toUpperCase()).replace(/\s/g, "_");
                this.form.view_name = (name.toLowerCase()).replace(/\s/g, "-");
            }
        },
        setFormData(data) {
            let data_length = Object.keys(data).length;
            let form = document.querySelector("#addEditFrm");
            for (let a in data) {
                let val = data[a];
                //setting form values;
                this.form[a] = val;
                //setting document values
                let ele = form[a] || document.getElementById(a);
                if (ele) {
                    // to make it visible for checkbox and radio
                    if(ele.type === "checkbox" || ele.type === "radio") {
                        this.form[a] = (val === 1);
                    }
                }
            }


        },
        checkForService(dataType) {
            let self = this
            this.showExtraData["show"] = dataType !== "" && (dataType.indexOf('Service') > -1 || dataType.indexOf('service') > -1);
        },
        createModule() {
            let $this = this;

            preCheck(this);

            this.form.post(this.saveURL)
                .then((response) => {
                    this.resetForm(response);
                } )
                .catch((response) => {
                    this.showError(response);
                });

            function preCheck() {

                $this.form.is_seo_module = ($this.form.is_seo_module) ? $this.form.is_seo_module : 0;
                $this.form.individual_cache = ($this.form.individual_cache) ? $this.form.individual_cache : 0;
                $this.form.update_inAllSites = ($this.form.update_inAllSites) ? $this.form.update_inAllSites : 0;
                $this.form.is_mandatory = ($this.form.is_mandatory) ? $this.form.is_mandatory : 0;
                $this.form.service_params = ($this.form.service_params) ? $this.form.service_params : "";
                $this.form.shared = ($this.form.shared) ? $this.form.shared : 0;
                $this.form.live_edit = ($this.form.live_edit) ? $this.form.live_edit : 0;
            }
        },
        showError(res) {
            for (let i in res.errors) {
                if (res.errors.hasOwnProperty(i)) {
                    this.errors[i] = res.errors[i][0];
                }
            }

            this.errorMessage = res.message;

        },
        hideErrorMessage(event) {

            let name = event.target.getAttribute("name");
            this.errors[name] = "";

            if (this.errorMessage !== '') {
                this.errorMessage = '';
            }
        },
        resetForm(response) {

            //console.log("response.isSaved ",response.isSaved);
            if (!isTrue(response.isSaved)) {

                this.errorMessage = {};
                Toast.show(this, "There is some error...");
                this.errorMessage = response.message;

            } else {

                this.errorMessage = '';

                let action = this.dataActionPerformed.toUpperCase();

                Toast.show(this, "Saved...");

                if (isTrue(response.isSaved)) {
                    window.location.href = response.backURL;
                }

            }

            function isTrue(isSaved) {
                return (isSaved.toString() === "true" || isSaved.toString() === "1");
            }
        }
    }
}

</script>
