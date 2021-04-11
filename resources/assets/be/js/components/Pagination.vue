<template>
  <div class="row admin-pagination">
      <nav aria-label="navigation">
          <ul class="pagination" v-if="showPagination">
              <li v-for="page in allPages" :class="getCss(page)"><a class="page-link" :href="getLink(page)" v-html="page.label"></a></li>
          </ul>
          <span class="counters" v-if="totalCount > 0">
                 {{dataFirstItem}} - {{lastItem}} of {{totalCount}}
          </span>
      </nav>
      <div class="pageRow">
        <span class="pull-right" v-if="totalCount > 0" style="margin-right:16px">
            <download-button :data-controller-name="controllerName"></download-button>
        </span>
    </div>
  </div>
</template>

<script>

  import {EventBus} from "../helpers/event-bus";
  import DownloadButton from './Downlods';
  export default {
      components:{
          'download-button':DownloadButton
      },
      mounted() {
        this.updatePageParams();
          let $this = this;
          EventBus.$on('pagination-on-delete', function () {
              $this.decreaseCounter();
          });

      },
      props:[
          'dataPaginator',
          'dataFirstItem',
          'dataLastItem',
          'dataTotal',
          'dataControllerName'
      ],
      data() {
        return {
            totalCount: parseInt(this.dataTotal),
            lastItem:parseInt(this.dataLastItem),
            controllerName:this.dataControllerName,
            paginator:JSON.parse(this.dataPaginator)
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
          getCss(page) {
              if(page.url == null) {
                  return "page-item disabled";
              }
              return page.active === true ? "page-item active" : "page-item";
          },
          updatePageParams() {
            var params = window.location.search.substring(1);
           // console.log("params "+params);
            var hasPage = params.match(/page=\d+/gi);
            if(hasPage!=null) {
                params = params.replace(/page=\d+/, "");
            }
            if(params!="") {

                params = (params.indexOf("&")==0) ? params : "&"+params;
                var elements = document.querySelectorAll(".pagination a");
                elements.forEach(function(ele, index) {
                  var href = ele.href;
                  href = href+""+params;
                  //remove last and double &
                  href = href.replace(/&$/, "");
                  href = href.replace(/&&/, "&");
                  ele.href = href;
                });
            };
        }
      }
}

</script>
