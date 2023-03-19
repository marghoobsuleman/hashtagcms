<template>
    <div>
        <div class="col-lg-6">
            <form id="addEditFrm" method="post" class="form-horizontal" role="form"
                  v-on:submit.prevent="saveNow" v-on:keyup="hideErrorMessage($event)">
                <div>
                    <div class="box-with-shadow p10">
                        <p><strong>Source Language:</strong> Data will be copied from the selected lanuage</p>
                        <select name="sourceLang" class="form-control" v-model="form.sourceLang" @change="hideErrorMessage($event)">
                            <option value="">Select</option>
                            <option v-for="language in languages" :value="language.id">{{language.name}} - {{language.iso_code}}</option>
                        </select>
                        <div class="text text-danger">{{this.errors.sourceLang}}</div>
                    </div>

                </div>
                <div>
                    <div class="box-with-shadow p10">
                        <p><strong>Target Language:</strong> New record will be inserted with this language id in selected tables</p>
                        <select name="targetLang" class="form-control" v-model="form.targetLang" @change="hideErrorMessage($event)">
                            <option value="">Select</option>
                            <option v-for="language in languages" :value="language.id">{{language.name}} - {{language.iso_code}}</option>
                        </select>
                        <div class="text text-danger">{{this.errors.targetLang}}</div>
                    </div>

                </div>

                <div>
                    <div class="box-with-shadow p10">
                        <p>Select tables</p>
                        <select style="height: 200px" name="tables" class="form-control" multiple v-model="form.tables" @change="hideErrorMessage($event)">
                            <option v-for="langTable in languageTables"> {{langTable.name}}</option>
                        </select>
                        <div class="text text-danger">{{this.errors.tables}}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="alert alert-danger" v-show="typeof errorMessage == 'string' && errorMessage != '' ">{{errorMessage}}</div>

                    <div v-for="message in messages" :class="getCss(message)">
                        {{message.message}}
                    </div>

                    <div class="form-group center-align">
                        <input type="submit" name="submit" value="Submit and Hold on" class="btn btn-success btn-large"/>
                    </div>
                </div>

            </form>
        </div>

    </div>
</template>

<script>

    import Form from '../helpers/Form';
    import {Toast} from '../helpers/Common';
    export default {

        mounted() {

        },
        props: [
            'dataLanguages',
            'dataLanguageTables'
        ],
        data() {
            return {
                languages: ((this.dataLanguages && this.dataLanguages!="null") ? JSON.parse(this.dataLanguages) : []),
                languageTables: ((this.dataLanguageTables && this.dataLanguageTables!="null") ? JSON.parse(this.dataLanguageTables) : []),
                messages:[],
                form: new Form({
                    tables: [],
                    sourceLang:'',
                    targetLang:''
                }),
                errors:{},
                saveUrl:'language/translateNow',
                errorMessage:''
            }

        },
        methods: {
            getCss(row) {
                return row.status == 0 ? 'text text-danger' : 'text text-success';
            },
            showError(res) {
                //console.log("res");
                //console.log(res);
                for(let i in res.errors) {
                    if(res.errors.hasOwnProperty(i)) {
                        this.$set(this.errors, i, res.errors[i][0]);
                    }
                }

                this.errorMessage = res.message;

            },
            hideErrorMessage(event) {

                let name = event.target.getAttribute("name");

                this.$set(this.errors, name, "");

                if(this.errorMessage != '') {
                    this.errorMessage = '';
                }
            },
            onSuccess(response) {

                console.log(response);
                //console.log("response.isSaved ",response.isSaved);
                if(response.isSaved == 0) {
                    Toast.show(this, response.message);
                    this.errorMessage = {};
                    this.errorMessage = response.message;

                } else {

                    this.errorMessage = '';
                    //Toast.show(this, response.message, 5000);
                    this.messages = response.message;
                }
            },
            saveNow() {
                let $this = this;
                let url = AdminConfig.admin_path(this.saveUrl);

                this.form.post(url)
                    .then(response => this.onSuccess(response))
                    .catch(response => this.showError(response));

                return false;

            }
        }
    }

</script>
