<template>
    <div class="sortElem" @click="showHide($event)">
        <ul id="sortableField">
            <li v-for="current in allData" class="parent" v-if="current.module_pid==0">
                <h3>
                    <a>
                        <i v-if="hasChild(current)" class="js_more fa fa-ellipsis-v pull-right adjustMore"></i>
                        <span class="text">{{current.name}}</span>
                    </a>
                </h3>
                <ul v-if="hasChild(current)">
                    <li v-for="child in current.child">
                        <a>
                            <span class="text">{{child.name}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</template>

<script>

    import Sortable from 'sortablejs';
    import {Toast} from '../helpers/Common';
    import Form from '../helpers/Form';


    export default {

        mounted() {
            this.enableSorting();
            console.log(this.controllerName);
        },

        props: [
            'dataAllModules',
            'dataControllerName'
        ],

        data() {

            return {
                allData: ((this.dataAllModules && this.dataAllModules != "null") ? JSON.parse(this.dataAllModules) : []),
                sortable: null,
                sortingInterval: -1,
                updateIndexUrl: AdminConfig.admin_path(this.controllerName + '/updateIndex'),
                lastTarget: ''
            }
        },
        computed: {
            controllerName() {
                let cName = (typeof this.dataControllerName == "undefined") ? "" : this.dataControllerName.toLocaleLowerCase();
                return cName.replace(/\s/g, "");
            }
        },
        methods: {
            hasChild(data) {
                return data.child.length > 0;
            },
            hideAll() {
                var $this = this;
                document.querySelectorAll(".sortElem .parent").forEach(function (i) {
                    i.classList.remove('active');
                });
            },
            showHide(event) {
                let ele = event.target;
                if (ele.parentElement.parentElement.parentElement != this.lastTarget) {
                    this.hideAll();
                    if (ele.classList.contains("js_more")) {
                        event.preventDefault();
                        ele.parentElement.parentElement.parentElement.classList.add("active", "animated", "fadeIn");
                    }
                    this.lastTarget = ele.parentElement.parentElement.parentElement;
                }
                else {
                    if (ele.parentElement.parentElement.parentElement.classList.contains('active')) {
                        ele.parentElement.parentElement.parentElement.classList.remove('active')
                    }
                    else {
                        ele.parentElement.parentElement.parentElement.classList.add('active');
                    }
                }
            },
            getActiveCss(controller_name, data) {
                return (this.isActive(controller_name, data)) ? "active" : "";
            },
            isActive(controller_name, data) {

                if (this.controllerName == controller_name) {
                    return true;
                }

                if (data && data.child && data.child.length > 0) {
                    var controllerName = this.controllerName;
                    return data.child.find(function (c,) {
                        return c.controller_name == controllerName;
                    });
                }
            },
            enableSorting() {
                this.$nextTick(function () {
                    if (this.sortable != null) {
                        this.sortable.destroy();
                    }
                    var el = document.getElementById('sortableField');
                    this.sortable = Sortable.create(el, {
                        onEnd: this.sortingCallback,
                        onStart: this.cancelSortingCallback
                    });
                });
            },
            cancelSortingCallback() {
                if (this.sortingInterval != -1) {
                    clearInterval(this.sortingInterval);
                }
            },
            sortingCallback(evt) {
                var text = "";
                var $this = this;
                var data = [];
                // console.log(evt);
                document.querySelectorAll(".sortElem .parent").forEach(function (i, index) {
                    var text = i.children[0].children[0].innerText;
                    var indx = index;
                    for (var a = 0; a < $this.allData.length; a++) {
                        if ($this.allData[a].module_pid == 0 && $this.allData[a].name == text) {
                            data[a] = {};
                            data[a].id = $this.allData[a].id;
                            data[a].position = index;
                            data[a].name = $this.allData[a].name;

                        }
                    }
                });
                // console.log(data);
                this.updateIndex(data);
                this.cancelSortingCallback();
            },
            submit(requestType, url, data) {
                return new Promise((resolve, reject) => {
                    axios[requestType](url, data)
                        .then(response => {
                            this.onSuccess(response);
                        }).catch(error => {
                        this.onFailure(error.response);
                    });
                });
            },
            updateIndex(data) {
                this.submit('post', this.updateIndexUrl, data)
                    .then(response => this.onSuccess(response))
                    .catch(response => this.onFailure(response))
            },
            onSuccess(res) {
                Toast.show(this, "Modules Sorted.");
            },
            onFailure(res) {
                console.log(res);
            }
        }
    }
</script>

<style>
    .sortElem {
        margin-top: 15px;
    }

    .sortElem .parent {
        list-style: none;
        border: 1px solid #cacaca;
        background: #fff;
    }

    .sortElem .parent h3 {
        font-size: 16px;
        margin: 0;
    }

    .sortElem ul {
        padding: 0;
    }

    .sortElem .parent a {
        padding: 10px;
        color: #666;
        display: block;
    }

    .sortElem .parent ul {
        display: none;
    }

    .sortElem .parent.active ul {
        display: block;
        background: #ffff;
        border-top: 1px solid #cacaca;
    }

    .sortElem .parent.active ul li {
        list-style: none;
        margin-left: 15px;
        line-height: normal;
    }

    .sortElem .parent.active ul li a span {
        color: #000;
        font-weight: bold;
    }

    .sortElem .parent.sortable-chosen {
        background: #f4f4f4;
    }

</style>
