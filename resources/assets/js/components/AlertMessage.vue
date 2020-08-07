<template>
    <div>
        <div :class="css + ' alert mb-0 appear'" v-if="message!=''">
            {{message}} <span @click="closeAlert" class="pull-right pointer"><i class="fa fa-times"></i></span>
        </div>
    </div>
</template>

<script>

    export default {

        mounted() {
            //console.log(this.message)
        },
        props: [
            'dataMessage',
            'dataType'
        ],
        computed: {

            css() {
                if(typeof this.dataType == "undefined" || this.dataType == 'error') {
                    return "alert-danger";
                }
                return 'alert-success';
            }
        },
        data() {
            return {
                message:(typeof this.dataMessage == "undefined" || this.dataMessage == '') ? '' : this.dataMessage
            }
        },
        methods: {
            closeAlert() {
                this.message = '';
            },
            setMessage(message) {
                this.message = message;
            }
        }
    }

</script>


