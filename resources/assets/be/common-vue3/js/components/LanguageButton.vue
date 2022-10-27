<template>
    <split-button v-if="hasLanguage()"
                  :data-options="dataLanguages"
                  @change="setLanguage"
                  :data-parser="parseLang"
                  :data-selected="currentIndex"
                    >English</split-button>
</template>

<script>
    import SplitButton from '../library/SplitButton';

    export default {

        components:{
            'split-button':SplitButton
        },
        props:[
            'dataLanguages',
            'dataSelectedLanguage'
        ],
        mounted() {
            //console.log(this.languages);
        },
        data() {
          return {
              languages:(typeof this.dataLanguages == "undefined") ? null : JSON.parse(this.dataLanguages),
              currentLang:(typeof this.dataSelectedLanguage == "undefined") ? 1 : parseInt(this.dataSelectedLanguage)
          }
        },
        computed: {
          currentIndex() {

              if(this.languages != null) {
                  for (var i = 0; i < this.languages.length; i++) {
                       var item = this.languages[i];

                      if (item.id == this.currentLang) {
                          return i;
                      }
                  }
              }
              //console.log($this.currentLang);
              return 0;
          }
        },
        methods: {
            hasLanguage() {
                //has language greate than one
                return (this.languages != null && this.languages.length>1);
            },
            parseLang: function(row) {
                //console.log(row);
                return {label:row.name, value:row.id};
            },
            setLanguage(data) {
                //console.log("changed happend")

                var ajaxController = AdminConfig.admin_path(`ajax/setLanguage/${data.value}`);

                axios.get(ajaxController)
                    .then(function (response) {
                        //console.log(response);
                        window.location.reload();
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            }
        }
    }

</script>