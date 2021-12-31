<template>
  <div>
      <form :action="savePollOptions" method="post" class="form-horizontal" role="form"
            v-on:submit.prevent="savePollOptions">

          <div class="form-group" v-for="result in this.results.lang">
              <div class="col-sm-2">
                  <label>Poll Name</label>
              </div>
              <div class="col-sm-10">
                  <input v-model="result.name" type="text" class="form-control" name="poll_title" required placeholder="poll title" />
              </div>
          </div>

          <div class="form-group" v-for="(option, index) in this.results.option">
              <div class="col-sm-2">
                  <label>Poll Option</label>
              </div>
              <div class="col-sm-8">
                  <input v-model="option.name" type="text" class="form-control" name="option_name" required placeholder="poll name" />
              </div>
              <div class="col-sm-2" style="float:right" v-if="option.id==0">
                  <button v-on:click="deleteOptionRow(index)" class="btn btn-danger">Delete</button>
              </div>
          </div>

          <div class="form-group">
              <div class="col-sm-2" style="float:right">
                  <button v-on:click="addOptionRow" class="btn btn-info">Add poll option</button>
              </div>
          </div>

          <fieldset class="fieldset">
              <legend>Details:</legend>

          <div class="form-group">
              <div class="col-sm-2">
                  <label>Published ?</label>
              </div>
              <div class="col-sm-10">
                  <label title="Published"><input type="checkbox" v-model="results.publish_status" /></label>
              </div>
          </div>

          <div class="form-group">
              <div class="col-sm-2">
                  <label>Start Date : </label>
              </div>
              <div class="col-sm-10">
                  <label title="Published"><input type="date" v-model="results.start_date" /></label>
              </div>
          </div>

          <div class="form-group">
              <div class="col-sm-2">
                  <label>End Date : </label>
              </div>
              <div class="col-sm-10">
                  <label title="Published"><input type="date" v-model="results.end_date" /></label>
              </div>
          </div>

          <div class="form-group">
              <div class="col-sm-2">
                  <label>Select Site </label>
              </div>
              <div class="col-sm-10">
                  <select class="form-control select" v-model="results.site_id" >
                      <option>Select a Site</option>
                      <option v-for="site in sites" :value="site.id">{{site.name}}</option>
                  </select>
              </div>
          </div>

          <div class="form-group">
              <div class="col-sm-2">
                  <label>Select Country </label>
              </div>
              <div class="col-sm-10">
                  <select class="form-control select" v-model="results.country_id" >
                      <option>Select a Country</option>
                      <option v-for="country in countries" :value="country.country_id">{{country.name}}</option>
                  </select>
              </div>
          </div>

          </fieldset>

          <div class="row">
              <div class="alert alert-danger" v-if="errorMessage!=''">{{errorMessage}}</div>
              <div class="form-group center-align">
                  <input type="submit" name="submit" value="Save" class="btn btn-success"/>
                  <a :href="dataBackUrl" class="btn btn-default">Cancel</a>
              </div>
          </div>
      </form>
  </div>
</template>

<script>

import {Toast} from '../helpers/Common';

export default {

    mounted() {
        if(this.dataResults=="[]"){
            this.results = this.defaultResult;
            for(var i =0; i<3; i++){
                this.addOptionRow();
            }
        }else{
            this.results.start_date = (new Date(this.results.start_date)).toISOString().slice(0,10);
            this.results.end_date = (new Date(this.results.end_date)).toISOString().slice(0,10);
        }
    },
    props: [
        'dataResults',
        'dataSites',
        'dataCountries',
        'dataControllerName',
        'dataBackUrl'
    ],
    computed: {
        savePoll: function () {
            return AdminConfig.admin_path(this.dataControllerName+'/savePollData');
        }
    },
    data() {
      return {
          title:'',
          errorMessage:'',
          results: JSON.parse(this.dataResults),
          sites: JSON.parse(this.dataSites),
          countries: JSON.parse(this.dataCountries),
          defaultResult:{
              id: 0,
              publish_status: 0,
              start_date: "",
              end_date: "",
              site_id: 0,
              country_id: 0,
              created_at: "",
              updated_at: "",
              deleted_at: "",
              lang: [
                  {
                      id: 0,
                      poll_id: 0,
                      lang_id: 0,
                      name: "",
                      created_at: "",
                      updated_at: "",
                      deleted_at: ""
                  }
              ],
              option: [
                  {
                      id: 0,
                      poll_option_id: 0,
                      lang_id: 0,
                      name: "",
                      created_at: "",
                      updated_at: "",
                      deleted_at: "",
                      poll_id: 0
                  }
              ]
          },
          baseInfo:{
              id:0,
              poll_option_id:0,
              lang_id:0,
              name:"",
              created_at:"",
              updated_at:"",
              deleted_at:"",
              poll_id:0
          }
      }
    },
    methods: {
        getAllInputs: function() {
            console.log("all inputs");
        },
        addOptionRow: function() {
            this.results.option.push(JSON.parse(JSON.stringify(this.baseInfo)));
            if(event){
                event.preventDefault();
            }
        },
        savePollOptions() {
            if(this.results.publish_status){
                this.results.publish_status = 1;
            }else{
                this.results.publish_status = 0;
            }
            let url = AdminConfig.admin_path(this.dataControllerName+'/savePollData');
            let $this = this;
            this.saveNow(url, this.results).then(function(res) {
                if(res.data.error) {
                    Toast.show($this, res.data.error);
                    console.log(res.data.error);
                } else if(res.data.success) {
                    Toast.show($this, "Saved");
                    console.log("Saved ...");
                    window.location.href = AdminConfig.admin_path($this.dataControllerName);
                }
            }).catch(function (res) {
                console.error(res.message);
                Toast.show($this, "Some error occured... ");
            });

        },
        deleteOptionRow(index) {
            this.results.option.splice(index, 1);
            if(event){
                event.preventDefault();
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
        }
    }
}

</script>
