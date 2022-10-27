<template>
    <div>
        <form :action="saveURL" method="post" class="form-horizontal" role="form"
              v-on:submit.prevent="createModule" v-on:keyup="hideErrorMessage($event)">

            <div class="form-group">
                <label class="col-sm-10">
                    <input type="checkbox" v-model="form.createFiles"/> Create Files (Controller/Model/View/Vaidator) -
                    <small class="text-info">Try creating it from terminal command. It might not work in production due to rights</small>
                </label>
            </div>

            <input type="hidden" v-model="form.display_name" value="" />
            <div class="form-group">
                <label class="col-sm-3">Name</label>
                <div class="col-sm-7">
                    <input type="text" required class="form-control" name="name" v-model="form.name"
                           placeholder="Module name" @blur="updateControllerName()"/>
                    <div class="text text-danger">{{this.errors.name}}</div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3">Sub Title</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="name" v-model="form.sub_title"
                           placeholder="Sub title"  />
                    <div class="text text-danger">{{this.errors.sub_title}}</div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3">Controller Name (link rewrite)</label>
                <div class="col-sm-7">
                    <input type="text" required class="form-control" name="controller_name" v-model="form.controller_name"
                           placeholder="Controller name" @blur="updateControllerName(form.controller_name)"/>
                    <div class="text text-danger">{{this.errors.controller_name}}</div>
                </div>

            </div>

            <div class="form-group">
                <label class="col-sm-3">Parent</label>
                <div class="col-sm-7">
                    <select class="form-control select-big" v-model="form.parent_id">
                        <option value="">Select</option>
                        <option v-for="module in allModules" :value="module.id">
                            {{module.name}}
                        </option>
                    </select>

                    <div class="text text-danger">{{this.errors.parent_id}}</div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3">Icon CSS</label>
                <div class="col-sm-7">
                    <input type="text" required class="form-control" name="name" v-model="form.icon_css"
                           placeholder="Icon CSS"  />
                    <div class="text text-danger">{{this.errors.icon_css}}</div>
                </div>
            </div>

            <div class="form-group">

                <label class="col-sm-3">List View Name</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="name" v-model="form.list_view_name"
                           placeholder="Enter list view Name (Default list view is common/listing)"  />
                    <div class="text text-danger">{{this.errors.list_view_name}}</div>
                </div>

            </div>
            <div class="form-group">
                <label class="col-sm-3">Edit View Name</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="name" v-model="form.edit_view_name"
                           placeholder="Edit list view Name (Default edit view is addedit)"  />
                    <div class="text text-danger">{{this.errors.edit_view_name}}</div>
                </div>

            </div>

            <div v-if="form.createFiles">
                <div class="form-group">
                    <label class="col-sm-3">Data Source</label>
                    <div class="col-sm-7">
                        <select class="form-control select-big" v-model="mainModel.tableName"
                                @change="populateMainDataFields()">
                            <option value="">Select</option>
                            <option v-for="table in allTables" :value="table.name">
                                {{table.name}}
                            </option>
                        </select>
                        <div class="col-sm-5">
                                    <span class="models" v-if="mainModel.tableName!=''">
                                        {{getModelName(mainModel.tableName)}}
                                    </span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3">Data with? Choose another model.</label>
                    <div class="col-sm-7">

                        <select class="form-control select-big" v-model="relationModels.tableName"
                                @change="addRelationModel()">
                            <option value="">Select</option>
                            <option v-for="table in allTables" :value="table.name">
                                {{table.name}}
                            </option>

                        </select>

                        <div>
                            <fieldset class="fieldset" v-if="hasRelation()">
                                <legend class="small legendTitle">Relation Models</legend>
                                <div class="models_row" v-for="(rModel, index) in relationModels.models">
                                            <span class="models">
                                                {{rModel.model}}
                                                <input type="text" name="relation" placeholder="Relation name"
                                                       :value="rModel.relationAlias"
                                                       @change="updateRelationModel($event, index, 'relationAlias')"/>
                                                <select class="select" name="relationType"
                                                        @change="updateRelationModel($event, index, 'relationType')">
                                                    <option value="hasMany">hasMany</option>
                                                    <option value="belongsTo">belongsTo</option>
                                                    <option value="hasOne">hasOne</option>
                                                </select>
                                                isLanguage: <input type="checkbox" v-model="rModel.isLanguage" />
                                                <span class="small fa fa-close"
                                                      @click="removeRelationModel(index, rModel.model)"></span>
                                            </span>
                                </div>

                            </fieldset>

                        </div>
                    </div>

                </div>

                <div class="form-group" v-if="hasMainDataSource()">
                    <label class="col-sm-3">Click to select fields</label>
                    <div class="col-sm-12">
                        <div class="col-sm-4">
                            <ul class="box-with-shadow">
                                <li class="box-with-shadow-header">{{getModelName(mainModel.tableName)}} <span
                                        class="text-small pull-right hand" @click="selectAllField(mainModel)">Add all &#187;</span>
                                </li>
                                <li class="divider"></li>
                                <ul class="box-with-shadow-child">
                                    <li @click="selectField(field, mainModel)" v-for="field in mainModel.fields">{{field}}</li>
                                </ul>
                            </ul>


                            <div v-if="hasRelation()" v-for="(rModel, index) in relationModels.models">
                                <ul class="box-with-shadow">
                                    <li class="box-with-shadow-header">{{rModel.model}} -- {{rModel.relationAlias}} <span
                                            class="text-small pull-right hand" @click="selectAllField(mainModel, rModel.relationAlias)">Add all &#187;</span>
                                    </li>
                                    <li class="divider"></li>
                                    <ul class="box-with-shadow-child" style="padding-left: 5px">
                                        <li @click="selectField(field, mainModel, rModel.relationAlias)" v-for="field in rModel.fields">
                                            {{field}}
                                        </li>
                                    </ul>
                                </ul>
                            </div>


                        </div>

                        <div class="col-sm-8 clearfix">
                            <ul class="box-with-shadow">
                                <li class="box-with-shadow-header">Selected Fields<span
                                        class="text-small pull-right hand" @click="removeAllField(mainModel)">Remove all</span>
                                </li>
                                <li class="divider"></li>
                                <ul class="box-with-shadow-child-inline" style="padding-left: 5px" v-if="mainModel.selected.length > 0" id="selectedFields">
                                    <li class="fields" :data-fieldname="val" v-for="(val, index) in mainModel.selected">{{val}} <span
                                            @click="removeField(mainModel, index)"
                                            class="small fa fa-close"></span></li>
                                </ul>
                            </ul>
                        </div>

                    </div>

                </div>
            </div>

            <div class="row">
                <div class="alert alert-danger" v-if="errorMessage!=''">{{errorMessage}}</div>
                <div class="form-group center-align">
                    <input type="submit" name="submit" value="Save" class="btn btn-success"/>
                    <a :href="dataBackUrl" class="btn btn-default">Done</a>
                </div>
            </div>
        </form>
    </div>
</template>


<script>

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


    import {Toast} from '../helpers/Common';
    import Form from '../helpers/Form';
    import Sortable from 'sortablejs';

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
                  return AdminConfig.admin_path(this.dataControllerName+'/createModule');
              }
        },
        data() {
            return {
                allTables: (typeof this.dataDatabaseTables !== "undefined") ? JSON.parse(this.dataDatabaseTables) : [],
                allModules:(typeof this.dataCmsModules !== "undefined") ? JSON.parse(this.dataCmsModules) : [],
                mainModel: {tableName: '', fields: [], modelName: '', selected: []},
                relationModels: {tableName: '', models: []},

                form: new Form({
                    name: '',
                    controller_name: '',
                    validator_name: '',
                    dataWith: [],
                    dataSource:'',
                    createFiles: true,
                    selectedFields:[],
                    relationModels:[],
                    parent_id:'',
                    display_name:'',
                    sub_title:'',
                    icon_css:'',
                    list_view_name:'',
                    edit_view_name:''
                }),
                errors:{},
                cacheData: {},
                sortable:null,
                sortingInterval:-1,
                errorMessage:''
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

                var tableName = modelName;
                var $this = this;
                modelName = pluralize.singular(modelName) + "::class";
                var arr = modelName.split("_");
                arr = arr.map(a => a.charAt(0).toUpperCase() + a.substr(1, a.length));

                return arr.join("");

            },
            populateMainDataFields() {
                var $this = this;
                if (this.mainModel.tableName != "") {

                    DbData.getFields(this.mainModel.tableName).then(function (res) {
                        $this.mainModel.fields = res.data;
                    });
                    this.mainModel.modelName = this.getModelName(this.mainModel.tableName);
                    this.form.dataSource = this.mainModel.modelName;

                };

            },
            updateWithData() {
                this.form.dataWith = [];
                for (let i = 0; i < this.relationModels.models.length; i++) {
                    let current = this.relationModels.models[i];
                    if (this.form.dataWith.indexOf(current.relationAlias) == -1) {
                        this.form.dataWith.push(current.relationAlias);
                    }
                    ;
                }
                ;

            },
            addRelationModel() {

                if (this.relationModels.tableName != "") {

                    let tableName = this.relationModels.tableName;
                    let modelName = this.getModelName(tableName);


                    if (this.hasInRelationModel(modelName) == false) {

                        let relationAlias = "";
                        if (tableName.endsWith("_langs") || tableName.endsWith("_sites")) {
                            relationAlias = tableName.endsWith("_langs") ? "lang" : "site";
                        } else {
                            relationAlias = pluralize.singular(tableName.replace(/_/g, "")).toLowerCase();
                        }

                        let isLanguage = (relationAlias=="lang") ? true : false;

                        let relationType = 'hasMany';

                        let relationalData = {
                            model: modelName,
                            relationAlias: relationAlias,
                            relationType: relationType,
                            isLanguage:isLanguage,
                            fields: [],
                            selected: []
                        };


                        if (!this.cacheData[modelName]) {
                            this.relationModels.models.push(relationalData);
                        }
                        //Add in cache
                        this.cacheData[modelName] = relationalData;

                        DbData.getFields(tableName).then(function (res) {
                            relationalData.fields = res.data;
                        });

                        this.updateWithData();
                    }
                    ;
                }
                ;

            },
            hasInRelationModel(modelName) {
                return this.cacheData[modelName] || false;
            },
            updateRelationModel(event, index, key) {
                let alias = this.relationModels.models[index][key];
                this.relationModels.models[index][key] = event.target.value;

                if (key == "relationAlias") {
                    //reset
                    this.relationModels.models[index].selected = [];
                    this.removeSelectedRelationFields(alias);
                }

                this.updateWithData();

            },
            removeSelectedRelationFields(alias) {
                if(this.mainModel.selected.length>0) {
                    var selected = [];
                    for(let i=0;i<this.mainModel.selected.length;i++) {
                        let current = this.mainModel.selected[i];
                        if(!current.startsWith(alias+".")) {
                            selected.push(current);
                        } else {
                            // this.mainModel.selected.splice(i, 1); this is not working
                        };
                    };
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
                return this.mainModel.tableName != "";
            },
            selectAllField(where, relation="") {

                if(where.selected.length ==0) {
                    where.selected = where.fields.slice();
                    if (relation!="") {
                        where.selected = where.selected.map(c => (relation + "." + c));
                    }
                } else {
                    for(let i=0;i<where.fields.length;i++) {
                        this.selectField(where.fields[i], where, relation);
                    }
                }

                this.enableSorting();

            },
            selectField(field, where, relation="") {

                field = (relation!="") ? relation + "." + field : field;

                if (where.selected.indexOf(field) == -1) {
                    where.selected.push(field);
                };

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
                if (name != "") {
                    let $this = this;
                    axios.get("isControllerExists?name=" + name).then(
                        function (res) {
                            if (res.data == 1) {
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
                    if(this.sortable!=null) {
                        this.sortable.destroy();
                    }
                    var el = document.getElementById('selectedFields');
                    this.sortable = Sortable.create(el, {
                        onEnd: this.sortingCallback,
                        onStart:this.cancelSortingCallback
                    });
                });
            },
            cancelSortingCallback() {
                if(this.sortingInterval != -1) {
                    clearInterval(this.sortingInterval);
                }
            },
            sortingCallback() {

                this.cancelSortingCallback();

            },
            setSortedFields() {
                let items = document.querySelectorAll("#selectedFields li");
                let count = items.length;
                if(count>1) {
                    let selected = [];
                    for(let i=0;i<count;i++) {
                        let current = items[i];
                        let field = current.getAttribute("data-fieldname");
                        selected.push(field);
                    }
                    this.form.selectedFields = selected;
                };
            },
            showError(res) {
                this.errors = {};
                for(let i in res.errors) {
                    if(res.errors.hasOwnProperty(i)) {
                        this.$set(this.errors, i, res.errors[i][0]);
                    }
                }

                this.errorMessage = res.message;

            },
            hideErrorMessage(event) {

                let name = event.target.getAttribute("name");
                this.$set(this.errors, name, "")

                if(this.errorMessage != '') {
                    this.errorMessage = ''
                }
            },
            resetForm(response) {
                this.mainModel = {tableName: '', fields: [], modelName: '', selected: []};
                this.relationModels = {tableName: '', models: []};

                if(response.created == 0) {
                    Toast.show(this, "There is some error...");
                    this.errorMessage = response.message;
                } else {
                    Toast.show(this, "Created...");
                }

            }

        }
    }

</script>
