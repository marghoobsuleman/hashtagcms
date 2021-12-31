<template>
    <div class="toasterHolder" v-show="showMe">
        <div class="toasterContent">
            {{message}}
        </div>
    </div>
</template>
<style>
    .toasterHolder{min-width:200px;background:#333333; padding:10px; text-align:center; float:left;color:#fff; font-size:11px; letter-spacing:1px; -moz-border-radius:5px; border-radius:5px; z-index: 9999; position: absolute;}
    .toasterHolder .toasterContent{text-align:center; color:#fff;}
</style>
<script>
    export default {
        mounted() {

        },
        data() {
            return {
                showMe:false,
                message:"Toast Content",
                intervalId:0
            }
        },
        methods: {
            show(message="", timeout=3000, position) {
                this.hide();
                var $this = this;
                this.showMe = true;
                this.message = (message!="") ? message : "";
                this.$nextTick(function () {
                    this.align(position);
                });

                this.intervalId = setTimeout(function () {
                    $this.hide();
                }, timeout);
            },
            hide() {
                this.showMe = false;
                if(this.intervalId) {
                    clearInterval(this.intervalId);
                }
            },
            align(position) {

                let left = (position!=undefined) ? position.left : ((window.innerWidth/2) - (this.$el.offsetWidth/2));
                let top = (position!=undefined) ? position.top :  (((window.innerHeight/2) - (this.$el.offsetHeight/2)) +window.scrollY);
                this.$el.style.top = top+"px";
                this.$el.style.left = left+"px";
            }
        }

    }
</script>