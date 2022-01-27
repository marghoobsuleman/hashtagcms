<template>
<div>
    <select :name="name" :id="id" @change="setPlatform" v-model="selected" :multiple="dataMultiple == 'true' ? multiple : false">
        <option value="">Select Platform</option>
        <option v-for="platform in platforms" :value="platform.id">
            {{platform.name}}
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
            'dataPlatforms',
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

            EventBus.$on("site_changed", this.populatePlatform);
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
              platforms:(typeof this.dataPlatforms === "undefined") ? [] : JSON.parse(this.dataPlatforms),
              name:(typeof this.dataName === "undefined") ? "platform_id" : this.dataName,
              id:(typeof this.dataId === "undefined") ? "platform_id" : this.dataId,
              multiple:(typeof this.dataMultiple === "undefined") ? "" : "multiple='multiple'",
              siteId:(typeof this.dataSiteId === "undefined") ? 1 : parseInt(this.dataSiteId),
              fetchOnInit:(typeof this.dataFetchOnInit === "undefined" || this.dataFetchOnInit === "false") ? false : this.dataFetchOnInit,
              isLoading:false,
              selectedValue:this.selected
          }
        },
        methods: {
            init() {
                this.populatePlatform(this.siteId);
            },
            setPlatform() {
                EventBus.$emit("platform_changed", this.selected);
            },
            populatePlatform(siteId=null) {
                if(siteId === null || siteId === "") {
                    this.platforms = [];
                    return false;
                }
                this.isLoading = true;

                let $this = this;
                let platformUrl = AdminConfig.admin_path("ajax/getInfo/site/"+siteId);
                axios.get(platformUrl).then(function(res) {
                    $this.platforms = res.data.results.platform;
                    $this.isLoading = false;
                }).catch(function(res) {
                    console.error(res.data);
                });
            }
        }
    }
</script>