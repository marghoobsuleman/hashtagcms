<template>
  <div class="row admin-pagination">
    <div class="pageRow">
      <slot></slot>
      <span class="counters" v-if="totalCount > 0">
        {{dataFirstItem}} - {{lastItem}} of {{totalCount}}
      </span>
        <span class="pull-right" style="margin-right:16px">
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
          'dataCount',
          'dataCurrentPage',
          'dataFirstItem',
          'dataHasMorePages',
          'dataLastItem',
          'dataLastPage',
          'dataNextPageUrl',
          'dataPerPage',
          'dataPreviousPageUrl',
          'dataTotal',
          'dataControllerName'
      ],
      data() {
        return {
            totalCount: parseInt(this.dataTotal),
            lastItem:parseInt(this.dataLastItem),
            controllerName:this.dataControllerName
        }
      },
      methods: {
          decreaseCounter() {
              this.totalCount = this.totalCount - 1;
              this.lastItem = this.lastItem - 1;
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
