<template>
    <div class="info-box">
        <span :class="getColor()+' info-box-icon'"><i style="font-style: normal" :class="icon">{{getIconText()}}</i></span>
        <div class="info-box-content">
            <div v-if="link!=''">
                <a :href="link" class="info-box-text">
                    <span vclass="info-box-text" v-html="content"></span>
                </a>
            </div>
            <span v-if="link==''" class="info-box-text">{{content}}</span>

            <span v-if="total" class="info-box-number">{{total}}</span>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            //console.log("this.label "+this.label);
            //console.log(this.content);
        },
        props: [
            'dataInfo',
            'dataColorIndex',
            'dataTotal',
            'dataLabel',
            'dataIconCss',
            'dataLink'
        ],
        data() {
            return {
                info:(typeof this.dataInfo == "undefined") ? [] : (typeof this.dataInfo == "string") ? JSON.parse(this.dataInfo) : this.dataInfo,
                total:(typeof this.dataTotal == "undefined") ? 0 : (typeof this.dataTotal == "string") ? parseInt(this.dataTotal) : this.dataTotal,
                label:this.dataLabel,
                iconCss:this.dataIconCss,
                link:(typeof this.dataLink == "undefined") ? "" : this.dataLink
            }
        },
        computed: {
            content() {

                return (typeof this.label == "undefined") ? this.info.name : this.label;

                if((typeof this.label == "undefined" || this.label == "") && this.info.length != 0) {
                    return this.info.name;
                } else {
                    return this.label;
                }
            },
            icon() {
                if((typeof this.iconCss == "undefined" || this.iconCss == "") && this.info.length != 0) {
                    return this.info.icon_css;
                } else {
                    return this.iconCss;
                }
            }

        },
        methods: {
            getIconText() {
                return ((this.info.icon_css === "" || !this.info.icon_css) && typeof this.label == "undefined") ? (this.content.replace(/[^a-zA-Z- ]/g, "").match(/\b\w/g)).join("") : "";
            },
            getColor: function () {
                let n = (!this.dataColorIndex) ? this.getSerialNumber() : this.dataColorIndex;
                return "color-"+n;
            },
            getRandom(min=1, max=10) {
                //not in used
                min = parseInt(min);
                max = parseInt(max);
                return Math.floor(Math.random() * (max - min)) + min;
            },
            getSerialNumber() {
                if(Store.fetch("counter")==undefined) {
                    Store.store("counter", 1);
                };

                let counter = Store.fetch("counter");

                if(counter>10) {
                    counter = 1;
                };
                Store.store("counter", (counter+1));

                return counter;
            }
        }
    }

</script>
