<template>
    <button @click="downloadNow()" type="button" class="btn btn-info">
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
          let path = AdminConfig.admin_path(this.dataControllerName+"/download");
          window.location.href = path;
      }
  }
}

</script>
