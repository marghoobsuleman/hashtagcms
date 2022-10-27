<template>
  <div>
      <div class="row border-bottom">
      <div class="col-md-6">
          <h3>{{controllerTitle | humanize}}</h3>
      </div>

       <div class="btn-toolbar" role="toolbar">
          <div class="btn-group" style="float: right; right: 18px;margin-top: 11px;">

              <cms-module-dropdown
                      :data-modules="cmsModules"
              ></cms-module-dropdown>

              <language-button v-if="hasLangMethod" :data-languages="dataLanguages" :data-selected-language="dataSelectedLanguage">English</language-button>

              <button v-if="showAdd && showAddButtonBasedOnAction" title="Add New" @click="addNew" type="button" class="btn btn-default" aria-label="Add New">
                 <i class="fa fa-plus" aria-hidden="true"></i> Add New
              </button>

              <a v-for="actionButton in moreActionButtons"  class="btn btn-default" :href="getAction(actionButton.action)" :title="actionButton.label">
                  <i v-if="actionButton.icon_css" :class="actionButton.icon_css" aria-hidden="true"></i>
                  <span v-if="getButtonType(actionButton)=='button'">{{actionButton.label}}</span>
              </a>

              <button v-if="showSearchButton" title="Search" @click="showHideSearch" type="button" :class="isActive" aria-label="Search">
                  <i class="fa fa-search" aria-hidden="true"></i>
              </button>
              <button v-if="fields.length > 0" title="Layout" @click="changeLayout()" type="button" class="btn btn-default" aria-label="Layout">
                  <i :class="layoutIcon" aria-hidden="true"></i>
              </button>
          </div>
           <div v-if="showBack" class="pull-right back-link">
               <a :href="backURL">Back</a>
           </div>
       </div>
      </div>
      <div v-if="showSearchPanel" class="row pull-right" style="margin:5px 0; padding-right: 15px">
      <search-bar :class="searchAnim"
              :data-controller-name="controllerName"
              :data-selected-params="selectedParams"
              :data-fields="fields"
              :data-action-fields="actionFields"
      ></search-bar>
      </div>
  </div>
</template>

<script>

  import LanguageButton from '../components/LanguageButton';
  import {Toast} from '../helpers/Common';


  export default {

    components:{
      'language-button':LanguageButton
    },
    mounted() {

        this.shouldShowSearchPanel();

       //console.log(this.dataShowSearch);

    },
    props:[
        'dataSelectedParams',
        'dataControllerName',
        'dataControllerTitle',
        'dataFields',
        'dataActionFields',
        'dataLanguages',
        'dataSelectedLanguage',
        'dataMoreActions',
        'dataShowAdd',
        'dataHasLangMethod',
        'dataCmsModules',
        'dataShowSearch',
        'dataShowBack',
        'dataLayoutType'
    ],
      computed: {
          backURL() {
              return AdminConfig.admin_path(this.controllerName);
          },
          layoutIcon() {
              return (this.layoutType == "grid") ? "fa fa-th-list" : "fa fa-table";
          },
          showAddButtonBasedOnAction() {
            if(this.actionFields.indexOf("edit") == -1) {
                return false;
            }
            return true;
          }
      },
      data() {
        return {
            canAdd:true,
            showSearchButton:(typeof this.dataShowSearch === "undefined" || this.dataShowSearch == "" || this.dataShowSearch == "false") ? false : true,
            showSearchPanel:false,
            isActive:'btn btn-default',
            searchAnim:'',
            moreActionButtons:(typeof this.dataMoreActions === "undefined" || this.dataMoreActions === "") ? [] : JSON.parse(this.dataMoreActions),
            showAdd:(typeof this.dataShowAdd === "undefined" || this.dataShowAdd === "true") ? true : false,
            hasLangMethod:(typeof this.dataHasLangMethod === "undefined" || this.dataHasLangMethod == "false") ? false : true,
            cmsModules:(typeof this.dataCmsModules === "undefined" || this.dataCmsModules == "") ? [] : JSON.parse(this.dataCmsModules),
            selectedParams:(typeof this.dataSelectedParams === "undefined") ? "" : this.dataSelectedParams,
            controllerName:(typeof this.dataControllerName === "undefined") ? "" : this.dataControllerName,
            controllerTitle:(typeof this.dataControllerTitle === "undefined") ? "" : this.dataControllerTitle,
            fields:(typeof this.dataFields === "undefined" || this.dataFields == "") ? [] : this.dataFields,
            actionFields:(typeof this.dataActionFields === "undefined" || this.dataActionFields == "") ? [] : this.dataActionFields,
            showBack:(this.dataShowBack === "true") ? true : false,
            layoutType: (typeof this.dataLayoutType === "undefined" || this.dataLayoutType === "") ? "table" : this.dataLayoutType,
        }
      },
    methods: {
        changeLayout() {
            Toast.show(this, "Please wait. We are changing listing style for you...", 5000);
            this.layoutType = (this.layoutType == "table") ? "grid" : "table";
            if ('URLSearchParams' in window) {
                var searchParams = new URLSearchParams(window.location.search);
                searchParams.set("layout", this.layoutType);
                window.location.search = searchParams.toString();

            }

         },
        getAction(val) {

            if(val.indexOf("http") == 0) {
                return val;
            }
            return AdminConfig.admin_path(val);
        },
        maintainActiveSearch(animate=true) {
            this.isActive = (this.showSearchPanel === true) ? 'btn btn-success' : 'btn btn-default';
            this.searchAnim = (this.showSearchPanel === true && animate==true) ? "animated flipInX" : "";
        },
        showHideSearch() {
            this.showSearchPanel = !this.showSearchPanel;
            this.maintainActiveSearch();
        },
        shouldShowSearchPanel() {
            if(this.selectedParams !== "") {
                if(JSON.parse(this.selectedParams).q) {
                    this.showSearchPanel = true;
                }
                this.maintainActiveSearch(false);
            };

        },
        addNew() {
            if(this.controllerName !== "") {
                var controller_url = AdminConfig.admin_path(this.controllerName);
                window.location.href = controller_url+"/create";
            }
        },
        getButtonType(row) {
            return (!row.as || row.as==="button") ? "button" : row.as;
        }
    }
  }

</script>
