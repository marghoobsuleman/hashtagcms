<template>
    <div :class="modelCss" tabindex="-1" role="dialog" :style="style">
        <div class="modal-dialog" role="document" :style="modalWidth">
            <div class="modal-content">
                <div :class="titleCss + ' modal-header'">
                    <button @click="sendData(0)" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <slot name="title">{{title}}</slot>
                    </h4>
                </div>
                <div :class="contentCss + ' modal-body'">
                    <slot name="content">{{content}}</slot>
                </div>
                <div :class="footerCss + ' modal-footer'" v-show="showFooter">

                    <slot name="footer" v-html="footerContent">

                    </slot>

                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: [
            'dataShowModal',
            'dataShowFooter',
            'dataTitleCss',
            'dataContentCss',
            'dataFooterCss',
            'dataWidth'
        ],
        mounted() {
            if(this.visible) {
                this.open();
            };

        },
        data() {
            return {
                modelCss:'modal',
                titleCss:(typeof this.dataTitleCss === "undefined") ? '' : this.dataTitleCss,
                contentCss:(typeof this.dataContentCss === "undefined") ? '' : this.dataContentCss,
                footerCss:(typeof this.dataFooterCss === "undefined") ? '' : this.dataFooterCss,
                visible:(this.dataShowModal !== undefined && this.dataShowModal.toString() == 'true') ? true : false,
                style:'',
                toBeReturned:{},
                title:"Alert",
                content:"Modal Content",
                footerContent:""
            }
        },
        computed: {
          showFooter() {
            return ((typeof this.dataShowFooter !== "undefined" && this.dataShowFooter.toString() === "true") || this.footerContent != "") ? true : false
          },
          modalWidth() {
            if(typeof this.dataWidth != "undefined" && this.dataWidth != "")  {
                return `width:${this.dataWidth}`;
            } else {
                return "";
            }
          }
        },
        methods:  {
            open(toBeReturned={}) {
                this.visible = true;
                this.style = 'display:block';
                this.modelCss = 'modal animated bounceInDown';
                this.toBeReturned = toBeReturned;
            },
            close() {
                this.visible = false;
                this.modelCss = 'modal animated bounceOutUp';

                if(typeof this.toBeReturned === "function") {
                    this.toBeReturned();
                }
            },
            sendData(isOkay) {
                this.close();
                this.$emit("onClose", isOkay, this.toBeReturned);
            },

            show(message, position, callback, timeout) {

                if(typeof message == "string") {
                    this.content = message;
                } else {

                    if(message.title) {
                        this.title = message.title;
                    };
                    if(message.content) {
                        this.content = message.content;
                    };
                    if(message.footerContent) {
                        this.footerContent = message.footerContent;
                    };
                };

                //handle position
                if(position) {
                    //align this
                }

                this.open(callback);

            },
            hide() {
                this.close();
            }
        }
    }

</script>
