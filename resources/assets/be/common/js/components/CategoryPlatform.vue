<template>
  <div>
    <modal-box ref="platformbox" @onClose="hideSpinner">
      <div slot="title">Select Platform</div>
      <div slot="content" class="container">
        <div class="input-group row">
        <div class="col-xs-8">
          <select v-model="currentPlatform" class="form-control select-medium">
            <option value="">Select a platform</option>
            <option v-for="platform in platforms" :value="platform.id">{{platform.name}}</option>
          </select>
        </div>
          <div class="col-xs-4">
            <button type="button" @click="editNow()" class="btn btn-success">Edit Now</button>
          </div>
        </div>
      </div>
    </modal-box>
  </div>
</template>

<script>
import {EventBus} from "../helpers/event-bus";
import {Toast} from "../helpers/Common";

export default {

  mounted() {
      this.verifyAction();
  },
    props:[
      'dataPlatforms'
    ],
    data() {
      return {
          modelCss:'modal',
          showHide:true,
          platforms:((this.dataPlatforms && this.dataPlatforms!="null") ? JSON.parse(this.dataPlatforms) : []),
          currentPlatform:"",
          currentTarget:null

      }

    },
  methods: {
      verifyAction: function() {
          var $this = this;
          EventBus.$on('list-view-pre-edit', function (current) {
              $this.currentTarget = current;
              $this.open();
          });
    },
      editNow() {
          let platform_id = this.currentPlatform;
          if(platform_id == "") {
              Toast.show(this, "Please select a platform");
              return false;
          }
          let target = this.currentTarget;
          let id = target.getAttribute("data-rowid");
          let path = AdminConfig.admin_path(`category/edit/${id}/${platform_id}`);
          window.location = path;
      },
      open() {
         // console.log(this.platforms);
          if(this.platforms.length > 1) {
              this.$refs.platformbox.open();
          } else {
              this.currentPlatform = this.platforms[0].id;
              this.editNow();
          }


      },
      close() {
          this.$refs.platformbox.close();
      },
      hideSpinner() {
          EventBus.$emit('list-view-hide-spinner', this);
      }
  }
}

</script>
