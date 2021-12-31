<template>

        <split-button v-if="hasChild()"
                      :data-options="allModules"
                      @change="onChange"
                      :data-parser="parseData"
                      :data-selected="currentIndex"
        >
            {{selectedModule}}
        </split-button>

</template>

<script>
    import SplitButton from '../library/SplitButton';

    export default {

        components:{
            'split-button':SplitButton
        },
        props:[
            'dataModules',
            'dataCurrentModule'
        ],
        mounted() {

            //console.log(this.modules);
        },
        data() {
          return {
              modules:(typeof this.dataModules == "undefined") ? [] : (typeof this.dataModules == "string") ? JSON.parse(this.dataModules) : this.dataModules,
              selectedModule:(typeof this.dataCurrentModule == "undefined") ? "" : this.dataCurrentModule
          }
        },
        computed: {
          currentIndex() {
              if(this.allModules.length > 0) {
                  for (var i = 0; i < this.allModules.length; i++) {
                      var item = this.allModules[i]
                      if (item.controller_name.toLowerCase() == this.selectedModule.toLowerCase()) {
                          return i;
                      };
                  };
              };

              return 0;
          },
          allModules() {
              let options = this.modules.child;
              options.splice(0, 0, {name:this.modules.name, controller_name:this.modules.controller_name});
              return options;
          }
        },
        methods: {
            hasChild() {
                return (this.modules.parent_id == 0 && this.allModules.length>1);
            },
            parseData: function(row) {
                return {label:row.name, value:row.controller_name};
            },
            onChange(data) {
                let url = AdminConfig.admin_path(data.value);
                window.location = url;
            }
        }
    }

</script>