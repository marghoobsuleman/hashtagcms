<template>
  <div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5">
            <slot name="header"></slot>
          </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <slot name="content"></slot>
        </div>
        <div class="modal-footer">
          <slot name="footer"></slot>
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
            }
        },
        data() {
            return {
                modelCss:'modal',
                titleCss:(typeof this.dataTitleCss === "undefined") ? '' : this.dataTitleCss,
                contentCss:(typeof this.dataContentCss === "undefined") ? '' : this.dataContentCss,
                footerCss:(typeof this.dataFooterCss === "undefined") ? '' : this.dataFooterCss,
                visible:(this.dataShowModal !== undefined && this.dataShowModal.toString() === 'true'),
                style:'',
                toBeReturned:{},
                title:"Alert",
                content:"Modal Content",
                footerContent:""
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
