<template>
  <div class="card mb-3 shadow info-box">
    <span :class="getColor()+' info-box-icon'"><i style="font-style: normal" :class="icon">{{getIconText()}}</i></span>
    <div class="card-body">
      <h5 class="card-title">
        <a v-if="link!==''" :href="link" class="info-box-text"><span class="info-box-text" v-html="content"></span></a>
        <span v-if="link===''" class="info-box-text">{{content}}</span>
      </h5>
      <p class="card-text text-center" v-if="subTitle!==''">
        {{subTitle}}
      </p>
    </div>
  </div>
</template>

<script>
    export default {
        mounted() {

        },
        props: [
            'dataInfo',
            'dataColorIndex',
            'dataSubTitle',
            'dataTitle',
            'dataIconCss',
            'dataLink'
        ],
        data() {
            return {
                info:(typeof this.dataInfo == "undefined") ? null : (typeof this.dataInfo == "string") ? JSON.parse(this.dataInfo) : this.dataInfo,
                subTitle:(typeof this.dataSubTitle !== "undefined") ? this.dataSubTitle : "",
                iconCss:this.dataIconCss,
                link:(typeof this.dataLink == "undefined") ? "" : this.dataLink
            }
        },
        computed: {
            content() {
              return (this.info !== null) ? (this.info.name || this.info.title) : this.dataTitle;
            },
            icon() {
                if((typeof this.iconCss == "undefined" || this.iconCss === "") && this.info !== null) {
                    return (this.info.icon_css || this.info.iconCss);
                } else {
                    return this.iconCss;
                }
            }

        },
        methods: {
            getIconText() {
                return (this.info?.icon_css || typeof this.iconCss === "undefined") ? (this.content.replace(/[^a-zA-Z- ]/g, "").match(/\b\w/g)).join("") : "";
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
                if(Store.fetch("counter")===undefined) {
                    Store.store("counter", 1);
                }

                let counter = Store.fetch("counter");

                if(counter>10) {
                    counter = 1;
                }
                Store.store("counter", (counter+1));

                return counter;
            }
        }
    }

</script>
