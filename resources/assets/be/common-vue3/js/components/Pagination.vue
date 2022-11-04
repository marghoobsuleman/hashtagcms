<template>
  <div class="row">
    <div class="col-auto">
        <span class="counters" v-if="totalCount > 0">
                 {{dataFirstItem}} - {{lastItem}} of {{totalCount}}
          </span>
    </div>
    <div class="col-auto">
      <nav v-if="showPagination" class="shadow-sm">
        <ul class="pagination">
          <li v-for="page in allPages" :class="getCss(page)"><a class="page-link" :href="getLink(page)" v-html="getLabel(page.label)"></a></li>
        </ul>
      </nav>
    </div>
    <div class="col float-end" v-if="totalCount > 0">
      <span class="pull-right" style="margin-right:16px">
            <download-button :data-controller-name="controllerName"></download-button>
        </span>
    </div>
  </div>
</template>

<script>

  import {EventBus} from "../helpers/event-bus";
  import DownloadButton from './Downlods.vue';

  export default {
      components:{
          'download-button':DownloadButton
      },
      mounted() {
        this.updatePageParams();
          let $this = this;
          EventBus.on('pagination-on-delete', function () {
              $this.decreaseCounter();
          });

      },
      props:[
          'dataPaginator',
          'dataFirstItem',
          'dataLastItem',
          'dataTotal',
          'dataControllerName',
          'dataNextLabel',
          'dataPreviousLabel'
      ],
      data() {
        return {
            totalCount: parseInt(this.dataTotal),
            lastItem:parseInt(this.dataLastItem),
            controllerName:this.dataControllerName,
            paginator:JSON.parse(this.dataPaginator),
            pageLabel:{
                "pagination.next": (this.dataNextLabel),
                "pagination.previous": (this.dataPreviousLabel)
            }
        }
      },
      computed: {
        hasPreviousPage() {
            return this.paginator.prev_page_url != null;
        },
        allPages() {
            return this.paginator.links;
        },
        showPagination() {
            return this.allPages.length > 3; //["Previous", "1", "Next"]
        }
      },
      methods: {
          decreaseCounter() {
              this.totalCount = this.totalCount - 1;
              this.lastItem = this.lastItem - 1;
          },
          getLink(page) {
              return page.url == null ? "javascript:void(0)" : page.url;
          },
          getLabel(label) {
              //label is provided from view file
              if(this.pageLabel[label]) {
                  return this.pageLabel[label];
              }

              return label;
          },
          getCss(page) {
              if(page.url == null) {
                  return "page-item disabled";
              }
              return page.active === true ? "page-item active" : "page-item";
          },
          updatePageParams() {
            let params = window.location.search.substring(1);
           // console.log("params "+params);
            let hasPage = params.match(/page=\d+/gi);
            if(hasPage!=null) {
                params = params.replace(/page=\d+/, "");
            }
            if(params!=="") {

                params = (params.indexOf("&")===0) ? params : "&"+params;
                let elements = document.querySelectorAll(".pagination a");
                elements.forEach(function(ele, index) {
                  let href = ele.href;
                  href = href+""+params;
                  //remove last and double &
                  href = href.replace(/&$/, "");
                  href = href.replace(/&&/, "&");
                  ele.href = href;
                });
            }
        }
      }
}

</script>
