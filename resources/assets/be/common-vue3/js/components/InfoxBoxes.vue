<template>
    <template v-for="(module, index) in allModules">
        <div v-if="hasAccess(module.id)" class="col-auto hand" @click="navigate(module)" :title="module.name">
            <info-box
                    :key="module.name+index"
                    :data-title="module.name"
                    :data-sub-title="module.sub_title"
                    :data-icon-css="module.icon_css"
                    >
            </info-box>
        </div>
    </template>
</template>

<script>

    import InfoBox from '../library/InfoBox.vue';

    export default {
        components:{
            'info-box':InfoBox
        },
        mounted() {
            //console.log(this.modulesAllowed);
          //
        },
        props: [
            'dataModules',
            'dataModulesAllowed',
            'dataIsAdmin'
        ],
        data() {
            return {
                modulesAllowed:(this.dataModulesAllowed ? JSON.parse(this.dataModulesAllowed) : [])
            }
        },
        computed: {
            allModules() {
                let modules = (typeof this.dataModules == "undefined") ? [] : JSON.parse(this.dataModules);
                return modules.child;
            }
        },
        methods: {
            navigate(current) {

                window.location = AdminConfig.admin_path(current.controller_name);

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

        }
    }

</script>
