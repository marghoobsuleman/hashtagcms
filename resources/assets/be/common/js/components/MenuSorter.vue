<template>
    <div>
        <h4 v-if="showGroups">Arrange Menus
            <split-button :data-options="groups"
                          @change="arrangeAgain"
                          :data-selected="selectedIndex"
            >
            </split-button>
        </h4>
        <ul class="sortable-list js_sortable">
            <template v-for="(item, index) in allData" >
                <li data-is-parent="true" v-if="isParent(item)" class="item" :data-id="getId(item)">
                    <span v-html="getName(item)"></span>
                    <div v-if="hasChild(item)" class="inline">
                        <label :for="'item_'+index" class="accordion-header pull-right">
                            <span class="fa fa-ellipsis-v ellipsis"></span>
                        </label>
                        <input :id="'item_'+index" type="checkbox" class="accordion-control">
                        <ul class="child accordion-body js_sortable">
                            <li data-is-parent="false" class="item" v-for="child in item.child" :data-id="getId(child)">
                                {{getName(child)}}
                            </li>
                        </ul>
                    </div>
                </li>
            </template>
        </ul>
        <div class="center-align mt-3" v-if="allData.length > 1">
            <input type="button" class="btn btn-success btn-from-submit" value="Save" @click="updateIndex()" />
        </div>
    </div>
</template>

<script>

    import Sortable from 'sortablejs';
    import {Toast, Loader} from '../helpers/common';

    import SplitButton from '../library/splitButton.vue';

    export default {

        components: {
          'split-button':SplitButton
        },

        mounted() {
            this.enableSorting();
            //console.log(this.controllerName);
            //console.log(this.allData);
        },

        props: [
            'dataAllData',
            'dataFields',
            'dataControllerName',
            'dataControllerChildName',
            'dataGroups',
            'dataGroupName',
            'dataShowGroups'
        ],

        data() {

            return {
                allData: ((this.dataAllData && this.dataAllData !== "null") ? JSON.parse(this.dataAllData) : []),
                groups: ((this.dataGroups && this.dataGroups !== "") ? JSON.parse(this.dataGroups) : []),
                groupName: ((this.dataGroupName && this.dataGroupName !== "") ? this.dataGroupName : ""),
                showGroups:!!(this.dataShowGroups && this.dataShowGroups === "true")
            }
        },
        computed: {
            fields() {
              let fields = ((this.dataFields && this.dataFields !== "null") ? JSON.parse(this.dataFields) : null);
              if(fields === null) {
                fields = {};
                fields.id = "id";
                fields.label = "name";
                fields.isImage = false;
              }
              return fields;
            },
            controllerName() {
                let cName = (typeof this.dataControllerName == "undefined") ? "" : this.dataControllerName.toLowerCase();
                return cName.replace(/\s/g, "");
            },
            controlerChildName() {
                let cName = (typeof this.dataControllerChildName == "undefined") ? "" : this.dataControllerChildName.toLowerCase();
                return cName.replace(/\s/g, "");

            },
            selectedIndex() {
                let index = 0;
                for(let i=0;i<this.groups.length;i++) {
                    if(this.groups[i] === this.groupName) {
                        index = i;
                        break;
                    }
                }
                return index;

            }
        },
        methods: {
            isParent(data) {
                return data.parent_id === 0 || !data.parent_id;

            },
            arrangeAgain(data) {
                window.location.href = AdminConfig.admin_path(this.controllerName+"/sort/"+data.value);
            },
            hasChild(data) {
                return (data.child && data.child.length > 0);
            },
            getName(data) {
                //Added media support
                if (this.fields.isImage === true) {
                    let path = (data[this.fields.label]) ? AdminConfig.get_media(data[this.fields.label]) : AdminConfig.get_media(data.lang[this.fields.label]);
                    return `<a href='${path}' target='_blank'><img height='30' src='${path}' /></a>`;
                } else {
                    return (data[this.fields.label]) ? data[this.fields.label] : (data.lang[this.fields.label]);
                }

            },
            getId(data) {
                return data[this.fields.id];
            },
            enableSorting() {
                let $this = this;
                this.$nextTick(function () {
                    let list = document.querySelectorAll(".js_sortable");
                    list.forEach(function (current) {
                        Sortable.create(current, {
                            animation:500,
                            ghostClass: "text-danger",
                            onUpdate: function (/**Event*/evt) {
                                //console.log("onUpdate ", evt.item);
                                //let item = evt.item;
                                //let isParent = (item.getAttribute("data-is-parent") === "true");
                                //$this.updateIndex(isParent);
                            }
                        })
                    })

                });
            },
            submit(requestType, url, data, controllerName) {
                Loader.show(this, "Please wait. Saving sorting data...");
                return new Promise((resolve, reject) => {
                    axios[requestType](url, data)
                        .then(response => {
                            this.onSuccess(response, controllerName);
                        }).catch(error => {
                        this.onFailure(error.response);
                    }).finally(()=> {
                        Loader.hide(this);
                    });
                });
            },
            updateIndex(isParent=false) {
                let items = document.querySelectorAll(".item");
                let datas = [];

                let controllerName = this.controllerName;

                let saveAll = true;



                if(this.controlerChildName !== "") {
                    saveAll = false;
                }

                controllerName = (isParent === true) ? this.controllerName : (this.controlerChildName !== "") ? this.controlerChildName : this.controllerName;

                let counter = 1;

                console.log("saveAll "+saveAll);

                items.forEach(function (current, index) {

                    if(saveAll === true) {
                        let id = current.getAttribute("data-id");
                        let position = counter;
                        datas.push({position:position, where:{id:parseInt(id)}});
                        counter = counter+1;
                    } else {

                        let isParentElement = current.getAttribute("data-is-parent");
                        let id = current.getAttribute("data-id");

                        if(isParent.toString() === isParentElement.toString()) {
                            let id = current.getAttribute("data-id");
                            let position = counter;
                            datas.push({position:position, where:{id:parseInt(id)}});
                            counter = counter+1;
                        }

                    }

                });

                let updateIndexUrl = AdminConfig.admin_path(controllerName+'/updateIndex');

                this.submit('post', updateIndexUrl, datas, controllerName);
            },
            onSuccess(res, controllerName) {
                Toast.show(this, (controllerName.toUpperCase())+" Sorted.");
            },
            onFailure(res) {
                Toast.show(this, res?.statusText || "There is some error! Don't know the reason", 5000);
                console.log(res);
            },
            setData(data) {
                this.allData = data;
            }
        }
    }
</script>
