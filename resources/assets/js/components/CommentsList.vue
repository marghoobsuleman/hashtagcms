<template>
    <div>
        <hr v-if="hasComments" />
        <fieldset :class="comments.length > 0 ? 'loaded' : ''">
            <legend>
                <span v-if="!hasComments" class="first-text" v-html="noCommentsAvailabeMessage"></span>
                <button type="button" class="btn btn-info" v-if="hasComments && !commentsLoaded" @click="fetchComments">
                    <span v-html="showCommentText"></span> <span class="badge badge-light">{{commentsCount}}</span>
                </button>
                <h3 v-if="hasComments && commentsLoaded">Comments <span class="badge badge-dark">{{commentsCount}}</span></h3>
            </legend>
            <div class="row mb-3 mt-3">
                <div class="col-lg-12 text-right mt-1">

                </div>
            </div>
            <div class="row" v-for="comment in comments">
                <div class="col-lg-2 mb-5 text-center image-column">
                    <div class="rounded-circle image" :style="styleImageSize">
                        <img :src="getAvatar(comment.email)" />
                    </div>
                    <span class="author">
                            {{comment.name}}
                        </span>
                    <span class="said">said on</span>
                    <span class="date"><span class="fa fa-calendar-o"></span> {{getFormattedDate(comment.created_at)}}</span>
                </div>
                <div class="col-lg-6">
                    <div style="" class="popover fade show bs-popover-right" role="tooltip" x-placement="right">
                        <div class="arrow" style="top: 16px;"></div>
                        <h3 class="popover-header"></h3>
                        <div class="popover-body">{{comment.comment}}
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</template>

<script>
    import {EventBus} from "../helpers/event-bus";
    import md5 from 'md5';
    import moment from 'moment';
    export default {
        mounted() {
            this.init();
        },
        props: [
            'dataCommentsCount',
            'dataComments',
            'dataAutoLoad',
            'dataCategoryId',
            'dataContentId',
            'dataImageSize',
            'dataDefaultImage',
            'dataNoCommentAvailableMessage',
            'dataDisplayOrder'

        ],
        computed: {
          hasComments() {
              return (this.comments.length > 0 || this.commentsCount > 0) ;
          },
           styleImageSize() {
              return `width:${this.imageSize}px;height:${this.imageSize}px`;
           },
            showCommentText() {
              return (this.isLoading == false) ? "Comments " : "Please wait... ";
            },
            commentsCount() {

                if(this.commentsLoaded == false) {
                    let commentsCount = (typeof this.dataCommentsCount == "undefined" || this.dataCommentsCount == "") ? 0 : parseInt(this.dataCommentsCount);

                    return this.comments.length + commentsCount;
                } else {
                    //comment count
                    return this.comments.length;
                }


            },
        },
        data() {
            return {
                comments:(typeof this.dataComments == "undefined" || this.dataComments == "") ? [] : JSON.parse(this.dataComments),
                imageSize:(typeof this.dataImageSize == "undefined" || typeof this.dataImageSize == "") ? 75 : this.dataImageSize,
                defaultImage:(typeof this.dataDefaultImage == "undefined") ? 75 : this.dataDefaultImage,
                noCommentsAvailabeMessage:(typeof this.dataNoCommentAvaiableMessage == "undefined") ? 'Be the first to write a comment.' : this.dataNoCommentAvaiableMessage,
                fetchURL:(typeof this.dataCommentFetchUrl == "undefined") ? "/comment/getComments" : this.dataCommentFetchUrl,
                isLoading:false,
                autoLoad:(typeof this.dataAutoLoad == "undefined" || this.dataAutoLoad == "") ? false : this.dataAutoLoad,
                category_id:(typeof this.dataCategoryId == "undefined") ? 0 : this.dataCategoryId,
                page_id:(typeof this.dataContentId == "undefined") ? null : this.dataContentId,
                commentsLoaded:false,
                displayOrder:(typeof this.dataDisplayOrder == "undefined" || this.dataDisplayOrder == "") ? 'desc' : this.dataDisplayOrder
            }
        },
        methods: {
            init() {

                let autoload = this.autoLoad.toString();
                if(autoload == "true" || autoload == "1") {
                    this.fetchComments();
                }
                EventBus.$on('on-comment-post', (data)=> {
                    this.addComments(data);
                });

            },
            getFormattedDate(dt) {
                let dte = new Date(dt);
                return moment(dt, "YYYYMMDD").fromNow();
            },
            showAllComments(data) {
                this.comments = data;
                this.commentsLoaded = true;
            },
            addComments(data) {
                if(this.displayOrder == 'desc') {
                    this.comments.unshift(data);
                } else {
                    this.comments.push(data);
                }


            },
            showLoader(status) {
                this.isLoading = status;
            },
            fetchComments() {
                this.showLoader(true);

                let url = this.fetchURL+`?category_id=${this.category_id}&page_id=${this.page_id}&order=${this.displayOrder}`;

                axios.get(url).then((response) => {
                        //console.log(response);
                        this.showAllComments(response.data);
                        this.showLoader(false);
                    })
                    .catch((response) => {
                        console.log(response);
                        this.showLoader(false);
                    });
            },
            getAvatar(email) {
                email = md5(email.toLowerCase());
                let size = this.imageSize;
                let defaultImg =  encodeURI(this.defaultImage);
                return `https://www.gravatar.com/avatar/${email}?d=${defaultImg}&s=${size}`;
            }
        }
    }

</script>