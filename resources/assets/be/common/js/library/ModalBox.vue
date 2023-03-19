<template>
    <div :class="modelCss" tabindex="-1" aria-hidden="true" :style="style">
        <div :class="'modal-dialog modal-dialog-centered modal-dialog-scrollable '+dataModalCss" :style="modalWidth">
            <div class="modal-content">
                <div :class="titleCss + ' modal-header'">
                    <h5 class="modal-title" style="width: 100%">
                        <slot name="title">{{title}}</slot>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="sendData(0)"></button>
                </div>
                <div :class="contentCss + ' modal-body'">
                    <slot name="content">{{content}}</slot>
                </div>
                <div :class="footerCss + ' modal-footer'" v-show="showFooter">
                    <slot name="footer">{{footerContent}}</slot>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" :style="backDropStyle"></div>
</template>

<script>

    export default {
        emits: ["onClose"],
        props: [
            'dataShowModal',
            'dataShowFooter',
            'dataTitleCss',
            'dataContentCss',
            'dataFooterCss',
            'dataModalCss',
            'dataWidth'
        ],
        mounted() {
            if(this.visible) {
                this.open();
            }
        },
        data() {
            return {
                modelCss:'modal modal-lg fade show',
                titleCss:(typeof this.dataTitleCss === "undefined") ? '' : this.dataTitleCss,
                contentCss:(typeof this.dataContentCss === "undefined") ? '' : this.dataContentCss,
                footerCss:(typeof this.dataFooterCss === "undefined") ? '' : this.dataFooterCss,
                visible:(this.dataShowModal !== undefined && this.dataShowModal.toString() === 'true'),
                style:'',
                toBeReturned:{},
                title:"Alert",
                content:"Modal Content",
                footerContent:"",
                backDropStyle:'display:none'
            }
        },
        computed: {
          showFooter() {
            return ((typeof this.dataShowFooter !== "undefined" && this.dataShowFooter.toString() === "true") || this.footerContent !== "")
          },
          modalWidth() {
            if(typeof this.dataWidth != "undefined" && this.dataWidth !== "")  {
                return `width:${this.dataWidth}`;
            } else {
                return "";
            }
          }
        },
        methods:  {
            open(toBeReturned={}) {
                this.visible = true;
                this.backDropStyle = this.style = 'display:block';
                this.modelCss = 'modal modal-lg fade show animated bounceInDown';
                this.toBeReturned = toBeReturned;
            },
            close() {
                this.visible = false;
                this.modelCss = 'modal modal-lg animated bounceOutUp';
                this.backDropStyle = 'display:none';

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
                    }
                    if(message.content) {
                        this.content = message.content;
                    }
                    if(message.footerContent) {
                        this.footerContent = message.footerContent;
                    }
                }

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
