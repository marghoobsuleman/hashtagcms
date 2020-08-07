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
            <li data-is-parent="true" v-for="(item, index) in allData" class="item" :data-id="getId(item)" v-if="isParent(item)">
                {{getName(item)}}
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
        </ul>
    </div>
</template>

<script>

    import Sortable from 'sortablejs';
    import {Toast} from '../helpers/Common';

    import SplitButton from '../library/SplitButton';

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
            'dataControllerName',
            'dataControllerChildName',
            'dataGroups',
            'dataGroupName',
            'dataShowGroups'
        ],

        data() {

            return {
                allData: ((this.dataAllData && this.dataAllData != "null") ? JSON.parse(this.dataAllData) : []),
                groups: ((this.dataGroups && this.dataGroups != "") ? JSON.parse(this.dataGroups) : []),
                groupName: ((this.dataGroupName && this.dataGroupName != "") ? this.dataGroupName : ""),
                showGroups:(this.dataShowGroups && this.dataShowGroups == "true") ? true : false
            }
        },
        computed: {

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
                    if(this.groups[i] == this.groupName) {
                        index = i;
                        break;
                    }
                };
                return index;

            }
        },
        methods: {
            isParent(data) {
                if(data.parent_id == 0 || !data.parent_id) {
                    return true;
                }
                return false;
            },
            arrangeAgain(data) {
                let path = AdminConfig.admin_path(this.controllerName+"/sort/"+data.value);
                window.location.href = path;
            },
            hasChild(data) {
                return (data.child && data.child.length > 0) ? true : false;
            },
            getName(data) {

                return (data.name) ? data.name : (data.lang.name);

            },
            getId(data) {
                return (data.id) ? data.id : data.menu_manager_id;
            },
            enableSorting() {
                let $this = this;
                this.$nextTick(function () {
                    let list = document.querySelectorAll(".js_sortable");
                    list.forEach(function (current) {
                        Sortable.create(current, {
                            animation:200,
                            onUpdate: function (/**Event*/evt) {
                                //console.log("onUpdate ", evt.item);
                                let item = evt.item;
                                let isParent = (item.getAttribute("data-is-parent") == "true") ? true : false;
                                $this.updateIndex(isParent);
                            }
                        })
                    })

                });
            },
            submit(requestType, url, data, controllerName) {
                return new Promise((resolve, reject) => {
                    axios[requestType](url, data)
                        .then(response => {
                            this.onSuccess(response, controllerName);
                        }).catch(error => {
                        this.onFailure(error.response);
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

                controllerName = (isParent == true) ? this.controllerName : (this.controlerChildName !== "") ? this.controlerChildName : this.controllerName;

                let counter = 1;

                console.log("saveAll "+saveAll);

                items.forEach(function (current, index) {

                    if(saveAll == true) {
                        let id = current.getAttribute("data-id");
                        let position = counter;
                        datas.push({position:position, where:{id:parseInt(id)}});
                        counter = counter+1;
                    } else {

                        let isParentElement = current.getAttribute("data-is-parent");
                        let id = current.getAttribute("data-id");

                        if(isParent.toString() == isParentElement.toString()) {
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
                Toast.show(this, res.statusText);
                console.log(res);
            }
        }
    }
</script>
