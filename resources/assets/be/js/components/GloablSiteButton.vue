<template>
<div>
    <select name="site_combo" id="site_combo" @change="setSite" v-model="currentSite">
        <option v-for="site in sites" :value="site.id">
            {{site.name}}
        </option>
    </select>


</div>

</template>

<script>
    import SplitButton from '../library/SplitButton';

    import SecureLS from 'secure-ls';

    export default {
        components:{
            'split-button':SplitButton
        },
        props:[
            'dataSites',
            'dataCurrentSite'
        ],
        mounted() {
            this.init();
        },
        data() {
          return {
              sites:(typeof this.dataSites == "undefined") ? [] : JSON.parse(this.dataSites),
              currentSite:(typeof this.dataCurrentSite == "undefined") ? 1 : parseInt(this.dataCurrentSite)
          }
        },
        methods: {
            init() {
                let ls = new SecureLS();
                let $this = this;
                let allSites = ls.get("allSites");
                if(allSites === null || allSites.length === 0) {
                    let siteController = AdminConfig.admin_path(`site/getAllSite`);
                    axios.get(siteController)
                        .then(function (response) {

                            $this.sites = response.data;
                            ls.set("allSites", JSON.stringify($this.sites));
                            //  $this.$refs.site_combo.setData($this.sites);

                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                } else {
                    $this.sites = JSON.parse(allSites);
                }

            },
            hasSites() {
                //has language greate than one
                return (this.sites != null && this.sites.length>1);
            },
            parseSite: function(row) {
               // console.log("parseSite");
                //console.log(row);
                return {label:row.name, value:row.id};
            },
            setSite(data) {

                var ajaxController = AdminConfig.admin_path(`ajax/setSiteId/${this.currentSite}`);
                axios.get(ajaxController)
                    .then(function (response) {
                        //console.log(response);
                       //window.location.reload();
                        window.location.href = window.location.pathname;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            }
        }
    }

</script>