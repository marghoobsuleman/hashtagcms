<template>
    <div>
        <div class="col-md-12">
            <div class="col-md-4">
               Copy From: <select class="form-control select" @change="getBySite()" v-model="currentSite">
                    <option value="">Select Site</option>
                    <option v-for="site in allSites" :value="site.id">{{getLabel(site)}}</option>
                </select>
            </div>
        </div>
        <site-wise ref="sitewise"
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
                siteData:(typeof this.dataSiteData == "undefined" || this.dataSiteData == "") ? [] : JSON.parse(this.dataSiteData),
                allData:(typeof this.dataAllData == "undefined" || this.dataAllData == "") ? [] : JSON.parse(this.dataAllData),
                currentKey:this.dataCurrentKey,
                searchKey:'',
                siteId:parseInt(this.dataSiteId),
                currentSite:''
            }
        },
        methods: {
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
                var label = "";
                if(data.name || data.alias) {
                    label = data.name || data.alias;
                } else if(data.lang) {
                    label = (typeof data.lang.name == "undefined") ? "" : data.lang.name;
                }
                return label;

            },
            populateData(data) {
                this.allData = data;
                //console.log(data);
                this.$refs.sitewise.setData('data', data);
            },
            getBySite() {
                if(this.currentSite != "") {
                    let what = this.currentKey;
                    let url = AdminConfig.admin_path("site/getBySite/"+this.currentSite+"/"+what);
                    axios.get(url).then(response=> {
                        this.populateData(response.data);
                    }).catch(response => {
                        console.log(response);
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

                let url = AdminConfig.admin_path("site/copySettings");
                axios.post(url, postData).then(response=> {
                    console.log(response);
                    feedback(response);
                }).catch(response => {
                    console.log(response);
                });


                function feedback(response) {

                    let data = response.data;
                    //console.log("data.inserted ",data.inserted);
                    if(data.inserted === false) {
                        Toast.show($this, data.message || "Nothing happend. I don't know the reason", 5000);
                    } else {
                        let ignored = data.ignored;
                        let copied = data.copied;
                        $this.$refs.sitewise.setSiteData(data.siteData);

                        //Showing some manners - giving feedback
                        let msg = `${copied.length} Copied and ${ignored.length} ignored. Look at the console for details`;
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
                axios.post(url, postData).then(response=> {
                    feedback(response);
                }).catch(response => {
                    console.log(response);
                });

                function feedback(response) {
                    //console.log(response);
                    let data = response.data;
                    if(data.deleted === 0) {
                        Toast.show($this, "Sorry!, Somehow it's not deleted.", 5000);
                    }
                    $this.$refs.sitewise.setSiteData(data.siteData);
                }

            },
            doAction(action, allData, seletedData, ids) {

                if(seletedData && seletedData.length > 0) {
                    console.log(seletedData)
                }
                if(action == "add") {
                    this.actionAdd(allData, seletedData, ids);
                } else {
                    this.actionRemove(allData, seletedData, ids);
                }

            }
        }
    }

</script>
