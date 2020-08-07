<template>
    <div>
        <form id="addEditFrm" :action="saveURL" method="post" class="form-horizontal" role="form"
              v-on:submit.prevent="createModule" v-on:keyup="hideErrorMessage($event)">

            <div class="form-group">
                <label class="col-sm-3" for="name">Name</label>
                <div class="col-sm-7">
                    <input type="text" required class="form-control" id="name" name="name" v-model="form.name"
                           placeholder="Module name"/>
                    <label><input @click="goConventional()" type="checkbox" v-model="conventional" /> Go Conventional</label>
                    <div class="text text-danger">{{this.errors.name}}</div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3" for="alias">Alias</label>
                <div class="col-sm-7">
                    <input type="text" required class="form-control" id="alias" name="alias" v-model="form.alias"
                           placeholder="Alias"  />
                    <div class="text text-danger">{{this.errors.alias}}</div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3" for="view_name">View Name</label>
                <div class="col-sm-7">
                    <input type="text" required class="form-control" id="view_name" name="view_name" v-model="form.view_name"
                           placeholder="View name"/>
                    <div class="text text-danger">{{this.errors.view_name}}</div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3" for="cache_group">Cache Group</label>
                <div class="col-sm-7">
                  <input type="text"  class="form-control" maxlength="60" id="cache_group" name="cache_group" v-model="form.cache_group"
                         placeholder="Cache Category"/>

                    <div class="text text-danger">{{this.errors.cache_group}}</div>
                </div>
            </div>

            <div class="form-group hide">
                <label class="col-sm-3" for="individual_cache">Is Individual Cache ?.</label>
                <label class="col-sm-7">
                   <input type="checkbox" id="individual_cache" name="individual_cache" v-model="form.individual_cache"/>
                </label>
            </div>


            <div class="form-group">
                <label class="col-sm-3" for="data_type">Data Type</label>
                <div class="col-sm-7">
                    <select @change="checkForService(form.data_type)" class="form-control select-big" v-model="form.data_type" id="data_type" name="data_type">
                        <option value="">Select</option>
                        <option v-for="dataType in allDataTypes" :value="dataType">
                            {{dataType}}
                        </option>
                    </select>
                    <div class="text text-danger">{{this.errors.data_type}}</div>
                    <div class="alert alert-info" v-show="form.data_type != ''" v-html="this.dataTypesInfo[form.data_type] || '' "></div>
                </div>
            </div>

            <div v-show="showQueryForm">
            <div class="form-group">
                <label class="col-sm-3" for="method_type">Method Type</label>
                <div class="col-sm-7">
                    <select  class="form-control select-big" v-model="form.method_type" id="method_type" name="method_type">
                        <option value="">Select</option>
                        <option v-for="methodType in allMethodTypes" :value="methodType.value">
                            {{methodType.name}}
                        </option>
                    </select>
                    <div class="text text-danger">{{this.errors.data_type}}</div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3" for="service_params">Service Params</label>
                <div class="col-sm-7">
                    <input type="text"  class="form-control" id="service_params" name="service_params" v-model="form.service_params"
                           placeholder="Service Params"  />
                    <div class="text text-danger">{{this.errors.service_params}}</div>
                </div>
            </div>
          </div>

            <div class="form-group ">
                <label class="col-sm-3" for="is_seo_module">Use this module as seo module</label>
                <label class="col-sm-7">
                    <input type="checkbox" id="is_seo_module" name="is_seo_module" v-model="form.is_seo_module"/>
                </label>
            </div>

            <div class="form-group ">
                <label class="col-sm-3" for="is_mandatory">Is Mandatory ?</label>
                <label class="col-sm-7">
                   <input type="checkbox" id="is_mandatory" name="is_mandatory" v-model="form.is_mandatory"/>
                </label>
            </div>


            <div class="form-group ">
                <label class="col-sm-3" for="is_user_cache">Is User Cache?</label>
                  <label class="col-sm-7">
                   <input type="checkbox" id="is_user_cache" name="is_user_cache" v-model="form.is_user_cache"/>
                </label>
            </div>

            <div class="form-group ">
                <label class="col-sm-3" for="data_handler">Data Handler</label>
                <label class="col-sm-7">
                   <textarea rows="5" id="data_handler" name="data_handler" v-bind:style="fluidCol" v-model="form.data_handler"/>
                </label>
            </div>

            <div class="form-group ">
                <label class="col-sm-3" for="data_key_map">Data Key Map</label>
                <label class="col-sm-7">
                   <textarea rows="5"  v-bind:style="fluidCol" id="data_key_map" name="data_key_map" v-model="form.data_key_map"/>
                </label>
            </div>

            <div class="form-group " v-show="showQueryForm">
               <fieldset class="border">
                 <legend>
                     Query Service Properties
                 </legend>
                   <div class="form-group">

                     <label class="col-sm-3" v-bind:style="sFont" for="query_statement">Query</label>

                     <label class="col-sm-7">
                       <textarea rows="5"  v-bind:style="fluidCol" id="query_statement" name="query_statement" v-model="form.query_statement"/>
                     </label>
                   </div>

                   <div class="form-group">
                       <label class="col-sm-3" v-bind:style="sFont" for="query_as">Get query data as</label>
                       <div class="col-sm-7">
                           <select  class="form-control select-big" v-model="form.query_as" id="query_as" name="query_as">
                               <option value="">Select</option>
                               <option v-for="queryData in allQueryDataTypes" :value="queryData.value">
                                   {{queryData.name}}
                               </option>
                           </select>
                           <div class="text text-danger">{{this.errors.query_as}}</div>
                       </div>
                   </div>

                 </fieldset>
            </div>

            <div class="form-group ">
                <label class="col-sm-3" for="description">Description</label>
                <label class="col-sm-7">
                   <textarea rows="5"  v-bind:style="fluidCol" id="description" name="description" v-model="form.description"/>
                </label>
            </div>

            <div class="form-group">
                <label class="col-sm-3" for="site_id">Site</label>
                <div class="col-sm-7">
                    <select class="form-control select-big" v-model="form.site_id" id="site_id" name="site_id">
                        <option value="">Select</option>
                        <option v-for="site in siteData" :value="site.id">
                            {{site.name}}
                        </option>
                    </select>
                    <div class="text text-danger">{{this.errors.site_id}}</div>
                </div>

            </div>
            <div class="form-group" v-show="this.showAllUpdateSection">
                <label class="col-sm-3" for="update_inAllSites">Update in all sites ?</label>
                <label class="col-sm-7">
                    <input type="checkbox" id="update_inAllSites" name="update_inAllSites" v-model="form.update_inAllSites"/>
                    <div class="alert alert-info">
                    Be careful by clicking this checkbox. Content will be updated in all sites based on alias.
                    If you are changing alias it will be updated only in this site.
                </div>
                </label>
            </div>
            <div class="row">
                <div class="alert alert-danger" v-show="typeof errorMessage == 'string' && errorMessage != '' ">{{errorMessage}}</div>
                <div class="form-group center-align">
                    <input type="submit" name="submit" value="Save" class="btn btn-success"/>
                    <a :href="dataBackUrl" class="btn btn-default">Back</a>

                </div>
            </div>
        </form>
    </div>
</template>


<script>
    import {Toast} from '../helpers/Common';
    import Form from '../helpers/Form';
    import Sortable from 'sortablejs';

    export default {


        mounted() {
          if(this.dataActionPerformed=="edit"){
            this.setFormData(this.formData);
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
            'dataFormAction'

        ],
        data() {
            return {
              sFont:{
                'font-size':'14px'
              },
              fluidCol:{
                'width':'100%'
              },allQueryDataTypes:[
                {'name':'param','value':'param'},
                {'name':'data','value':'data'},
              ],
              conventional:false,
              formData:(typeof this.dataResults!="undefined") ? JSON.parse(this.dataResults) : [],
              siteData:(typeof this.dataSite !== "undefined") ? JSON.parse(this.dataSite) : [],
              showExtraData:{},
              allDataTypes:(typeof this.dataDataTypes!="undefined") ? JSON.parse(this.dataDataTypes) : [],
                allMethodTypes:[
                  {'name':'GET','value':'GET'},
                  {'name':'POST','value':'POST'}
                ],
                form: new Form({
                    id:0,
                    name: "",
                    icon_css:"",
                    alias:"",
                    view_name:"",
                    cache_group:"",
                    data_type:"",
                    method_type:"",
                    data_handler:"",
                    data_key_map:"",
                    query_statement:"",
                    query_as:"",
                    description:"",
                    site_id:1,
                    query_statement:"",
                    query_as:"",

                    is_seo_module:0,
                    is_user_cache:0,
                    individual_cache:0,
                    update_inAllSites:0,
                    is_mandatory:0,
                    service_params:"",

                    actionPerformed:this.dataActionPerformed,
                    backURL:this.dataBackUrl
            }),
                errors:{},
                cacheData: {},
                sortable:null,
                sortingInterval:-1,
                errorMessage:'',
                dataTypesInfo:(typeof this.dataDataTypesInfo!="undefined") ? JSON.parse(this.dataDataTypesInfo) : [],
                saveURL:this.dataFormAction
            }
        },
        computed:{
          showQueryForm: function () {
            if(this.form.data_type.indexOf('Service') > -1){
              return true;
            }else{
              return false;
            }
          },
            showAllUpdateSection: function () {

                return (this.dataActionPerformed == 'edit' && this.siteData.length > 1);
            }

        },
        methods: {
            goConventional(arg) {
                //wanted to go conventional
                if(this.conventional==false) {
                    var name = this.form.name;
                    this.form.title = name;
                    this.form.alias = ("MODULE_"+name.toUpperCase()).replace(/\s/g, "_");
                    this.form.view_name = (name.toLowerCase()).replace(/\s/g, "-");
                }
            },
            setFormData(data){
              var data_length = Object.keys(data).length;
              for(var a in data){
                    this.form[a] = data[a];
              }
            },
            checkForService(dataType){
              var self = this
              if(dataType!="" && (dataType.indexOf('Service') >-1 || dataType.indexOf('service') >-1)){
                this.$set(this.showExtraData,'show',true);
                //console.log(this.showExtraData);
              }else{
                this.$set(this.showExtraData,'show',false)
              }
            },
            createModule() {
                var $this = this;

                preCheck(this);

                this.form.post(this.saveURL)
                    .then(response => this.resetForm(response))
                    .catch(response => this.showError(response));

                function preCheck() {

                    $this.form.is_seo_module = ($this.form.is_seo_module) ? $this.form.is_seo_module : 0;
                    $this.form.is_user_cache = ($this.form.is_user_cache) ? $this.form.is_user_cache : 0;
                    $this.form.individual_cache = ($this.form.individual_cache) ? $this.form.individual_cache : 0;
                    $this.form.update_inAllSites = ($this.form.update_inAllSites) ? $this.form.update_inAllSites : 0;
                    $this.form.is_mandatory = ($this.form.is_mandatory) ? $this.form.is_mandatory : 0;
                    $this.form.service_params = ($this.form.service_params) ? $this.form.service_params : "";
                }
            },
            showError(res) {
                //console.log("res");
                //console.log(res);
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

                //console.log(response);
                //console.log("response.isSaved ",response.isSaved);
                if(response.isSaved == 0) {
                    this.errorMessage = {};
                    Toast.show(this, "There is some error...");
                    this.errorMessage = response.message;

                } else {

                    this.errorMessage = '';

                    var action = this.dataActionPerformed.toUpperCase()

                    Toast.show(this, "Saved...");

                    if(response.isSaved==true) {
                        window.location.href = response.backURL;
                    }

                }
            }
        }
    }

</script>
