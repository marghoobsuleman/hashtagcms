<template>
    <div class="btn-toolbar justify-content-between border-bottom mb-2 pb-2" role="toolbar">
        <div class="btn-group" role="group">
            <small style="position: relative;top: 5px" v-if="showExpand"> <left-menu-toggle data-icon-css="fa fa-arrow-left hand" data-icon-css-off="fa fa-arrow-right hand"></left-menu-toggle></small>
            <h4 class="moduleTitle" v-html="title"></h4></div>
        <div class="input-group actionToolbar">
            <copy-paste :data-copy-paste-auto-init="dataCopyPasteAutoInit" :data-show-copy="dataShowCopy" :data-show-paste="dataShowPaste"></copy-paste>
            <button v-if="showBackButton" aria-label="Back" class="btn btn-outline-secondary" title="Back" type="button"
                    @click="goBack()">{{ backTitle }}
            </button>
        </div>
    </div>
</template>

<script>
import CopyPaste from "../library/copy-paste.vue";
import LeftMenuShowHide from "../library/left-menu-show-hide.vue";

export default {
    components: {
        'copy-paste': CopyPaste,
        'left-menu-toggle':LeftMenuShowHide
    },
    mounted() {

    },
    props: [
        'dataTitle',
        'dataBackUrl',
        'dataBackTitle',
        'dataShowCopy',
        'dataShowPaste',
        'dataCopyPasteAutoInit',
        'dataShowExpand'
    ],
    computed: {
        showBackButton() {
            return (typeof this.dataBackUrl !== "undefined");
        },
        showExpand() {
            return (typeof this.dataShowExpand !== "undefined");
        }
    },
    data() {
        return {
            title: this.dataTitle,
            backUrl: this.dataBackUrl,
            backTitle: (typeof this.dataBackTitle === "undefined") ? "Back" : this.dataBackTitle
        }
    },
    methods: {
        goBack() {
            window.location.href = this.backUrl;
        }
    }
}

</script>
