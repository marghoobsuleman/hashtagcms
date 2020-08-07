<template>
  <div>
    <div v-if="superAdmin" class="alert alert-info">{{userModules.name}} is admin or super admin. No need to provide module access. </div>
    <form v-if="!superAdmin"  :action="permissionSaveUrl" method="post" class="form-horizontal" role="form"
          v-on:submit.prevent="saveData">
      <div class="checkbox title">
          <h4>
            <label>
              <input @click="selectAll()" type="checkbox" v-model="selectAllModule"  />
              Select
            </label>
              modules for <span class="text-danger">{{userModules.name}} {{userModules.last_name}}</span>
          </h4>
      </div>
      <ul class="list-unstyled list-permission">
        <li v-for="module in form.cmsModuleData" v-if="isParent(module)" class="clearfix">
          <div class="clearfix pb5">
            <div class="checkbox normal col-sm-3">
              <label>
                <input @click="selectMe(module)" type="checkbox" v-model="module.selected" /> {{module.name}}
              </label>
            </div>

            <div :class="'checkbox normal col-sm-3 '+showHide(module)">
              <label> <input type="checkbox" @click="selectReadOnly(module)" v-model="module.readonly" /> Read Only</label>
            </div>

          </div>

          <ul v-if="hasChild(module)" class="list-unstyled clearfix" style="margin-left:20px">

            <li v-for="child in module.child" class="clearfix">

              <div class="checkbox normal col-sm-3">
                <label>
                  <input type="checkbox" @click="selectMe(child, module)"  v-model="child.selected" /> {{child.name}}
                </label>
              </div>

              <div :class="'checkbox normal col-sm-3 '+showHide(child)">
                <label>
                  <input type="checkbox" @click="selectReadOnly(child)" v-model="child.readonly" /> Read Only
                </label>
              </div>

            </li>
          </ul>
        </li>
      </ul>

      <div class="row">
        <div class="alert alert-danger" v-if="errorMessage!=''">{{errorMessage}}</div>
        <div class="form-group center-align">
          <input type="submit" name="submit" value="Save" class="btn btn-success"/>
          <a :href="dataBackUrl" class="btn btn-default">Back</a>
        </div>
      </div>
    </form>
    <div class="row" v-if="superAdmin">
      <div class="form-group center-align">
        <a :href="dataBackUrl" class="btn btn-default">Okay :)</a>
      </div>
    </div>
  </div>
</template>

<script>

import Form from '../helpers/Form';
import {Toast} from '../helpers/Common'

export default {
  mounted() {

      this.form.cmsModuleData = this.allModules;
      this.form.userId = this.userModules.id
      //console.log(this.form.userId);
      this.showSuperAdmin();

      console.log(this.form.cmsModuleData);
  },
  props:[
      'dataCmsModules',
      'dataUserModules',
      'dataControllerName',
      'dataBackUrl',
      'dataIsSuperAdmin'
  ],
  data() {
    return {
        userModules:(this.dataUserModules) ? JSON.parse(this.dataUserModules) : [],
        form: new Form({
            cmsModuleData:[],
            userId:0
        }),
        selectAllModule:false,
        superAdmin:false,
        errorMessage:''
    }
  },
  computed: {
    allModules() {
      let userModules = this.userModules.cmsmodules;

      let modules = (this.dataCmsModules) ? JSON.parse(this.dataCmsModules) : [];
      console.log(modules);
      let filterModules = [];
      if(modules.length>0) {
          for(let i=0;i<modules.length;i++) {
              let current = modules[i];
              let found = findModule(current);
              current.selected = found;
              current.readonly = found.readonly;
              if(current.parent_id==0 && current.child.length>0) {
                  for(let c=0;c<current.child.length;c++) {
                    let child = current.child[c];
                    let found = findModule(child);
                    child.selected = (found) ? true : false;
                    child.readonly = found.readonly;
                  }
              };
              if(current.parent_id==0) {
                  filterModules.push(current);
              }
          }
      };
      return filterModules;

      function findModule(currentModule) {

          if(userModules.length>0) {

              let found = userModules.find(function(current) {
                  return current.module_id == currentModule.id;
              });

              return (found == undefined) ? false : found;

          }
          return false;
      }
    },
      permissionSaveUrl() {

        return AdminConfig.admin_path(this.dataControllerName+"/saveModulePermissions");
      }
  },
  methods: {
    hasChild(data) {
      return (data.child && data.child.length > 0);
    },
    isParent(data) {
      return data.parent_id==0;
    },
    selectReadOnly(current) {

        if(current.readonly) {
            current.selected = true;
        }
    },
    selectAll() {

      var shouldSelect = (this.selectAllModule==false) ? true : false;

      for(let i=0;i<this.form.cmsModuleData.length;i++) {
          let current = this.form.cmsModuleData[i];
          current.selected = shouldSelect;

          this.selectMe(current, undefined, shouldSelect);
      }
    },
    selectMe(current, parentModule, forcedSelectAll=null) {

      var shouldSelect = (current.selected == false) ? true : false;

      //if forcing
      if(forcedSelectAll != null) {
          shouldSelect = forcedSelectAll;
      }

      if(current.child && current.child.length>0) {

          current.child.map(function(c) {

              c.selected = shouldSelect;
              //reset readonly
              if(!shouldSelect) {
                  c.readonly = false;
              }

          })
      }

      if(!current.selected) {
          current.readonly = false;
      }

      //check if parent should be selected
      if(parentModule && forcedSelectAll==false) {

          let isAnySelected = parentModule.child.find(function(current) {
              return current.selected == true;
          });

          if(shouldSelect || isAnySelected) {
              parentModule.selected = true;
          };

      }

        //Check if one or more is unchecked. reset select all tab
        if(shouldSelect==false) {
            this.selectAllModule = false;
        };

    },
    showHide(source) {
        //console.log("source.readonly "+source.readonly);
        return (!source.readonly) ? "readonly" : "";
    },
    showSuperAdmin() {
      if(this.dataIsSuperAdmin == "1") {
          this.superAdmin = true;
      }
    },
    saveData() {

        this.form.post(this.permissionSaveUrl, false)
            .then(response => this.afterFormSaved(response))
            .catch(response => this.afterFormSaved(response));
    },
    afterFormSaved(response) {

      if(response.isSaved == true) {
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
