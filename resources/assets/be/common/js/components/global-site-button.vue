<template>
    <select @click="init(true)" name="site_combo" class="form-select" id="site_combo" @change="setSite"
            v-model="currentSite">
        <option v-for="site in sites" :value="site.id">
            {{ site.name }}
        </option>
    </select>
</template>

<script>
import SplitButton from '../library/split-button.vue';

import SecureLS from 'secure-ls';

export default {
    components: {
        'split-button': SplitButton
    },
    props: [
        'dataSites',
        'dataCurrentSite',
        'dataSupportedSites',
        'dataIsAdmin'
    ],
    mounted() {
        this.init();
    },
    data() {
        return {
            sites: (typeof this.dataSites == "undefined") ? [] : JSON.parse(this.dataSites),
            currentSite: (typeof this.dataCurrentSite == "undefined") ? 1 : parseInt(this.dataCurrentSite),
            supportedSite: (typeof this.dataSupportedSites == "undefined") ? [] : JSON.parse(this.dataSupportedSites)
        }
    },
    methods: {
        init(force = false) {
            let $this = this;
            //$this.sites = [{name:"Please wait...", id:0}];
            let ls = new SecureLS();
            let allSites = ls.get("allSites");

            if ((allSites.length === 0) || force === true) {
                let siteController = AdminConfig.admin_path(`site/getSitesForUsers`);
                axios.get(siteController, {withCredentials: false})
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
            //has language greater than one
            return (this.sites != null && this.sites.length > 1);
        },
        parseSite: function (row) {
            // console.log("parseSite");
            //console.log(row);
            return {label: row.name, value: row.id};
        },
        setSite(data) {
            let ajaxController = AdminConfig.admin_path(`ajax/setSiteId/${this.currentSite}`);
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
