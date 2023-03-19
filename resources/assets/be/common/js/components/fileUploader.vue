<template>
    <form ref="uploaderForm" name="uploaderForm" enctype="multipart/form-data">
    <div class="row" v-show="!isUploading">
        <div class="col-5">
            <input type="file" ref="allFilesC" name="msFiles__[]" multiple class="form-control" required @change="checkErrors" :accept="acceptFileType" />
            <div class="alert alert-danger mt-1" v-if="errors.files">
                Please select at least one file.
            </div>
        </div>
        <div class="col-5">
            <input type="text" name="msTags__" id="msTags__" required v-model="form.tags"
                   class="form-control" placeholder="Please enter keyword to search later. User comma for multiple keywords" @change="checkErrors" />
            <div class="alert alert-danger mt-1" v-if="errors.tags">Please enter keywords to search later.</div>
        </div>
        <div class="col-2">
            <input type="button" value="Upload" class="btn btn-primary" @click="uploadNow" />
        </div>
    </div>
    </form>
    <div class="row" v-if="isUploading">
        <div class="progress p-0">
            <div class="progress-bar progress-bar-striped bg-success" role="progressbar" :style="'width:'+percent+'%'" :aria-valuenow="percent" aria-valuemin="0" aria-valuemax="100">{{ percent }}%</div>
        </div>
    </div>
    <div class="row" v-if="message !== ''">
        <div class="col">
            <div :class="'alert m-1 '+msgType">{{ message }}</div>
        </div>

    </div>
</template>

<script>
import {EventBus} from "../helpers/event-bus";

export default {
    mounted() {

    },
    props: [
        'dataTitle',
        'dataAccept'
    ],
    data() {
        return {
            title: this.dataTitle,
            form: {
                tags: "",
                files:""
            },
            isUploading:false,
            errors: {
                files:false,
                tags:false
            },
            percent:0,
            message: "",
            msgType: "alert-success",
            acceptFileType: (typeof this.dataAccept !== 'undefined') ? this.dataAccept : "image/*"
        }
    },
    methods: {
        goBack() {
            window.location.href = this.backUrl;
        },
        checkErrors() {
            this.errors.files = false;
            this.errors.tags = false;
        },
        resetForm() {
            this.form.tags = "";
            this.form.files = "";
            this.errors.tags = false;
            this.errors.files = false;
            this.percent = 0;
            this.$refs.uploaderForm.reset();
        },
        showMsg(msg, isError) {
            this.message = msg;
            if (isError) {
                this.msgType = "alert-danger";
            } else {
                this.msgType = "alert-success";
                setTimeout(() => {
                    this.message = "";
                }, 2000);
            }
        },
        uploadNow() {

            let url = AdminConfig.admin_path('gallery/uploadFiles');
            let onProgress = (progress) => {
                this.percent = Math.round(progress.progress * 100);
            };
            let onSuccess = (response) => {
                EventBus.emit('gallery-image-uploaded', response.data);
                this.resetForm();
                this.showMsg(response.data.message, false);
            };
           let onError = (error) => {
                let dm = "There is some error while uploading. Please try again.";
               this.showMsg(error.response.statusText || dm, true);
                this.resetForm();
            };

            this.message = "";

            let files = this.$refs.allFilesC.files;

            //check required fields
            let isErrors = false;
            if (files.length === 0) {
                isErrors = this.errors.files = true;
            }
            if (this.form.tags === "") {
                isErrors = this.errors.tags = true;
            }
            if (isErrors) {
                return false;
            }

            this.isUploading = true;
            let formData = new FormData();
            // Append each file to the FormData object
            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }

            formData.append('tags', this.form.tags);
            formData.append('groupName', 'content');
            // keep only text from the accepted file types and accepted file type can be any input
            formData.append('mediaType', this.acceptFileType.replace(/[^a-zA-Z,]/g, ''));

            formData.append('_csrf', window.Laravel.csrfToken);

            axios.post(url, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: (progressEvent) => {
                    onProgress(progressEvent);
                }
            }).then((response) => {
               onSuccess(response);
            }).catch((error) => {
                onError(error);
            }).finally(() => {
                this.isUploading = false;
            })

        }
    }
}

</script>
