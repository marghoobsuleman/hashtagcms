<template>
  <div class="d-flex flex-column flex-shrink-0 t_left">
    <ul class="list-unstyled ps-0">
      <template v-for="current in allData">
        <li v-if="current.parent_id === 0">
          <a :href="getHref(current)"  class="text-white" aria-expanded="true">
            <i v-if="hasChild(current)" class="js_more fa fa-ellipsis-v pull-right adjustMore"></i>
            <i :class="current.icon_css" aria-hidden="true"></i>
            {{current.name}}
          </a>
          <template v-if="hasChild(current)">
            <ul class="btn-toggle-nav list-unstyled fw-normal small">
              <li v-for="child in current.child"><a :href="getHref(child)" class="text-white">
                <i :class="child.icon_css" aria-hidden="true"></i>
                {{ child.name }}
              </a></li>
            </ul>
          </template>
        </li>
      </template>
    </ul>
    <hr>
    <div class="dropdown">
      <a title="Open HashtagCMS.org" href="https://www.hashtagcms.org/" target="_blank" class="d-flex align-items-center text-light text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
        <strong>v1.3.8</strong>
      </a>
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

            if(this.dataControllerName === controller_name) {
                return true;
            }

            if(data && data.child && data.child.length>0) {
                let controllerName = this.dataControllerName;
                return data.child.find(function(c, ) {
                    return c.controller_name === controllerName;
                });
            }
        },
        getHref(data) {
            return AdminConfig.admin_path(data.controller_name);
        },
        hideAll() {
            let $this = this;
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
            if(this.dataIsAdmin.toString() === "1") {
                return true;
            }
            for(let i=0; i<this.modulesAllowed.length;i++) {
                let current = this.modulesAllowed[i];
                if(current.module_id === module_id) {
                    return true;
                }

            }
            return false;
        },
        getMinHeight() {
            return `height:${window.innerHeight}px;`;
        }
    }
}

</script>
