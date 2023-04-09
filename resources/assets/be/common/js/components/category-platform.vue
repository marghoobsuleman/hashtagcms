<template>
    <div>
        <modal-box ref="platformbox" @onClose="hideSpinner">
            <template #title>
                Select Platform
            </template>
            <template #content>
                <div class="row">
                    <div class="col-auto">
                        <select v-model="currentPlatform" class="form-select select-medium" @change="editNow()">
                            <option value="">Select a platform</option>
                            <option v-for="platform in platforms" :value="platform.id">{{ platform.name }}</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success btn-from-submit" type="button" @click="editNow()">Edit Now</button>
                    </div>
                </div>
            </template>
        </modal-box>
    </div>
</template>

<script>
import {EventBus} from "../helpers/event-bus";
import {Toast} from "../helpers/common";
import ModalBox from "../library/modal-box.vue";

export default {
    components: {
        'modal-box': ModalBox
    },
    mounted() {
        this.verifyAction();
    },
    props: [
        'dataPlatforms'
    ],
    data() {
        return {
            modelCss: 'modal',
            showHide: true,
            platforms: ((this.dataPlatforms && this.dataPlatforms != "null") ? JSON.parse(this.dataPlatforms) : []),
            currentPlatform: "",
            currentTarget: null

        }

    },
    methods: {
        verifyAction: function () {
            let $this = this;
            EventBus.on('list-view-pre-edit', function (current) {
                $this.currentTarget = current;
                $this.open();
            });
        },
        editNow() {
            let platform_id = this.currentPlatform;
            if (platform_id === "") {
                Toast.show(this, "Please select a platform");
                return false;
            }
            let target = this.currentTarget;
            let id = target.getAttribute("data-rowid");
            window.location = AdminConfig.admin_path(`category/edit/${id}/${platform_id}`)
        },
        open() {
            // console.log(this.platforms);
            if (this.platforms.length > 1) {
                this.$refs.platformbox.open();
            } else {
                this.currentPlatform = this.platforms[0].id;
                this.editNow();
            }


        },
        close() {
            this.$refs.platformbox.close();
        },
        hideSpinner() {
            EventBus.emit('list-view-hide-spinner', this);
        }
    }
}

</script>
