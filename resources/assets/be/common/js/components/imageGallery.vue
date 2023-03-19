<template>
    <modal-box ref="imageModalBox" data-show-footer="true">
        <template #title>
            <div class="row">
                <div class="col-2">
                    Gallery
                </div>
                <div class="col-auto d-flex">
                    <input type="text" class="form-control me-1" placeholder="Search image" @keydown.enter="searchImages" v-model="searchKey" />
                    <input type="button" class="btn btn-secondary me-1" value="Search" @click="searchImages"  />
                </div>
            </div>
        </template>
        <template #content>
            <div class="row">
                <span class="spinner-border text-info" role="status" v-if="loading">
                        <span class="visually-hidden">Loading...</span>
                    </span>
                <div class="col-auto m-1 border" v-for="current in data">
                        <img :src="getImage(current.path)" width="100" @click="insertContentToEditor(getImage(current.path))" />
                </div>
                <div v-if="data.length === 0 && loading === false">
                    No images found
                </div>
            </div>
        </template>
        <template #footer>
            <div class="container-fluid">
            <file-uploader data-accept="image/*"></file-uploader>
            </div>
        </template>
    </modal-box>
</template>

<script>
import ModalBox from "../library/modalBox.vue";
import {EventBus} from "../helpers/event-bus";
import fileUploader from "./fileUploader.vue";

export default {
    components: {
        'modal-box': ModalBox,
        'file-uploader': fileUploader
    },
    props: [
        'dataModel',
        'dataModuleRelations',
        'dataId',
        'dataEditor'
    ],
    mounted() {
        EventBus.on('gallery-image-uploaded', (response) => {
            this.data = [...response.data, ...this.data];
        });
    },
    computed: {
        galleryCreate() {
            return AdminConfig.admin_path('gallery/create?return_url=' + encodeURIComponent(window.location.href));
        }
    },
    data() {
        return {
            data:[],
            loading:false,
            searchKey:'',
            editor: this.dataEditor,
            form: {
                file:''
            }
        }
    },
    methods: {
        loadData() {
            this.data = [];
            this.loading = true;
            let url = AdminConfig.admin_path(`gallery/getAllImages`);
            axios.get(url).then(res => {
                this.data = res.data;
            }).catch(err => {
                console.log(err);
            }).finally(() => {
                this.loading = false;
            })

        },
        getImage(value) {
            return AdminConfig.get_media(`${value}`);
        },
        insertContentToEditor(content) {
            content = `<img src="${content}" />`;
            if (this.editor) {
                this.editor.insertContent(content);
            }
            this.close();
        },
        searchImages() {
          if (this.searchKey.length > 0) {
                this.data = [];
                this.loading = true;
                let url = AdminConfig.admin_path(`gallery/searchImages/${this.searchKey}`);
                axios.get(url).then(res => {
                    this.data = res.data;
                }).catch(err => {
                    console.log(err);
                }).finally(() => {
                    this.loading = false;
                })
            } else {
                this.loadData();
          }
        },
        open(editor) {
            this.loadData();
            if (editor) {
                this.editor = editor;
            }
            this.$refs.imageModalBox.open();
            EventBus.emit('image-gallery-open');
        },
        close() {
            this.$refs.imageModalBox.close();
            EventBus.emit('image-gallery-close');
        }
    }
}




</script>
