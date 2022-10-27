<template>
    <div v-show="visible">
        <div class="loaderModal" ref="loaderModal" v-show="asModal">

        </div>
        <div class="loaderPanel" ref="loaderSpinner">
            <div class="spinner">
                <svg viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                    <circle class="length" fill="none" stroke-width="8" stroke-linecap="round" cx="33" cy="33" r="28"></circle>
                </svg>
                <svg viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                    <circle fill="none" stroke-width="8" stroke-linecap="round" cx="33" cy="33" r="28"></circle>
                </svg>
                <svg viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                    <circle fill="none" stroke-width="8" stroke-linecap="round" cx="33" cy="33" r="28"></circle>
                </svg>
                <svg viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                    <circle fill="none" stroke-width="8" stroke-linecap="round" cx="33" cy="33" r="28"></circle>
                </svg>
            </div>

            <span class="loaderText" v-html="loadingText"></span>
        </div>
    </div>
</template>

<script>

    export default {
        props: [
            'dataLoadingText',
            'dataAsModal'
        ],
        mounted() {
            this.fixLayout();
        },
        data() {
            return {
                loadingText:(typeof this.dataLoadingText === "undefined") ? "Loading. Please wait..." : this.dataLoadingText,
                asModal:(typeof dataAsModal === "undefined" || dataAsModal === true) ? true : false,
                visible:false
            }
        },
        methods:  {

            fixLayout(position=null) {

                let heightWidth = getHeightWidth();

                let loaderModal = this.$refs.loaderModal;
                let loaderSpinner = this.$refs.loaderSpinner;


                //loader width/height
                loaderModal.style.width = heightWidth.width+"px";
                loaderModal.style.height = heightWidth.height+"px";


                if(position!==null) {
                    if(position.left) {
                        loaderSpinner.style.left = position.left+"px";
                    }

                    if(position.top) {
                        loaderSpinner.style.top = position.top+"px";
                    }

                    if(position.right) {
                        loaderSpinner.style.top = position.right+"px";
                    }

                }


                function getHeightWidth() {
                    var width = 0, height = 0;
                    if (typeof(window.innerWidth) == 'number') {
                        //Non-IE
                        width = window.innerWidth;
                        height = window.innerHeight;
                    } else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
                        //IE 6+ in 'standards compliant mode'
                        width = document.documentElement.clientWidth;
                        height = document.documentElement.clientHeight;
                    } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
                        //IE 4 compatible
                        width = document.body.clientWidth;
                        height = document.body.clientHeight;
                    }

                    return {width, height};
                }

                //console.log(width, height);
            },
            show(message=null, position=null) {

                this.visible = true;

                if(typeof message == "string") {
                    this.loadingText = message;
                }

                this.fixLayout(position);


            },
            hide() {
                this.visible = false;
            }
        }
    }

</script>