<template>
  <div>
    <form :action="actionUrl" method="get" v-on:submit.prevent="searchNow" class="form-inline" role="form">
     <div class="form-group">
       <select name="fields" v-model="searchParams.f" class="form-control" @change="changeInputText()">
         <option v-for="field in searchFields" :value="getFieldName(field, 'key')">{{getFieldName(field)}}</option>
       </select>
     </div>
      <div class="form-group">
        <select name="fields" v-model="searchParams.o" class="form-control">
          <option v-for="operator in operators" :value="operator">{{operator}}</option>
        </select>
      </div>

    <div class="input-group">
      <input :type="inputType" v-model="searchParams.q" class="form-control" placeholder="Search for...">
      <span class="input-group-btn"><input type="submit" name="submit" value="Search" class="btn btn-success" />
      <input type="reset" name="reset" @click="resetForm()" value="Reset" class="btn btn-default" />
      </span>
    </div>
    </form>
  </div>
</template>

<script>


  export default {

    mounted() {

        this.init();

    },
    props:[
        'dataSelectedParams',
        'dataControllerName',
        'dataFields',
        'dataActionFields'
    ],
      data() {
        return {
            searchParams:{q:'', f:'id', o:'='},
            actionUrl:AdminConfig.admin_path(this.dataControllerName+'/search'),
            searchFields:((this.dataFields && this.dataFields!="null") ? JSON.parse(this.dataFields) : []),
            operators:['=', '!=', '>', '<', '>=', '<=', 'like%', '%like%'],
            hasAction:((this.dataActionFields && this.dataActionFields!="null") ? (JSON.parse(this.dataActionFields).length > 0) : false),
            defaultSearchParams:{q:'', f:'id', o:'='},
            inputType:'text'
        }
      },
    methods: {
      searchNow() {
          var url;
          if(this.searchParams.q.trim()=="") {
              url = AdminConfig.admin_path(this.dataControllerName);
          } else {
              var q = encodeURI(JSON.stringify(this.searchParams));
              var sep = (this.actionUrl.indexOf("?")==-1) ? "?" : "&";
              url = this.actionUrl+sep+"q="+q;
          }

          window.location.href = url;

          return false;

      },
      resetForm() {
          var url = AdminConfig.admin_path(this.dataControllerName);
          //alert(url);
          window.location.href = url;
      },
      getFieldName(key, prop) {
          prop = (prop) ? prop : 'label';
          return (typeof key == "string") ? key : (key[prop]==undefined) ? key : key[prop];
      },
      changeInputText() {
          if(this.searchParams.f.endsWith("_date") || this.searchParams.f.endsWith("_at")) {
            this.inputType = "date";
          } else {
              this.inputType = "text";
          }
      } ,
      init() {

          if(this.hasAction) {
              this.searchFields.pop();
          }

          this.searchParams = JSON.parse(this.dataSelectedParams);

          if(this.searchParams.f == undefined) {
              this.searchParams = this.defaultSearchParams;
          }

      }
    }
  }

</script>
