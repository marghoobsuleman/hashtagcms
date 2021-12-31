<template>
  <div>
    <modal-box ref="tenantbox" @onClose="hideSpinner">
      <div slot="title">Select Tenant</div>
      <div slot="content" class="container">
        <div class="input-group row">
        <div class="col-xs-8">
          <select v-model="currentTenant" class="form-control select-medium">
            <option value="">Select a tenant</option>
            <option v-for="tenant in tenants" :value="tenant.id">{{tenant.name}}</option>
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
      'dataTenants'
    ],
    data() {
      return {
          modelCss:'modal',
          showHide:true,
          tenants:((this.dataTenants && this.dataTenants!="null") ? JSON.parse(this.dataTenants) : []),
          currentTenant:"",
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
          let tenant_id = this.currentTenant;
          if(tenant_id == "") {
              Toast.show(this, "Please select a tenant");
              return false;
          }
          let target = this.currentTarget;
          let id = target.getAttribute("data-rowid");
          let path = AdminConfig.admin_path(`category/edit/${id}/${tenant_id}`);
          window.location = path;
      },
      open() {
         // console.log(this.tenants);
          if(this.tenants.length > 1) {
              this.$refs.tenantbox.open();
          } else {
              this.currentTenant = this.tenants[0].id;
              this.editNow();
          }


      },
      close() {
          this.$refs.tenantbox.close();
      },
      hideSpinner() {
          EventBus.$emit('list-view-hide-spinner', this);
      }
  }
}

</script>
