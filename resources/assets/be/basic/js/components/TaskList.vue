<template>
  <ul class="list-group">
    <li :class="['list-group-item', (completedCSS(task))]" v-for="(task, index) in listData">
      {{task.body}}
      <input type="checkbox" v-model="task.completed" @change="udpateTask(task, $event, index)"  :key="task.id" />
    </li>
  </ul>
</template>

<script>
    export default {
        props:['list'],
        mounted() {
            //console.log('Component mounted...')
        },
        data() {
          return {
            listData:JSON.parse(this.list)
          }
        },
        methods: {
          completedCSS(task) {
            return task.completed ? 'completed' : '';
          },
          udpateTask(task, evt, index) {
            var isChecked = evt.target.checked;
            task.completed = (isChecked==true) ? 1 : 0;
            this.updateTaskStatus(task, index);
          },
          updateTaskStatus(task, index) {
            var $this = this;
            axios.post('/task', {
                id: task.id,
                completed: task.completed
              })
              .then(function (response) {
                console.log(response);
              })
              .catch(function (error) {
                console.log(error);
              });
          }
        }
    }
</script>

<style>
.completed{text-decoration: line-through;}
</style>
