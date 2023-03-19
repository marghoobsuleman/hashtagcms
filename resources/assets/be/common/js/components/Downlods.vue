<template>
    <button @click="downloadNow()" type="button" class="btn btn-dark">
        <slot>Download</slot>
    </button>
</template>

<script>

  export default {

  mounted() {

  },
  props:[
      'dataControllerName'
  ],
  methods: {
      downloadNow() {
          window.location.href = AdminConfig.admin_path(this.dataControllerName+"/download");
      }
  }
}

</script>
