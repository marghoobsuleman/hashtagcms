<template>
<div>
    <select :name="name" :id="id" @change="setTenant" v-model="selected" :multiple="dataMultiple == 'true' ? multiple : false">
        <option value="">Select Tenant</option>
        <option v-for="tenant in tenants" :value="tenant.id">
            {{tenant.name}}
        </option>
    </select>
    <i v-show="isLoading" class="fa fa-refresh fa-spin" aria-hidden="true"></i>


</div>
</template>

<script>
    import SplitButton from '../library/SplitButton';
    import {EventBus} from "../helpers/event-bus";

    export default {
        components:{
            'split-button':SplitButton
        },
        props:[
            'dataName',
            'dataId',
            'dataTenants',
            'dataSelected',
            'dataMultiple',
            'dataSiteId',
            'dataFetchOnInit'
        ],
        mounted() {
            if(this.fetchOnInit !== false) {
                this.init();
            }

            console.log("selected ",this.selected);

            EventBus.$on("site_changed", this.populateTenant);
        },
        computed: {
            selected: {
                get() {
                    //if this is not multiple
                    if(typeof this.dataMultiple === "undefined" || this.dataMultiple !== "true") {
                        return (typeof this.dataSelected === "undefined") ? 1 : parseInt(this.dataSelected);
                    }
                    //this is multiple
                    return (typeof this.dataSelected === "undefined") ? [1] : JSON.parse(this.dataSelected);
                },
                set(newValue) {
                    return newValue;
                }
            }
        },
        data() {
          return {
              tenants:(typeof this.dataTenants === "undefined") ? [] : JSON.parse(this.dataTenants),
              name:(typeof this.dataName === "undefined") ? "tenant_id" : this.dataName,
              id:(typeof this.dataId === "undefined") ? "tenant_id" : this.dataId,
              multiple:(typeof this.dataMultiple === "undefined") ? "" : "multiple='multiple'",
              siteId:(typeof this.dataSiteId === "undefined") ? 1 : parseInt(this.dataSiteId),
              fetchOnInit:(typeof this.dataFetchOnInit === "undefined" || this.dataFetchOnInit === "false") ? false : this.dataFetchOnInit,
              isLoading:false,
              selectedValue:this.selected
          }
        },
        methods: {
            init() {
                this.populateTenant(this.siteId);
            },
            setTenant() {
                EventBus.$emit("tenant_changed", this.selected);
            },
            populateTenant(siteId=null) {
                if(siteId === null || siteId === "") {
                    this.tenants = [];
                    return false;
                }
                this.isLoading = true;

                let $this = this;
                let tenantUrl = AdminConfig.admin_path("ajax/getInfo/site/"+siteId);
                axios.get(tenantUrl).then(function(res) {
                    $this.tenants = res.data.results.tenant;
                    $this.isLoading = false;
                }).catch(function(res) {
                    console.error(res.data);
                });
            }
        }
    }
</script>