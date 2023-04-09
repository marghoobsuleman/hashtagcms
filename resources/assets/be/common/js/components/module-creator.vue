<template>
    <div>
        <form :action="saveURL" class="form-horizontal" method="post" role="form"
              v-on:keyup="hideErrorMessage($event)" v-on:submit.prevent="createModule">

            <div class="form-group row">
                <label class="col-sm-10">
                    <input v-model="form.createFiles" type="checkbox" name="create-validator" /> Create Files (Controller/Model/View/Vaidator) -
                    <small class="text-info">Try creating it from terminal command. It might not work due to rights</small>
                </label>
            </div>

            <input v-model="form.display_name" type="hidden"/>
            <div class="form-group row">
                <label class="col-sm-3">Name</label>
                <div class="col-sm-7">
                    <input v-model="form.name" class="form-control" name="name" placeholder="Module name" required
                           type="text" @blur="updateControllerName()"/>
                    <div class="text text-danger">{{ this.errors.name }}</div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3">Sub Title</label>
                <div class="col-sm-7">
                    <input v-model="form.sub_title" class="form-control" name="name" placeholder="Sub title"
                           type="text"/>
                    <div class="text text-danger">{{ this.errors.sub_title }}</div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3">Controller Name (link rewrite)</label>
                <div class="col-sm-7">
                    <input v-model="form.controller_name" class="form-control" name="controller_name" placeholder="Controller name"
                           required
                           type="text" @blur="updateControllerName(form.controller_name)"/>
                    <div class="text text-danger">{{ this.errors.controller_name }}</div>
                </div>

            </div>

            <div class="form-group row">
                <label class="col-sm-3">Parent</label>
                <div class="col-sm-7">
                    <select v-model="form.parent_id" class="form-select select-big">
                        <option value="">Select</option>
                        <option v-for="module in allModules" :value="module.id">
                            {{ module.name }}
                        </option>
                    </select>

                    <div class="text text-danger">{{ this.errors.parent_id }}</div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3">Icon CSS</label>
                <div class="col-sm-7">
                    <input v-model="form.icon_css" class="form-control" name="name" placeholder="Icon CSS" required
                           type="text"/>
                    <div class="text text-danger">{{ this.errors.icon_css }}</div>
                </div>
            </div>

            <div class="form-group row">

                <label class="col-sm-3">List View Name</label>
                <div class="col-sm-7">
                    <input v-model="form.list_view_name" class="form-control" name="name" placeholder="Enter list view Name (Default list view is common/listing)"
                           type="text"/>
                    <div class="text text-danger">{{ this.errors.list_view_name }}</div>
                </div>

            </div>
            <div class="form-group row">
                <label class="col-sm-3">Edit View Name</label>
                <div class="col-sm-7">
                    <input v-model="form.edit_view_name" class="form-control" name="name" placeholder="Edit list view Name (Default edit view is addedit)"
                           type="text"/>
                    <div class="text text-danger">{{ this.errors.edit_view_name }}</div>
                </div>

            </div>

            <div v-if="form.createFiles">
                <div class="form-group row">
                    <label class="col-sm-3">Data Source</label>
                    <div class="col-sm-7">
                        <select v-model="mainModel.tableName" class="form-select select-big"
                                @change="populateMainDataFields()">
                            <option value="">Select</option>
                            <option v-for="table in allTables" :value="table.name">
                                {{ table.name }}
                            </option>
                        </select>
                        <div class="col-sm-5">
                                    <span v-if="mainModel.tableName!=''" class="models">
                                        {{ getModelName(mainModel.tableName) }}
                                    </span>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3">Data with? Choose another model.</label>
                    <div class="col-sm-7">

                        <select v-model="relationModels.tableName" class="form-select select-big"
                                @change="addRelationModel()">
                            <option value="">Select</option>
                            <option v-for="table in allTables" :value="table.name">
                                {{ table.name }}
                            </option>

                        </select>

                        <div>
                            <fieldset v-if="hasRelation()" class="fieldset">
                                <legend class="small legendTitle">Relation Models</legend>
                                <div v-for="(rModel, index) in relationModels.models" class="models_row">
                                            <span class="models">
                                                {{ rModel.model }}
                                                <input :value="rModel.relationAlias" name="relation" placeholder="Relation name"
                                                       type="text"
                                                       @change="updateRelationModel($event, index, 'relationAlias')"/>
                                                <select class="select" name="relationType"
                                                        @change="updateRelationModel($event, index, 'relationType')">
                                                    <option value="hasMany">hasMany</option>
                                                    <option value="belongsTo">belongsTo</option>
                                                    <option value="hasOne">hasOne</option>
                                                </select>
                                                isLanguage: <input v-model="rModel.isLanguage" type="checkbox" name="relationModel" />
                                                <span class="small fa fa-close"
                                                      @click="removeRelationModel(index, rModel.model)"></span>
                                            </span>
                                </div>

                            </fieldset>

                        </div>
                    </div>

                </div>
                <div class="row" v-if="hasMainDataSource()">
                    <strong>Click on fields to choose</strong>
                </div>
                <div v-if="hasMainDataSource()" class="form-group row">

                    <div class="col-auto">
                        <div class="card shadow-sm models-box">
                            <div class="card-header">
                                <span
                                    class="text-small pull-right hand text-success"
                                    @click="selectAllField(mainModel)">Add all &#187;</span>{{ getModelName(mainModel.tableName) }}<br />
                            </div>
                            <div class="card-body p-0 m-0 tables_panel">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item" v-for="field in mainModel.fields" @click="selectField(field, mainModel)">{{ field }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <template v-if="hasRelation()">
                    <div class="col-auto" v-for="(rModel, index) in relationModels.models">
                        <div class="card shadow-sm models-box">
                            <div class="card-header">
                                <span
                                    class="text-small pull-right hand text-success"
                                    @click="selectAllField(mainModel, rModel.relationAlias)">Add all &#187;</span>{{ rModel.model }}<br />->with['{{ rModel.relationAlias }}']
                            </div>
                            <div class="card-body p-0 m-0 tables_panel">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item" v-for="field in rModel.fields"
                                        @click="selectField(field, mainModel, rModel.relationAlias)">{{ field }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    </template>
                </div>
                <div class="row mt-3" v-if="mainModel.selected.length > 0">
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                Selected Fields <span class="text-small pull-right hand text-danger" @click="removeAllField(mainModel)">Remove all</span>
                            </div>
                            <div class="card-body">
                                <ul id="selectedFields">
                                    <li class="fields non-selectable" v-for="(val, index) in mainModel.selected" :data-fieldname="val">
                                        {{ val }} <span
                                        class="small fa fa-close"
                                        @click="removeField(mainModel, index)"></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div v-if="errorMessage!==''" class="alert alert-danger">{{ errorMessage }}</div>
                <div class="form-group center-align">
                    <input class="btn btn-success btn-from-submit" name="submit" type="submit" value="Save"/> <a :href="dataBackUrl" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</template>


<script>

import {Toast} from '../helpers/common';
import Form from '../helpers/form';
import Sortable from 'sortablejs';


class DbData {

    static get data() {
        return {};
    }

    static getFields(table) {

        return new Promise((resolve, reject) => {
            axios.get("getFields?table=" + table)
                .then(function (res) {
                    resolve(res);
                })
                .catch(function () {
                    reject(this);
                })

        })
    }
}

export default {


    mounted() {
        //console.log("backUrl "+this.dataBackUrl)
        //console.log(this.controllerName);
    },
    props: [
        'dataDatabaseTables',
        'dataControllerName',
        'dataBackUrl',
        'dataCmsModules'
    ],
    computed: {
        saveURL: function () {
            return AdminConfig.admin_path(this.dataControllerName + '/createModule');
        }
    },
    data() {
        return {
            allTables: (typeof this.dataDatabaseTables !== "undefined") ? JSON.parse(this.dataDatabaseTables) : [],
            allModules: (typeof this.dataCmsModules !== "undefined") ? JSON.parse(this.dataCmsModules) : [],
            mainModel: {tableName: '', fields: [], modelName: '', selected: []},
            relationModels: {tableName: '', models: []},

            form: new Form({
                name: '',
                controller_name: '',
                validator_name: '',
                dataWith: [],
                dataSource: '',
                createFiles: true,
                selectedFields: [],
                relationModels: [],
                parent_id: '',
                display_name: '',
                sub_title: '',
                icon_css: '',
                list_view_name: '',
                edit_view_name: ''
            }),
            errors: {},
            cacheData: {},
            sortable: null,
            sortingInterval: -1,
            errorMessage: ''
        }
    },
    methods: {
        updateControllerName(controller_name) {
            let name = controller_name || this.form.name;
            name = name.toLowerCase().replace(/\s/g, "");

            this.form.controller_name = pluralize.singular(name);
            this.form.validator_name = this.form.controller_name;

            //check controller existence
            if (controller_name !== undefined) {
                this.isControllerExists(this.form.controller_name);
            }

            this.form.icon_css = this.form.controller_name;

            //console.log("this.form.validator_name "+this.form.validator_name);

        },
        getModelName(modelName = "") {

            let tableName = modelName;
            let $this = this;
            modelName = pluralize.singular(modelName) + "::class";
            let arr = modelName.split("_");
            arr = arr.map(a => a.charAt(0).toUpperCase() + a.substr(1, a.length));

            return arr.join("");

        },
        populateMainDataFields() {
            let $this = this;
            if (this.mainModel.tableName !== "") {
                this.mainModel.fields = ["Please wait..."];
                DbData.getFields(this.mainModel.tableName).then(function (res) {
                    $this.mainModel.fields = res.data;
                });
                this.mainModel.modelName = this.getModelName(this.mainModel.tableName);
                this.form.dataSource = this.mainModel.modelName;

            }

        },
        updateWithData() {
            this.form.dataWith = [];
            for (let i = 0; i < this.relationModels.models.length; i++) {
                let current = this.relationModels.models[i];
                if (this.form.dataWith.indexOf(current.relationAlias) === -1) {
                    this.form.dataWith.push(current.relationAlias);
                }
            }

        },
        addRelationModel() {

            if (this.relationModels.tableName !== "") {

                let tableName = this.relationModels.tableName;
                let modelName = this.getModelName(tableName);


                if (this.hasInRelationModel(modelName) === false) {

                    let relationAlias = "";
                    if (tableName.endsWith("_langs") || tableName.endsWith("_sites")) {
                        relationAlias = tableName.endsWith("_langs") ? "lang" : "site";
                    } else {
                        relationAlias = pluralize.singular(tableName.replace(/_/g, "")).toLowerCase();
                    }

                    let isLanguage = (relationAlias === "lang");

                    let relationType = 'hasMany';

                    let relationalData = {
                        model: modelName,
                        relationAlias: relationAlias,
                        relationType: relationType,
                        isLanguage: isLanguage,
                        fields: [],
                        selected: []
                    };

                    //not to show same table again.
                    if (!this.cacheData[modelName]) {
                        this.relationModels.models.push(relationalData);
                    }
                    //Add in cache
                    this.cacheData[modelName] = relationalData;
                    this.cacheData[modelName].fields = ["Please wait..."];
                    DbData.getFields(tableName).then((res)=> {

                        this.cacheData[modelName].fields = res.data;

                    });

                    this.updateWithData();
                }
            }

        },
        hasInRelationModel(modelName) {
            return this.cacheData[modelName] || false;
        },
        updateRelationModel(event, index, key) {
            let alias = this.relationModels.models[index][key];
            this.relationModels.models[index][key] = event.target.value;

            if (key === "relationAlias") {
                //reset
                this.relationModels.models[index].selected = [];
                this.removeSelectedRelationFields(alias);
            }

            this.updateWithData();

        },
        removeSelectedRelationFields(alias) {
            if (this.mainModel.selected.length > 0) {
                let selected = [];
                for (let i = 0; i < this.mainModel.selected.length; i++) {
                    let current = this.mainModel.selected[i];
                    if (!current.startsWith(alias + ".")) {
                        selected.push(current);
                    } else {
                        // this.mainModel.selected.splice(i, 1); this is not working
                    }
                }
                this.mainModel.selected = selected;
            }
        },
        removeRelationModel(index, model) {

            let alias = this.relationModels.models[index].relationAlias;

            delete this.cacheData[model];
            this.relationModels.models.splice(index, 1);
            this.relationModels.tableName = "";

            this.removeSelectedRelationFields(alias);

        },
        hasRelation() {
            return this.relationModels.models.length > 0;
        },
        hasMainDataSource() {
            return this.mainModel.tableName !== "";
        },
        selectAllField(where, relation = "") {

            if (where.selected.length === 0) {
                where.selected = where.fields.slice();
                if (relation !== "") {
                    where.selected = where.selected.map(c => (relation + "." + c));
                }
            } else {
                for (let i = 0; i < where.fields.length; i++) {
                    this.selectField(where.fields[i], where, relation);
                }
            }

            this.enableSorting();

        },
        selectField(field, where, relation = "") {

            field = (relation !== "") ? relation + "." + field : field;

            if (where.selected.indexOf(field) === -1) {
                where.selected.push(field);
            }

            this.enableSorting();

        },
        removeField(where, index) {
            where.selected.splice(index, 1);
        },
        removeAllField(where) {

            where.selected = [];
            this.form.selectedFields = [];

        },
        isControllerExists(name = "") {
            if (name !== "") {
                let $this = this;
                axios.get("isControllerExists?name=" + name).then(
                    function (res) {
                        if (res.data === 1) {
                            Toast.show($this, "Controller already exist");
                        }
                    }
                );
            }
        },
        createModule() {

            this.setSortedFields();
            this.form.relationModels = this.relationModels;


            //console.log(this.mainModel);
            //console.log(this.relationModels);
            //console.log(this.form);

            this.form.post(this.saveURL)
                .then(response => this.resetForm(response))
                .catch(response => this.showError(response));

        },
        enableSorting() {

            this.$nextTick(function () {
                if (this.sortable != null) {
                    this.sortable.destroy();
                }
                let el = document.getElementById('selectedFields');
                this.sortable = Sortable.create(el, {
                    onEnd: this.sortingCallback,
                    onStart: this.cancelSortingCallback
                });
            });
        },
        cancelSortingCallback() {
            if (this.sortingInterval !== -1) {
                clearInterval(this.sortingInterval);
            }
        },
        sortingCallback() {

            this.cancelSortingCallback();

        },
        setSortedFields() {
            let items = document.querySelectorAll("#selectedFields li");
            let count = items.length;
            if (count > 1) {
                let selected = [];
                for (let i = 0; i < count; i++) {
                    let current = items[i];
                    let field = current.getAttribute("data-fieldname");
                    selected.push(field);
                }
                this.form.selectedFields = selected;
            }
        },
        showError(res) {
            this.errors = {};
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
                this.errorMessage = ''
            }
        },
        resetForm(response) {
            this.mainModel = {tableName: '', fields: [], modelName: '', selected: []};
            this.relationModels = {tableName: '', models: []};

            if (response.created === 0) {
                Toast.show(this, "There is some error...");
                this.errorMessage = response.message;
            } else {
                Toast.show(this, "Created...");
            }

        }

    }
}

</script>
