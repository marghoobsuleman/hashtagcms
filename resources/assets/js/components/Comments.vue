<template>
    <div>
        <form class="comment-form relative" :action="saveURL" method="post" v-on:submit.prevent="saveNow" @keydown="clearMessage">
            <div class="row mb-3">
                <div class="col-sm-2">
                    <label for="_htcms_form_comment_name_">Name</label>
                </div>
                <div class="col-sm-10">
                    <input id="_htcms_form_comment_name_" type="text" class="form-control" required placeholder="Please enter your name" v-model="form.name" />
                </div>
                <div class="text text-danger">{{this.errors.name}}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-2">
                    <label for="_htcms_form_comment_email_">Email</label>
                </div>
                <div class="col-sm-10">
                    <input id="_htcms_form_comment_email_" type="email" class="form-control" required placeholder="Please enter your email  " v-model="form.email" />
                </div>
                <div class="text text-danger">{{this.errors.email}}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-2">
                    <label for="_htcms_form_comment_comment_">Comment</label>
                </div>
                <div class="col-sm-10">
                    <textarea id="_htcms_form_comment_comment_" v-model="form.comment" class="form-control" rows="7"></textarea>
                    <div class="text text-danger">{{this.errors.comment}}</div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-2">

                </div>
                <div class="col-sm-10 text-center">
                    <button class="btn btn-lg btn-info mb-1" type="submit">{{sendLabel}}</button>
                    <alert-message class="mt-1" ref="commentAlertMessage" data-type='successs'></alert-message>
                    <div class="tex text-danger">
                        {{errorMessage}}
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>

    import Form from "../helpers/Form";
    import AlertMessage from "./AlertMessage";
    import {EventBus} from "../helpers/event-bus";

    export default {
        components: {
            'alert-message':AlertMessage
        },
        mounted() {

        },
        props: [
            'dataCommentSaveUrl',
            'dataName',
            'dataEmail',
            'dataUserId',
            'dataCategoryId',
            'dataContentId'
        ],
        computed: {
          hasComments() {
              return (this.comments.length > 0 || this.commentsCount > 0) ;
          },
           styleImageSize() {
              return `width:${this.imageSize}px;height:${this.imageSize}px`;
           },
            sendLabel() {
              return (this.isLoading == false) ? "Submit" : "Please wait...";
            }
        },
        data() {
            return {
                form: new Form({
                    name:(typeof this.dataName == "undefined") ? "" : this.dataName,
                    email:(typeof this.dataEmail == "undefined") ? "" : this.dataEmail,
                    category_id:(typeof this.dataCategoryId == "undefined") ? 0 : this.dataCategoryId,
                    page_id:(typeof this.dataContentId == "undefined") ? null : this.dataContentId,
                    user_id:(typeof this.dataUserId == "undefined") ? null : this.dataUserId,
                    comment:''
                }),
                errors:{},
                saveURL:(typeof this.dataCommentSaveUrl == "undefined") ? "/comment/saveComment" : this.dataCommentSaveUrl,
                isLoading:false,
                errorMessage:''
            }
        },
        methods: {
            clearMessage() {
                this.errorMessage = '';
                this.$refs.commentAlertMessage.setMessage("");
            },
            showSuccess(response) {
                this.$refs.commentAlertMessage.setMessage(response.message);

                let data = response.data;
                data.created_at = new Date();

                //send to list of commnents
                EventBus.$emit('on-comment-post', data);
            },
            showLoader(status) {
                this.isLoading = status;
            },
            resetForm() {

                this.form.name = (typeof this.dataName == "undefined") ? "" : this.dataName;
                this.form.email = (typeof this.dataEmail == "undefined") ? "" : this.dataEmail;
                this.form.category_id = (typeof this.dataCategoryId == "undefined") ? 0 : this.dataCategoryId;
                this.form.page_id = (typeof this.dataContentId == "undefined") ? null : this.dataContentId;
                this.form.user_id = (typeof this.dataUserId == "undefined") ? null : this.dataUserId;

                this.showLoader(false);
            },
            showError(res) {
                this.showLoader(false);

                for(let i in res.errors) {
                    if(res.errors.hasOwnProperty(i)) {
                        this.$set(this.errors, i, res.errors[i][0]);
                    }
                }

                this.errorMessage = res.message;

            },
            hideErrorMessage(event) {

                let name = event.target.getAttribute("name");
                this.$set(this.errors, name, "")

                if(this.errorMessage != '') {
                    this.errorMessage = '';
                }
            },
            saveNow() {

                if(!this.isLoading) {
                    this.showLoader(true);

                    this.form.submit("post", this.saveURL)
                        .then(response => {

                            if(response.success===true) {
                                this.resetForm();
                                this.showSuccess(response);
                            } else {
                                this.errorMessage = response.message;
                            }

                            console.log(response);

                        })
                        .catch(response => {

                                this.showLoader(false);

                                if(response.success == false) {
                                    this.showError(response);
                                }

                            });
                }

            }
        }
    }

</script>