<template>

    <modal-box ref="infoModal" data-show-footer="true">
        <template #title>
            <div style="text-transform: capitalize">
                {{ model }} Info
            </div>
        </template>
        <template #content>
            <div class="row" style="max-height: 500px;overflow: scroll">
                <table class="table table-striped">
                    <tbody>
                        <template v-for="(res, key) in resData">
                        <tr v-if="!shouldExclude(key)">
                            <td>
                                {{ key }}
                            </td>
                            <td>
                                <span v-if="key==='id'" v-html="getValue(key, res)"></span>
                                <span v-if="key!=='id'" v-text="getValue(key, res)"></span>
                            </td>
                        </tr>
                        </template>
                    <tr v-if="errorMessage !== ''">
                        <td style="background-color: #fff; border:none">
                            <div class="text text-danger">
                                {{ errorMessage }}
                            </div>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </template>
        <template #footer>
            <button class="btn btn-outline-secondary" @click="close()">Close</button>
        </template>
    </modal-box>
</template>

<script>
import {Toast, Loader} from "../helpers/common";
import ModalBox from "../library/modal-box.vue";
import {EventBus} from "../helpers/event-bus";

export default {
    components: {
        'modal-box': ModalBox
    },
    props: [
        'dataModel',
        'dataModuleRelations',
        'dataId'
    ],
    mounted() {
        //console.log('Component mounted...')
    },
    data() {
        return {
            model: (typeof this.dataModel === "undefined" || this.dataModel === "") ? null : this.dataModel,
            relation: (typeof this.dataModuleRelations === "undefined" || this.dataModuleRelations === "") ? '' : this.dataModuleRelations,
            id: (typeof this.dataId === "undefined" || this.dataId === "") ? 0 : parseInt(this.dataId),
            resData: [],
            errorMessage: '',
            isEditable: true,
            excludeFields:[]
        }
    },
    methods: {
        shouldExclude(key) {
            return this.excludeFields.indexOf(key) >= 0;
        },
        getValue(key, value) {
            if (!this.isEditable) {
                return value;
            }
            if (key === "id" && this.isEditable) {
                let path = `edit/${value}`;
                path = AdminConfig.admin_path(`${this.model}/${path}`);
                return `<a href="${path}">${value}</a>`;
            }
            return value;
        },
        showInfo(model, id, excludeFields=[], isEditable = true) {
            this.isEditable = (isEditable.toString() === "true");
            this.excludeFields = excludeFields;
            this.model = model;
            let $this = this;
            Loader.show(this);
            axios.get(AdminConfig.get("base_path") + `/ajax/getInfo/${model}/${id}`)
                .then(function (res) {
                    $this.errorMessage = '';
                    $this.resData = {};
                    $this.resData = res.data.results;
                    if (res.data.error === true) {
                        $this.errorMessage = res.data.message;
                    }
                    $this.open();
                    Loader.hide($this);
                }).catch(function (res) {

                Toast.show($this, res.message);
                Loader.hide($this);

            })
        },
        open() {
            this.$refs.infoModal.open();
            EventBus.emit('info-popup-open');
        },
        close() {
            this.$refs.infoModal.close();
            EventBus.emit('info-popup-close');
        }
    }
}
</script>
