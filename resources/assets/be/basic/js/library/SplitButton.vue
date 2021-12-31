<template>
    <div class="btn-group">
        <button type="button" class="btn btn-default">
            {{current.label}}
        </button>
        <button type="button" @click="toggleMenu()" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" :style="display" @click="toggleMenu()" ref="dropdownMenu">
            <li :class="isActive(item)" v-for="(item, index) in lists" @click="setCurrent(index)"><a href="javascript:void(0)">{{item.label}}</a></li>
        </ul>
    </div>
</template>

<script>
    export default {

        mounted() {

            this.normalizeData();
            //console.log(this.lists);
        },

        props:[
            'dataOptions',
            'dataSelected',
            'dataParser',
            'dataOnChange'
            ],
        data() {
          return {
              display:"",
              formatter:(this.dataParser),
              lists:(typeof this.dataOptions != "undefined" && typeof this.dataOptions == "string") ? JSON.parse(this.dataOptions) : this.dataOptions,
              current:{},
              selectedIndex:(typeof this.dataSelected == "undefined") ? 0 : parseInt(this.dataSelected),
              openCSS:''
          }
        },
        computed: {
          onChange() {
              let method = (typeof this.dataOnChange == "undefined") ? null : this.dataOnChange;
              if(typeof method == "string" && method !== null) {
                  return eval(method);
              }
              return method;
          }
        },
        methods: {
            toggleMenu() {
                this.display = (this.display=="") ? "display:block" : "";
                if(this.display!="") {
                    //this.openCSS = 'animated slideInDown';
                    this.bindDocumentClick();
                }
            },
            isActive(item) {
                if(this.current.value == item.value) {
                    return "active";
                }
                return "";
            },
            normalizeData() {
                let formatter = this.formatter;
                let arr = [];
                this.lists.forEach(function(item, index) {
                    if(typeof formatter == "function") {
                        arr.push(formatter(item));
                    } else if(typeof index == "number") {
                        arr.push({label:item, value:item});
                    }
                });

                this.lists = arr;
                this.current = this.lists[this.selectedIndex];

            },
            setCurrent(index) {

                this.current = this.lists[index];
                this.current.index = index;

                if(this.onChange != null && typeof this.onChange == "function") {
                    this.onChange(this.current);
                }

                this.$emit("change", this.current);
            },
            mangeShowHide(event) {
                let element = this.$refs.dropdownMenu;
                let target = event.target;

                if ( (element !== target) && !element.contains(target)) {
                    this.display = "";
                    this.unBindDocumentClick();
                };
            },
            bindDocumentClick() {

                document.addEventListener("mouseup", this.mangeShowHide);

            },
            unBindDocumentClick() {
                document.removeEventListener('mouseup', this.mangeShowHide)
            },
            setData(data) {

                this.lists = [];
                this.lists = data;
                this.normalizeData();
            }
        }

    }
</script>