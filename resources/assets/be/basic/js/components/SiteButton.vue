<template>
<div>
    <select :name="name" :id="id" @change="setSite" v-model="selected">
        <option value="">Select Site</option>
        <option v-for="site in sites" :value="site.id">
            {{site.name}}
        </option>
    </select>
</div>

</template>

<script>
    import SplitButton from '../library/SplitButton';
    import {EventBus} from "../helpers/event-bus";

    export default {
        components:{
            'split-button':SplitButton
        },
        props:[
            'dataName',
            'dataId',
            'dataSites',
            'dataSelected'
        ],
        mounted() {
            //this.init();
        },
        data() {
          return {
              sites:(typeof this.dataSites === "undefined") ? [] : JSON.parse(this.dataSites),
              selected:(typeof this.dataSelected === "undefined") ? 1 : parseInt(this.dataSelected),
              name:(typeof this.dataName === "undefined") ? "site_id" : this.dataName,
              id:(typeof this.dataId === "undefined") ? "site_id" : this.dataId
          }
        },
        methods: {
            setSite() {
                EventBus.$emit("site_changed", this.selected);
            }
        }
    }

</script>