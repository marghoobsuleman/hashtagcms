<template>
<div>
    <div id="leftMenuPanel" class="leftMenu" @click="showHide($event)" :style="getMinHeight()">
        <ul>
            <li v-for="current in allData" :class="'parent '+getActiveCss(current.controller_name, current)" v-if="current.parent_id==0 && hasAccess(current.id)">
                <h3>
                    <a :href="getHref(current)">
                        <i v-if="hasChild(current)" class="js_more fa fa-ellipsis-v pull-right adjustMore"></i>
                        <i :class="current.icon_css" aria-hidden="true"></i>
                        <span class="text">{{current.name}}</span>
                    </a>
                </h3>
                <ul v-if="hasChild(current)">
                    <li v-for="child in current.child" v-if="hasAccess(child.id)" :class="getActiveCss(child.controller_name)">
                        <a :href="getHref(child)">
                            <i :class="getIconCss(child)" aria-hidden="true">{{getIconLabel(child)}}</i>
                            <span class="text">{{child.name}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
</template>

<script>
export default {
    mounted() {

    },
    props:[
        'dataList',
        'dataControllerName',
        'dataModulesAllowed',
        'dataIsAdmin'
    ],
    data() {
        return {
            allData:(this.dataList ? JSON.parse(this.dataList) : []),
            modulesAllowed:(this.dataModulesAllowed ? JSON.parse(this.dataModulesAllowed) : [])
        }
    },
    methods: {
        getIconLabel(data) {
            return (data.icon_css === '' || !data.icon_css) ? data.controller_name.charAt(0).toUpperCase() : '';
        },
        getIconCss(data) {
            return (data.icon_css === '' || !data.icon_css) ? 'badge badge-info text-small' : data.icon_css;
        },
        hasChild(data) {
            return data.child.length > 0;
        },
        getActiveCss(controller_name, data) {
            return (this.isActive(controller_name, data)) ? "active" : "";
        },
        isActive(controller_name, data) {

            if(this.dataControllerName == controller_name) {
                return true;
            }

            if(data && data.child && data.child.length>0) {
                var controllerName = this.dataControllerName;
                return data.child.find(function(c, ) {
                    return c.controller_name == controllerName;
                });
            }
        },
        getHref(data) {
            return AdminConfig.admin_path(data.controller_name);
        },
        hideAll() {
            var $this = this;
            document.querySelectorAll(".leftMenu .parent").forEach(function(i) {
                i.classList.remove("active", "animated", "fadeIn");
            });
        },
        showHide(event) {
            this.hideAll();
            let ele = event.target;

            if(ele.classList.contains("js_more")) {
                event.preventDefault();
                ele.parentElement.parentElement.parentElement.classList.add("active", "animated", "fadeIn");
            }
        },
        hasAccess(module_id) {
            if(this.dataIsAdmin.toString() == "1") {
                return true;
            }
            for(let i=0; i<this.modulesAllowed.length;i++) {
                let current = this.modulesAllowed[i];
                if(current.module_id == module_id) {
                    return true;
                }

            }
            return false;
        },
        getMinHeight() {
            var h = window.innerHeight;
            return `height:${h}px;`;
        }
    }
}

</script>
