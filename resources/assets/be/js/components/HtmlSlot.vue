<template>
  <div>
    <slot></slot>
  </div>
</template>

<script>

export default {

  mounted() {
   // console.log("html slot mounted");
    for(var i in this) {
        if(this.hasOwnProperty(i)) {
           // console.log(i + " : "+ this[i].toString());
        }
    }
  },
  methods: {
    getAllInputs: function() {
        console.log("all inputs");
    }
  }
}

</script>
