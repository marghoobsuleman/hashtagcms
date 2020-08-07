<template>
    <div>

        <form :action="saveSeoSrpTuple" method="post" class="form-horizontal" role="form"
              v-on:submit.prevent="saveSeoSrpTuple">

            <div class="form-group">
                <div class="col-sm-2">
                    <label>Keyword</label>
                </div>
                <div class="col-sm-10">
                    <input v-model="results.keyword" type="text" class="form-control" name="tuple_keyword" required placeholder="tuple keyword : java or noida or java jobs in noida" />
                </div>
            </div>

            <div class="form-group" v-for="result in this.results.lang">

                <div class="col-sm-1">
                    <label>Title</label>
                </div>
                <div class="col-sm-11">
                    <vue-editor id="editor2" v-model="result.title" />
                    <!--<input v-model="result.title" type="text" class="form-control" name="tuple_title" required placeholder="tuple title" />-->
                </div>

            </div>

            <div class="form-group" v-for="result in this.results.lang">

                <div class="col-sm-1">
                    <label>Description</label>
                </div>
                <div class="col-sm-11">
                    <vue-editor id="editor1" v-model="result.description" />
                </div>

            </div>

            <div class="form-group">
                <div class="col-sm-2">
                    <label>Published ?</label>
                </div>
                <div class="col-sm-10">
                    <label title="Published"><input type="checkbox" v-model="results.publish_status" /></label>
                </div>
            </div>

            <div class="row">
                <div class="alert alert-danger" v-if="errorMessage!=''">{{errorMessage}}</div>
                <div class="form-group center-align">
                    <input type="submit" name="submit" value="Save" class="btn btn-success"/>
                    <a :href="dataBackUrl" class="btn btn-default">Cancel</a>
                </div>
            </div>

        </form>

    </div>
</template>

<script>
    import {Toast} from '../helpers/Common';
    import { VueEditor } from "vue2-editor";

    export default {
        components: { VueEditor },

        mounted() {
            if(this.dataResults=="[]"){
                this.results = this.defaultResult;
            }else{
                // this.results.end_date = (new Date(this.results.end_date)).toISOString().slice(0,10);
            }
        },

        props: [
            'dataResults',
            'dataSite',
            'dataControllerName',
            'dataBackUrl'
        ],

        computed: {

        },

        data() {
            return {
                title:'',
                errorMessage:'',
                results: JSON.parse(this.dataResults),
                defaultResult:{
                    id: 0,
                    site_id: this.dataSite,
                    publish_status: 0,
                    keyword: '',
                    created_at: "",
                    updated_at: "",
                    deleted_at: "",
                    lang: [
                        {
                            id: 0,
                            seo_link_id: 0,
                            lang_id: 0,
                            title: "",
                            description: "",
                            created_at: "",
                            updated_at: "",
                            deleted_at: ""
                        }
                    ]
                },
            }
        },

        methods: {
            saveSeoSrpTuple() {
                if (this.results.publish_status) {
                    this.results.publish_status = 1;
                } else {
                    this.results.publish_status = 0;
                }
                let url = AdminConfig.admin_path(this.dataControllerName + '/saveSeoSrpTuple');
                let $this = this;
                this.saveNow(url, this.results).then(function (res) {
                    if (res.data.error) {
                        Toast.show($this, res.data.error);
                        console.log(res.data.error);
                    } else if (res.data.success) {
                        Toast.show($this, "Saved");
                        console.log("Saved ...");
                        window.location.href = AdminConfig.admin_path($this.dataControllerName);
                    }
                }).catch(function (res) {
                    console.error(res.message);
                    Toast.show($this, "Some error occured... ");
                });

            },
            saveNow(url, data) {
                return new Promise((resolve, reject) => {
                    axios.post(url, data)
                        .then(response => {
                            resolve(response);
                        }).catch(error => {
                        reject(error.response);
                    });
                });
            },
            logEditorData() {
                console.log(this.results);
            },
        }
    };
</script>

<style>
    /*#editor2 {*/
        /*height: 100px;*/
    /*}*/
    /*#editor1 {*/
        /*min-height: 600px;*/
    /*}*/
</style>