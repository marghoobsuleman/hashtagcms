<template>
    <div class="col col-4">
        <div class="card border-0">
            <div class="card-header">
                <h5 class="card-title m-0" style="line-height: inherit">Available {{title}} <span class="badge text-bg-secondary">{{total}}</span>
                    <button type="button" title="Add Selected" @click="doAction('add')" class="hand pull-right btn btn-sm btn-primary">Add Selected</button>
                </h5>
            </div>
            <div class="card-body p-2">
                <div class="card-subtitle mb-2 text-muted" v-if="total > 0">
                    <label class="inline"><input type="checkbox" @click="selectAllData('table', $event.target)"  /> Select All</label>
                </div>
                <div class="panel-sub-heading" style="padding:4px" v-if="showSearch">
                    <div class="input-group">
                        <input type="text" placeholder="Search"  class="form-control" aria-describedby="basic-addon3" v-model="searchKey">
                    </div>
                </div>
                <ul class="list-group">
                    <li v-for="(data, index) in filterData()" class="list-group-item"><label class="normal">
                        <input type="checkbox"  v-model="data.selected" />  {{getLabel(data)}} </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col col-4">
        <div class="card border-0">
            <div class="card-body p-0" v-if="message.length > 0">
                <small :class="alertCss" v-html="message" style="display:block"></small>
            </div>
            <div class="card-header">
                <h5 class="card-title text-success m-0" style="line-height: inherit">
                    Added {{title}} <span class="badge text-bg-secondary">{{selectedTotal}}</span> <button title="Remove Selected" @click="doAction('remove')" class="btn btn-sm btn-danger pull-right">Remove Selected</button>
                </h5>
            </div>
            <div class="card-body">
                <div v-if="selectedTotal > 0" class="card-subtitle mb-2 text-muted"><label class="inline"><input type="checkbox" @click="selectAllData('sitewise', $event.target)" /> Select All</label></div>
                <ul class="list-group">
                    <li v-for="(data, index) in siteData" class="list-group-item"><label class="normal"> <input type="checkbox" v-model="data.selected" /> {{getLabelForSite(data)}} </label></li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>

import {Loader, Toast} from '../helpers/common';


    export default {

        mounted() {
           //console.log(this.siteData);
          //  console.log(this.allData.data[0]);
            //console.log(this.cuurent);
          // this.initData();

        },
        created() {
          //this.initData();
        },

        props: [
            'dataMessage',
            'dataAllData',
            'dataSiteData',
            'dataCurrentKey',
            'dataSiteId',
            'dataDefaultActionForSave',
            'dataAlertCss'
        ],
        data() {
            return {
                siteData:(typeof this.dataSiteData == "undefined" || this.dataSiteData === "") ? [] : JSON.parse(this.dataSiteData),
                allData:(typeof this.dataAllData == "undefined" || this.dataAllData === "") ? [] : JSON.parse(this.dataAllData),
                currentKey:this.dataCurrentKey,
                searchKey:'',
                siteId:parseInt(this.dataSiteId),
                message:(typeof this.dataMessage == "undefined") ? '' : this.dataMessage,
                defaultActionForSave:(typeof this.dataDefaultActionForSave === "undefined") ? "saveSettings" : this.dataDefaultActionForSave,
                alertCss:(typeof this.dataAlertCss == "undefined" || this.dataAlertCss === "") ? 'alert alert-info' : this.dataAlertCss,
            }
        },
        computed: {
            title() {
                return this.allData.label;
            },
            total() {
                return (this.allData.data) ? this.allData.data.length : 0;
            },
            selectedTotal() {
                return (this.siteData == null) ? 0 : this.siteData.length;
            },
            showSearch() {
                //return true;
                return (this.allData.data && this.allData.data.length > 10);
            }
        },
        methods: {
            saveNow(url, data) {
                Loader.show(this, "Please wait. Making Changes...");
                return new Promise((resolve, reject) => {
                    axios.post(url, data)
                        .then(response => {
                            resolve(response);
                        }).catch(error => {
                        reject(error.response);
                    }).finally(()=> {
                        Loader.hide(this);
                    });
                });

            },
            doAction(action="add") {
                let $this = this;
                //if action is add use left data to copy/reference to the right side
                let currentData = (action === "add") ? this.allData.data : this.siteData;
                let ids = [];
                let selectedArr = [];

                if(currentData && currentData.length >0) {
                    currentData.forEach(function (current) {
                        if(current.selected === true) {
                            ids.push(current.id);
                            selectedArr.push(current);
                        }
                    });

                    //if action is method/function. means it is being called from another component
                    if(typeof this.defaultActionForSave === "function")  {
                        this.defaultActionForSave(action, currentData, selectedArr, ids);
                        return false;
                    }

                    if(ids.length > 0) {
                        let postParams = {};
                        postParams.key = this.currentKey;
                        postParams.ids = ids;
                        postParams.site_id = this.siteId;
                        postParams.action = action;
                        this.saveNow(AdminConfig.admin_path("site/"+this.defaultActionForSave), postParams).then(function (res) {

                            feedback(res);

                        }).catch(function(res) {
                          console.error("error: ", res);
                          Toast.show($this, res?.data?.message || "I don't know what went wrong.", 5000);
                        });
                    }
                }

                function hasInArr(element, onwhat, value) {
                    return element[onwhat] === value;
                }
                //console.log(ids);
                function feedback(res) {
                    console.log(res);
                    if(action === "add") {
                        //console.log("adding");
                        //console.log(selectedArr);
                        selectedArr.forEach(function(current) {
                            let id = current.id;

                            let found = $this.siteData.findIndex(function (c) {
                                return c.id === id;
                            });

                            if(found === -1) {
                                $this.siteData.push(current);
                            }

                        });

                    } else {
                        //remove from right side
                        selectedArr.forEach(function(current) {
                            let id = current.id;

                            let found = $this.siteData.findIndex(function (c) {
                                return c.id === id;
                            });

                            if(found !== -1) {
                                $this.siteData.splice(found, 1);
                            }

                        });
                    }

                }
            },
            selectAllData(dataType, holder) {

                let $this = this;
                let currentData = (dataType === "table") ? this.allData.data : this.siteData;
                let selected = holder.checked;
                if(currentData && currentData.length > 0) {
                    currentData.forEach(function (current) {
                        //$this.$set(current, "selected", selected);
                        current.selected = selected;
                    });
                }


            },
            getLabel(data) {
                let label = "";
                if(data.lang) {
                    label = (typeof data.lang.name == "undefined") ? "" : data.lang.name;
                } else {
                    label = data.name || data.alias || "";
                }
                return label;

            },
            getLabelForSite(data) {

                //console.log(data);

                if(typeof data.name != "undefined" || typeof data.alias != "undefined") {

                    return data.name || data.alias;

                } else if(data.lang) {

                    return (typeof data.lang.name == "undefined") ? "" : data.lang.name;

                }

                //Lang data can be found from the source too. Borrow it from left side if right side does not have it.
                //fallback - get from source/left side data  compare id or link_rewrite

                if(this.allData.data && this.allData.data.length > 0) {

                        let index = this.allData.data.findIndex(function(current) {

                            return (current.id === data.id) || (typeof data.link_rewrite != "undefined" && data.link_rewrite === current.link_rewrite);
                        });

                        return (index === -1) ? "" : this.getLabel(this.allData.data[index]);

                }

                return "";

            },
            filterData() {
                let $this = this;

                let data = [];

                let key = this.searchKey;

                if(key !== "" && key != null) {

                    key = key.toLowerCase();

                    return this.allData.data.filter(function(current) {

                        return ($this.getLabel(current).toLowerCase().includes(key) || current.id === key) ;

                    });


                } else {
                   //console.log("data ", this.allData.data);
                    return this.allData.data;
                }
            },
            setData(key, data) {
                //this.allData[key] = data;
                //console.log(key, data);
                this.allData[key] = data;
                //this.$set(this.allData, key, data);
            },
            setSiteData(data) {
                //this.allData[key] = data;
                this.siteData = {};
                this.siteData = data;
                //this.$set(this, 'siteData', data);
            }
        }
    }

</script>
