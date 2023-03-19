<template>
    <div class="container-fluid border-bottom mb-2 pb-2">
        <div class="row">
            <div class="col">
                <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group" role="group">
                        <h4 class="moduleTitle">{{getTitle(controllerTitle)}}</h4>
                    </div>
                    <div class="input-group actionToolbar">
                        <language-button v-if="hasLangMethod" :data-languages="dataLanguages" :data-selected-language="dataSelectedLanguage">English</language-button>
                        <button v-if="showAdd && showAddButtonBasedOnAction" title="Add New" @click="addNew" type="button" class="btn btn-outline-secondary" aria-label="Add New">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </button>
                        <a v-for="actionButton in moreActionButtons"  class="btn btn-outline-secondary" :href="getAction(actionButton.action)" :title="actionButton.label">
                            <i v-if="actionButton.icon_css" :class="actionButton.icon_css" aria-hidden="true"></i>
                            <span v-if="getButtonType(actionButton)=='button'">{{actionButton.label}}</span>
                        </a>
                        <button v-if="showSearchButton" title="Search" @click="showHideSearch" type="button" :class="isActive" aria-label="Search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                        <button v-if="fields.length > 0" title="Change Layout" @click="changeLayout()" type="button" class="btn btn-outline-secondary" aria-label="Layout">
                            <i :class="layoutIcon" aria-hidden="true"></i>
                        </button>
                        <a v-if="showBack" class="btn btn-outline-secondary" :href="backURL">Back</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col"  v-if="showSearchPanel">
                <search-bar :class="searchAnim"
                            :data-controller-name="controllerName"
                            :data-selected-params="selectedParams"
                            :data-fields="fields"
                            :data-action-fields="actionFields"
                ></search-bar>
            </div>
        </div>
    </div>
</template>

<script>
  import CmsModuleDropdown from './cmsModuleDropdown.vue';
  import LanguageButton from './languageButton.vue';
  import SearchBar from "./searchBar.vue";
  import {Toast} from '../helpers/common';
  import Humanize from "../helpers/humanize";


  export default {

    components:{
      'cms-module-dropdown':CmsModuleDropdown,
      'language-button':LanguageButton,
        'search-bar':SearchBar
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
              return (this.layoutType === "grid") ? "fa fa-th-list" : "fa fa-table";
          },
          showAddButtonBasedOnAction() {
            return this.actionFields.indexOf("edit") !== -1;
          }
      },
      data() {
        return {
            canAdd:true,
            showSearchButton:(!(typeof this.dataShowSearch === "undefined" || this.dataShowSearch === "" || this.dataShowSearch === "false")),
            showSearchPanel:false,
            isActive:'btn btn-outline-secondary',
            searchAnim:'',
            moreActionButtons:(typeof this.dataMoreActions === "undefined" || this.dataMoreActions === "") ? [] : JSON.parse(this.dataMoreActions),
            showAdd:(typeof this.dataShowAdd === "undefined" || this.dataShowAdd === "true"),
            hasLangMethod:(!(typeof this.dataHasLangMethod === "undefined" || this.dataHasLangMethod === "false")),
            cmsModules:(typeof this.dataCmsModules === "undefined" || this.dataCmsModules === "") ? [] : JSON.parse(this.dataCmsModules),
            selectedParams:(typeof this.dataSelectedParams === "undefined") ? "" : this.dataSelectedParams,
            controllerName:(typeof this.dataControllerName === "undefined") ? "" : this.dataControllerName,
            controllerTitle:(typeof this.dataControllerTitle === "undefined") ? "" : this.dataControllerTitle,
            fields:(typeof this.dataFields === "undefined" || this.dataFields === "") ? [] : this.dataFields,
            actionFields:(typeof this.dataActionFields === "undefined" || this.dataActionFields === "") ? [] : this.dataActionFields,
            showBack:(this.dataShowBack === "true"),
            layoutType: (typeof this.dataLayoutType === "undefined" || this.dataLayoutType === "") ? "table" : this.dataLayoutType,
        }
      },
    methods: {
        changeLayout() {
            Toast.show(this, "Please wait. Changing listing style for you...", 5000);
            this.layoutType = (this.layoutType === "table") ? "grid" : "table";
            if ('URLSearchParams' in window) {
                let searchParams = new URLSearchParams(window.location.search);
                searchParams.set("layout", this.layoutType);
                window.location.search = searchParams.toString();

            }
         },
        getAction(val) {

            if(val.indexOf("http") === 0) {
                return val;
            }
            return AdminConfig.admin_path(val);
        },
        maintainActiveSearch(animate=true) {
            this.isActive = (this.showSearchPanel === true) ? 'btn btn-success' : 'btn btn-outline-secondary';
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
            }

        },
        addNew() {
            if(this.controllerName !== "") {
                var controller_url = AdminConfig.admin_path(this.controllerName);
                window.location.href = controller_url+"/create";
            }
        },
        getButtonType(row) {
            return (!row.as || row.as==="button") ? "button" : row.as;
        },
        getTitle: function (text) {
          return Humanize(text);
        }
    }
  }

</script>
