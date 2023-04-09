<template>
    <div class="col mt-2">
        <div class="mb-2">
            Copy From: <select class="form-select inline width-auto" @change="getBySite()" v-model="currentSite">
            <option value="">Select Site</option>
            <option v-for="site in allSites" :value="site.id">{{getLabel(site)}}</option>
        </select>
        </div>
    </div>
    <div class="row">
    <site-wise ref="siteWiseComponent"
               :data-message="dataMessage"
               :data-site-data="JSON.stringify(siteData)"
               :data-all-data="JSON.stringify(allData)"
               :data-current-key="currentKey"
               :data-site-id="siteId"
               :data-default-action-for-save="doAction"
               :data-alert-css="dataAlertCss"
    >

    </site-wise>
    </div>
</template>

<script>

    import {Toast, Loader} from '../helpers/common';
    import SiteWiseData from "./sitewise-data.vue";

    export default {

        mounted() {
           //console.log(this.allData);
          //  console.log(this.allData.data[0]);
            //console.log(this.cuurent);
          // this.initData();

        },
        components: {
          'site-wise': SiteWiseData
        },
        created() {
          //this.initData();
        },

        props: [
            'dataAllSites',
            'dataMessage',
            'dataAllData',
            'dataSiteData',
            'dataCurrentKey',
            'dataSiteId',
            'dataAlertCss'
        ],
        data() {
            return {
                allSites:(typeof this.dataAllSites === "undefined") ? [] : JSON.parse(this.dataAllSites),
                siteData:(typeof this.dataSiteData == "undefined" || this.dataSiteData === "") ? [] : JSON.parse(this.dataSiteData),
                allData:(typeof this.dataAllData == "undefined" || this.dataAllData === "") ? [] : JSON.parse(this.dataAllData),
                currentKey:this.dataCurrentKey,
                searchKey:'',
                siteId:parseInt(this.dataSiteId),
                currentSite:''
            }
        },
        methods: {
            showHdeLoader: function (show) {
                if (show) {
                    Loader.show(this, "Please wait...");
                } else {
                    Loader.hide(this);
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

            },
            getLabel(data) {
                let label = "";
                if(data.name || data.alias) {
                    label = data.name || data.alias;
                } else if(data.lang) {
                    label = (typeof data.lang.name == "undefined") ? "" : data.lang.name;
                }
                return label;

            },
            populateData(data) {
                this.allData = data;
                //console.log("this.$refs.siteWiseComponent ",this.$refs.siteWiseComponent);
                this.$refs.siteWiseComponent.setData('data', data);
            },
            getBySite() {
                if(this.currentSite !== "") {
                    let what = this.currentKey;
                    let url = AdminConfig.admin_path("site/getBySite/"+this.currentSite+"/"+what);
                    this.showHdeLoader(true);
                    axios.get(url).then(response=> {
                        this.populateData(response.data);
                    }).catch(response => {
                        console.log(response);
                    }).finally(()=> {
                        this.showHdeLoader(false);
                    });
                } else {
                    this.populateData([]);
                }

            },
            actionAdd(allData, seletedData, ids) {

                console.log("copying now...");
                //console.log(allData, seletedData, ids);
                //get all from
                let $this = this;
                let postData = {fromSite: {
                                        site_id:this.currentSite,
                                        data:seletedData
                                        },
                                toSite:{site_id:this.siteId},
                                type:this.currentKey
                };
                this.showHdeLoader(true);
                let url = AdminConfig.admin_path("site/copySettings");
                axios.post(url, postData).then(response=> {
                    console.log(response);
                    feedback(response);
                }).catch(response => {
                    console.log(response);
                }).finally(()=> {
                    this.showHdeLoader(false);
                });


                function feedback(response) {

                    let data = response.data;
                    //console.log("data.inserted ",data.inserted);
                    if(data.inserted === false) {
                        Toast.show($this, data.message || "Nothing happened.", 5000);
                    } else {
                        let ignored = data.ignored;
                        let copied = data.copied;
                        $this.$refs.siteWiseComponent.setSiteData(data.siteData);

                        //Showing some manners - giving feedback
                        let msg = `${copied.length} Copied and ${ignored.length} ignored. Open console for details`;
                        Toast.show($this, msg, 7000);
                        console.info("Copied: ", copied);
                        console.info("Ignored: ", ignored);
                    }

                }

            },
            actionRemove(allData, seletedData, ids) {

                let site_id = (seletedData.length > 0) ? seletedData[0].site_id : null;

                if(site_id === null) {
                    return false;
                }

                let $this = this;
                let postData = {
                        site_id:site_id,
                        ids:ids,
                        type:this.currentKey
                    };

                let url = AdminConfig.admin_path("site/removeSettings");
                this.showHdeLoader(true);
                axios.post(url, postData).then(response=> {
                    feedback(response);
                }).catch(response => {
                    console.log(response);
                }).finally(()=> {
                    this.showHdeLoader(false);
                });

                function feedback(response) {
                    //console.log(response);
                    let data = response.data;
                    if(data.deleted === 0) {
                        Toast.show($this, "Sorry!, Somehow it's not deleted.", 5000);
                    }
                    $this.$refs.siteWiseComponent.setSiteData(data.siteData);
                }

            },
            doAction(action, allData, seletedData, ids) {

                if(action === "add") {
                    this.actionAdd(allData, seletedData, ids);
                } else {
                    this.actionRemove(allData, seletedData, ids);
                }

            }
        }
    }

</script>
