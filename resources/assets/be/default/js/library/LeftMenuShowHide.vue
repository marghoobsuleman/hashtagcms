<template>
    <div class="btn-group">
        <span @click="toggleMenu" :class="currentCss"></span>
    </div>
</template>

<script>
    import {LeftMenu} from "../helpers/Common";
    import {EventBus} from "../helpers/event-bus";

    export default {

        mounted() {
            LeftMenu.init();
            this.init();
        },
        props: [
            'dataIconCss',
            'dataIconCssOff'
        ],
        data() {
          return {
              visible:true,
              currentCss:'',
              css:(typeof this.dataIconCss == "undefined" || this.dataIconCss == "") ? "fa fa-arrow-left hand" : this.dataIconCss,
              cssOff:(typeof this.dataIconCssOff == "undefined" || this.dataIconCssOff == "") ? "fa fa-arrow-right hand" : this.dataIconCssOff
          }
        },
        methods: {
            init() {
                this.currentCss = (LeftMenu.isVisible()) ? this.css : this.cssOff;
            },
            manageCss() {
                var $this = this;

                EventBus.$on('left-menu-on-show', function () {
                    $this.currentCss = $this.css;
                });
                EventBus.$on('left-menu-on-hide', function () {
                    $this.currentCss = $this.cssOff;
                })
            },
            toggleMenu() {
                this.visible = !this.visible;
                LeftMenu.toggleShow();

                //console.log("LeftMenu ", LeftMenu.isVisible());

                this.manageCss();
            }
        }

    }
</script>
