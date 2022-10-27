<template>

    <modal-box ref="infoModal" data-show-footer="true">
        <div slot="title" style="text-transform: capitalize">
            {{model}} Info
        </div>
        <div slot="content" class="row" style="max-height: 500px;overflow: scroll">
            <table class="table table-striped">
                <tbody>
                    <tr v-for="(res, key) in resData">
                        <td>
                            {{key}}
                        </td>
                        <td>
                            <span v-if="key=='id'" v-html="getValue(key, res)"></span>
                            <span v-if="key!='id'" v-text="getValue(key, res)"></span>
                        </td>
                    </tr>
                    <tr v-if="errorMessage != ''">
                        <td style="background-color: #fff; border:none">
                            <div class="text text-danger">
                                {{errorMessage}}
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div slot="footer">
            <button class="btn btn-default" @click="close()">Close</button>
        </div>
    </modal-box>

</template>

<script>
    import {Toast, Loader} from "../helpers/Common";
    export default {
        props:[
            'dataModel',
            'dataModuleRelations',
            'dataId'
        ],
        mounted() {
            //console.log('Component mounted...')
        },
        data() {
          return {
              model:(typeof this.dataModel === "undefined" || this.dataModel == "") ? null : this.dataModel,
              relation:(typeof this.dataModuleRelations === "undefined" || this.dataModuleRelations == "") ? '' : this.dataModuleRelations,
              id:(typeof this.dataId === "undefined" || this.dataId === "") ? 0 : parseInt(this.dataId),
              resData:[],
              errorMessage:''
          }
        },
        methods: {
            getValue(key, value) {
                if(key==="id") {
                    let path = `edit/${value}`;
                    path = AdminConfig.admin_path(`${this.model}/${path}`);
                    return `<a href="${path}">${value}</a>`;
                }
                return value;
            },
            showInfo(model, id) {
              this.model = model;
              let $this = this;
              Loader.show(this);
              axios.get(AdminConfig.get("base_path")+`/ajax/getInfo/${model}/${id}`)
                  .then(function(res) {
                      $this.errorMessage = '';
                      $this.resData = {};
                      $this.resData = res.data.results;
                      if(res.data.error === true) {
                          $this.errorMessage = res.data.message;
                      }
                      $this.open();
                      Loader.hide($this);
                  }).catch(function(res) {

                        Toast.show($this, res.message);
                        Loader.hide($this);

                  })
            },
            open() {
              this.$refs.infoModal.open();
            },
            close() {
                this.$refs.infoModal.close();
            }
        }
    }
</script>
