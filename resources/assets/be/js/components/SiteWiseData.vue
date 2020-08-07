<template>
    <div>
        <div class="col-md-12">
            <div class="col-md-5">
                <div class="panel panel-info margin-top-20">
                    <div class="panel-heading"><label class="normal">
                        <input type="checkbox" @click="selectAllData('table', $event.target)"  />
                        Choose {{title}} for the site - <span class="badge">{{total}}</span></label>
                        <button title="Add Selected" @click="doAction('add')" class="hand pull-right btn btn-sm btn-default">Add Selected</button>

                    </div>
                    <div class="panel-sub-heading" style="padding:4px" v-if="showSearch">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon3">Search</span>
                            <input type="text"  class="form-control" aria-describedby="basic-addon3" v-model="searchKey">
                        </div>
                    </div>
                    <ul class="list-group">

                        <li v-for="(data, index) in filterData()" class="list-group-item"><label class="normal">
                            <input type="checkbox"  v-model="data.selected" />  {{getLabel(data)}} </label>
                        </li>

                    </ul>
                </div>
            </div>

            <div class="col-md-4">
                <small :class="alertCss" v-if="message.length > 0" v-html="message" style="display:block">

                </small>
                <div class="panel panel-success margin-top-20">
                    <div class="panel-heading">
                        <label class="normal">
                            <input type="checkbox" @click="selectAllData('sitewise', $event.target)" />
                        {{title}} <span class="badge">{{selectedTotal}}</span>
                        </label>
                        <button title="Remove Selected" @click="doAction('remove')" class="btn btn-sm btn-danger pull-right">Remove Selected</button>
                    </div>
                    <ul class="list-group">

                        <li v-for="(data, index) in siteData" class="list-group-item"><label class="normal"> <input type="checkbox" v-model="data.selected" /> {{getLabelForSite(data)}} </label></li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import {Toast} from '../helpers/Common';

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
                siteData:(typeof this.dataSiteData == "undefined" || this.dataSiteData == "") ? [] : JSON.parse(this.dataSiteData),
                allData:(typeof this.dataAllData == "undefined" || this.dataAllData == "") ? [] : JSON.parse(this.dataAllData),
                currentKey:this.dataCurrentKey,
                searchKey:'',
                siteId:parseInt(this.dataSiteId),
                message:(typeof this.dataMessage == "undefined") ? '' : this.dataMessage,
                defaultActionForSave:(typeof this.dataDefaultActionForSave === "undefined") ? "saveSettings" : this.dataDefaultActionForSave,
                alertCss:(typeof this.dataAlertCss == "undefined" || this.dataAlertCss == "") ? 'alert alert-info' : this.dataAlertCss,
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
                return (this.allData.data && this.allData.data.length > 10) ? true : false;
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
            doAction(action="add") {
                var $this = this;
                //if action is add use left data to copy/reference to the right side
                var currentData = (action == "add") ? this.allData.data : this.siteData;
                var ids = [];
                var selectedArr = [];

                if(currentData && currentData.length >0) {
                    currentData.forEach(function (current) {
                        if(current.selected == true) {
                            ids.push(current.id);
                            selectedArr.push(current);
                        }
                    });

                    //if action is method/function. means it is being called from another component
                    if(typeof this.defaultActionForSave === "function")  {
                        this.defaultActionForSave(action, currentData, selectedArr, ids);
                        return false;
                    };

                    if(ids.length > 0) {
                        let postParams = {};
                        postParams.key = this.currentKey;
                        postParams.ids = ids;
                        postParams.site_id = this.siteId;
                        postParams.action = action;
                        this.saveNow(AdminConfig.admin_path("site/"+this.defaultActionForSave), postParams).then(function (res) {

                            feedback(res);

                        }).catch(function() {

                        });
                    }
                }

                function hasInArr(element, onwhat, value) {
                    return element[onwhat] == value;
                }
                //console.log(ids);
                function feedback(res) {
                    console.log(res);
                    if(action == "add") {
                        //console.log("adding");
                        //console.log(selectedArr);
                        selectedArr.forEach(function(current) {
                            let id = current.id;

                            let found = $this.siteData.findIndex(function (c) {
                                return c.id == id;
                            });

                            if(found == -1) {
                                $this.siteData.push(current);
                            }

                        });

                    } else {
                        //remove from right side
                        selectedArr.forEach(function(current) {
                            let id = current.id;

                            let found = $this.siteData.findIndex(function (c) {
                                return c.id == id;
                            });

                            if(found !== -1) {
                                $this.siteData.splice(found, 1);
                            }

                        });
                    }

                }
            },
            selectAllData(dataType, holder) {

                var $this = this;
                var currentData = (dataType == "table") ? this.allData.data : this.siteData;
                var selected = holder.checked;
                if(currentData && currentData.length > 0) {
                    currentData.forEach(function (current) {
                        $this.$set(current, "selected", selected);
                    });
                }


            },
            getLabel(data) {
                var label = "";
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

                            return (current.id == data.id) || (typeof data.link_rewrite != "undefined" && data.link_rewrite == current.link_rewrite);
                        });

                        return (index == -1) ? "" : this.getLabel(this.allData.data[index]);

                }

                return "";

            },
            filterData() {
                var $this = this;

                var data = [];

                var key = this.searchKey;

                if(key != "" && key != null) {

                    key = key.toLowerCase();

                    var filterdData =  this.allData.data.filter(function(current) {

                        return ($this.getLabel(current).toLowerCase().includes(key) || current.id == key) ;

                    });

                    return filterdData;

                } else {
                   //console.log("data ", this.allData.data);
                    return this.allData.data;
                }
            },
            setData(key, data) {
                //this.allData[key] = data;
                this.$set(this.allData, key, data);
            },
            setSiteData(data) {
                //this.allData[key] = data;
                this.$set(this, 'siteData', data);
            }
        }
    }

</script>
