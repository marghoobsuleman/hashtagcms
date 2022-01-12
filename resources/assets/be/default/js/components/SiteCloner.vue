<template>
  <div>
    <div class="row">
      <div class="form-group">
        <div class="col-sm-2">
          Copy From:
        </div>
        <div class="col-sm-4">
          <select class="form-control" v-model="sourceSiteId">
            <option value="">Select Site</option>
            <option v-for="site in allSites" :value="site.id">{{ getLabel(site) }}</option>
          </select>
        </div>
      </div>
      </div>
    <div class="row margin-top-20">
      <div class="form-group">
        <div class="col-sm-2">
          Copy To:
        </div>
        <div class="col-sm-4">
          <select class="form-control" v-model="targetSiteId">
            <option value="">Select Site</option>
            <option v-for="site in allSites" :value="site.id">{{ getLabel(site) }}</option>
          </select>
        </div>
      </div>
    </div>
    <div class="row margin-top-20">
      <div class="form-group">
        <div class="col-sm-2">
          &nbsp;
        </div>
        <div class="col-sm-4">
          <div v-show="errorMsg !='' && !isLoading" class="alert alert-danger text-center">{{errorMessage}}</div>
          <div v-show="isLoading == 1" class="alert alert-info text-center">
            <span class="fa fa-spinner fa-pulse fa-fw">&nbsp;</span>&nbsp;Please wait and don't close this window. It's not running in thread :)
          </div>
          <div v-show="isLoading > 1" class="alert alert-info">
            <ul>
              <li v-for="msg in successMsg">
                <span :class="(msg.success === false) ? 'text-danger' : ''">
                    {{msg.message}}
                </span>

              </li>
            </ul>
          </div>
          <input @click="doAction()" type="button" name="submit" value="Copy Now!" class="btn btn-block btn-success" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import {Toast} from '../helpers/Common';

export default {

  mounted() {
    //console.log(this.allData);
    //  console.log(this.allData.data[0]);
    //console.log(this.cuurent);
    // this.initData();

  },
  created() {
    //this.initData();
  },

  props: [
    'dataAllSites'
  ],
  data() {
    return {
      allSites: (typeof this.dataAllSites === "undefined") ? [] : JSON.parse(this.dataAllSites),
      sourceSiteId: '',
      targetSiteId: '',
      isLoading:false,
      errorMsg:'',
      successMsg:[]
    }
  },
  computed: {
    errorMessage() {
      return this.errorMsg === '' ? "It's undoable. Please be careful." : this.errorMsg;
    }
  },
  methods: {
    getLabel(data) {
      var label = "";
      if (data.name || data.alias) {
        label = data.name || data.alias;
      } else if (data.lang) {
        label = (typeof data.lang.name == "undefined") ? "" : data.lang.name;
      }
      return label;

    },
    loading(isLoading) {
      this.isLoading = isLoading;
    },
    cloneSite() {
      console.log("copying now...");
      //console.log(allData, seletedData, ids);
      //get all from
      this.loading(1);
      let $this = this;
      let postData = {sourceSiteId: this.sourceSiteId, tagetSiteId: this.targetSiteId};

      let url = AdminConfig.admin_path("site/cloneSite");
      axios.post(url, postData).then(response => {
        console.log(response);
        feedback(response);
        this.loading(2);
      }).catch(error => {
         console.log("Error: ", error.response);
          $this.errorMsg = error.response.data.message;
        this.loading(0);
      });

      function feedback(response) {
        let data = response.data;
        $this.successMsg = data;
      }

    },
    doAction() {
      this.cloneSite();
    }
  }
}

</script>
