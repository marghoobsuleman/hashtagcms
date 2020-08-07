<template>
  <a href="#" @click="goBack()" class="btn btn-default"><slot>Back in </slot><span>{{totalTime - timeRemaining}} {{seconds}}</span></a>
</template>

<script>

export default {
  props:[
        'dataBackUrl',
        'dataTimeout'
        ],
  data() {
     return {
         totalTime:(typeof this.dataTimeout == "undefined") ? 5 : parseInt(this.dataTimeout),
         timeRemaining:0,
         intervalId:0,
         seconds:'Seconds'
     }
  } ,
  mounted() {

      this.intervalId = setInterval(this.updateTime, 1000);

  },
  methods: {
      goBack() {

          //console.log(this.dataBackUrl);
          clearInterval(this.intervalId);
          window.location.href = this.dataBackUrl;

      },

      updateTime() {

        if(this.timeRemaining >= this.totalTime) {

          clearInterval(this.intervalId);
          this.goBack();

        } else {

           this.timeRemaining++;

        };

        this.seconds = ((this.totalTime - this.timeRemaining) <= 1 ) ? "Second" : "Seconds";

      }
  }
}

</script>
