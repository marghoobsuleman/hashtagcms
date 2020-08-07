<template>
    <div>
        <form class="newsletter-form relative" action="/common/subscribe" method="post" v-on:submit.prevent="saveNow">
            <div class="row">
                    <div class="col-8" style="padding: 0; margin: 0">
                        <input v-model="email" type="email" name="email" class="form-control" placeholder="Email address" required style="height: 100%">
                    </div>
                    <div class="col-4" style="padding: 0 0 0 3px">
                        <button class="btn btn-lg btn-info btn-block" type="submit">Submit</button>
                    </div>
                <div :class="css + ' alert col-12 mt-2'" v-show="message">
                    {{message}} <span @click="closeAlert" class="pull-right pointer"><i class="fa fa-times"></i></span>
                </div>
            </div>
        </form>
    </div>
</template>

<script>

    export default {

        mounted() {

        },
        data() {
            return {
                isSaved:true,
                email:'',
                message:'',
                css:'alert-success'
            }
        },
        methods: {
            closeAlert() {
              this.message = '';
            },
            saveNow() {
                let url = "/common/subscribe";
                let data = {email:this.email};
                axios.post(url, data)
                    .then(response => {
                        this.afterSave(response.data);
                    }).catch(response => {
                        this.afterSave(response.data);
                });
            },
            afterSave(data) {
                console.log(data);
                if(data.success == true) {
                    this.message = data.message;
                    this.css = 'alert-success';
                    this.email = '';
                } else {
                    this.message = data.message || "There is some error.";
                    this.css = 'alert-danger'
                }

            }
        }
    }

</script>
