<template>
  <div class="clearfix">
      <div v-if="layoutType == 'grid'">
          <table class="table margin-top-10 table-grid" v-for="row in rows" :key="row.key" :id="'row_'+row.id">
              <tr v-for="fields in headings">
                  <td :class="'header head_'+getFieldName(fields)">
                      {{getFieldName(fields) | fieldsName }}
                      <div class="clearboth"></div>
                  </td>
                  <td>
                      <span v-html="getFieldValue(row, getFieldName(fields, 'key'), fields)"></span>

                      <div class="actions" v-if="isActionFieldKey(getFieldName(fields, 'key'))" v-html="getActionValue(row)"></div>
                        <div class="clearboth"></div>
                  </td>
              </tr>
          </table>
      </div>
      <table class="table table-bordered margin-top-10"  v-if="layoutType == 'table'">
        <thead>
          <tr>
            <th v-for="fields in headings" :class="'head_'+getFieldName(fields) | snakeCase">
              {{getFieldName(fields) | fieldsName }}
            </th>
          </tr>
        </thead>
        <tbody>
        <tr v-for="row in rows" :key="row.key" :id="'row_'+row.id" class="list-table-row">
          <td v-for="fields in headings">
            <span v-html="getFieldValue(row, getFieldName(fields, 'key'), fields)"></span>

            <div class="actions" v-if="isActionFieldKey(getFieldName(fields, 'key'))" v-html="getActionValue(row)"></div>

          </td>
        </tr>
        <tr v-if="isZeroResult()">
          <td :colspan="getTotalColumns()" class="alert-danger">

            <div > No results found...</div>
          </td>

        </tr>
        </tbody>
      </table>
    <modal-box :data-show-modal="false" ref="deleteBox" @onClose="deleteNow" :data-show-footer="true">
      <span slot="title">Alert</span>
      <span slot="content" class="text-danger">{{alertMessage.current}}</span>
      <div slot="footer">
        <label class="form-check-label">
          <input class="form-check" type="checkbox" v-model="preventDeleteBox" /> Don't ask again &nbsp;
        </label>
        <button v-show="isDelete" @click="deleteNow(0)" type="button" class="btn btn-default" data-dismiss="modal">
          No
        </button>
        <button v-show="isDelete" @click="deleteNow(1)" type="button" class="btn btn-primary">
          Yes
        </button>
        <button v-show="!isDelete" @click="closeDialog()" type="button" class="btn btn-primary">
          Okay
        </button>

      </div>

    </modal-box>

  </div>
</template>

<script>

import {Toast, queryBuilder, Modal} from '../helpers/Common';
import {FX} from '../helpers/Fx';
import {EventBus} from "../helpers/event-bus";

export default {

  mounted() {



     this.highlight();
     this.bindAction();
      if(this.showDeletePopup == "FALSE") {
          this.preventDeleteBox = true;
      }
      let $this = this;
      EventBus.$on('list-view-hide-spinner', function () {
          $this.showHideSpinner(undefined, "false");
      });

  },
  created() {

      //console.log("created");
      //console.log(this.headings);
      //console.log("this.moreActions ",this.moreActions);
      //console.log("this.actionFields ",this.actionFields);
  },
  props:[
      'dataHeaders',
      'dataList',
      'dataActionFields',
      'dataMoreActionFields',
      'dataControllerName',
      'dataActionAsAjax',
      'dataActionCss',
      'dataUserRights',
      'dataMakeFieldAsLink',
      'dataShowDeletePopup',
      'dataEditHandledByOtherComponent',
      'dataMinResultsNeeded',
      'dataLayoutType'
  ],

  data() {

      return {
          /*headings:(this.dataHeaders ? JSON.parse(this.dataHeaders) : []),*/
          rows:(this.dataList ? JSON.parse(this.dataList) : []),
          actionFields:((this.dataActionFields && this.dataActionFields!="null") ? JSON.parse(this.dataActionFields) : []),
          userRights:(this.dataUserRights ? JSON.parse(this.dataUserRights) : []),
          moreActions:((this.dataMoreActionFields && this.dataMoreActionFields!="null") ? JSON.parse(this.dataMoreActionFields) : []),
          actionAjax:((this.dataActionAsAjax && this.dataActionAsAjax!="null") ? JSON.parse(this.dataActionAsAjax) : this.getDefaultValue('action_as_ajax', [])),
          actionIconCss:((this.dataActionCss && this.dataActionCss!="null") ? JSON.parse(this.dataActionCss) : this.getDefaultValue('action_icon_css', [])),
          alertMessage:{current:'', delete:"Are you sure to delete this?", permission:"Sorry! You don't have permission to delete this."},
          isDelete:true,
          currentActionItem:null,
          preventDeleteBox:false,
          makeFieldAsLink:(this.dataMakeFieldAsLink ? JSON.parse(this.dataMakeFieldAsLink) : []),
          fieldAsLinkCache:{},
          showDeletePopup:(this.dataShowDeletePopup ? this.dataShowDeletePopup.toString().toUpperCase() : "FALSE"),
          editHandledByOtherComponent:(typeof this.dataEditHandledByOtherComponent !== "undefined" || this.dataEditHandledByOtherComponent == "true") ? true : false,
          minResultsNeeded:(typeof this.dataMinResultsNeeded === "undefined" || this.dataMinResultsNeeded == "") ? -1 : parseInt(this.dataMinResultsNeeded),
          layoutType: (typeof this.dataLayoutType === "undefined" || this.dataLayoutType === "") ? "table" : this.dataLayoutType

      }
  },
  computed: {
      headings() {
          let heading = (this.dataHeaders ? JSON.parse(this.dataHeaders) : []);

          let allHeadings = [];
          if(heading.length > 0 ) {
              heading.forEach(function (current, index) {
                  let label = current.label || current;
                  let key = current.key || current;
                  let isImage = (current.isImage && current.isImage.toString() == "true") ? true : false;
                  allHeadings.push({label, key, isImage});
              })
          }

         // console.log("allHeadings ",allHeadings)
          return allHeadings;
      }
  },
  methods: {
      isActionFieldKey(key) {
          //console.log("key ",key);
          //will have edit/delete/approve etc
        return (key != undefined && (key.toLowerCase()=='action' || key.toLowerCase() == 'moreactions'));
      },
      isMakeFieldAsLink(key) {

          //Making Cache for good performance

          if(this.fieldAsLinkCache[key] != undefined) {
              return this.fieldAsLinkCache[key];
          }

          //console.log("searching... "+key);

          let found = this.makeFieldAsLink.find(function(current,index) {
              if(current.key == key || current.action == key) {
                  return current;
              }
          });

          this.fieldAsLinkCache[key] = (found == undefined) ? -1 : found;

          return this.fieldAsLinkCache[key];
      },

      getFieldName(key, prop) {
        prop = (prop) ? prop : 'label';
        return (typeof key == "string") ? key : (key[prop]==undefined) ? key : key[prop];
      },

      getFieldValue(row, key, fields) {

        var $this = this;

        //console.log("key", key, fields);

        //data has something like lang.name
        if(key.includes(".")) {

          var extract = key.split(".");
          var scope = extract[0];
          var fieldName = extract[1];

          var scope = row[scope];

          switch (Object.prototype.toString.call(scope)) {
              case "[object Undefined]":
                  scope = "";
                  break;
              case "[object Array]":
                  scope = (scope.length>0) ? scope[0][fieldName] : scope[fieldName];
                  break;
              case "[object Object]":
                  scope = scope[fieldName] || "";
                  break;
          }

          return scope;
        }

        //get value
        return getActualValue(row, key, fields);



        function isAction(searchKey) {

            return $this.makeFieldAsLink.find(function(current,index) {
                if(current.key == searchKey) {
                    return current;
                }
            });

        };

        //Get actual value
        function getActualValue(row, forKey, fields) {

            let withIcon = true;
            //var actionAlias = {"id":"edit", "publish_status":"publish"};
            var value = "";

            var actionAlias = $this.isMakeFieldAsLink(forKey);

            // check if this is action field
            if(actionAlias !== undefined && actionAlias !=- 1) {
                //console.log(current=="edit" && this.actionFields.indexOf("edit")!=-1);

                var current = forKey;
                var actionName = actionAlias.action || current;

                //console.log("actionName ",actionName, " ", current);
                var title = actionName.charAt(0).toUpperCase() + actionName.slice(1);
                //var path = (actionName=="edit") ? `${row.id}/${actionName}` : `${actionName}/${row.id}`;
                var path = `${actionName}/${row.id}`;
                path = AdminConfig.admin_path(`${$this.dataControllerName}/${path}`);

                var iconOrValue = row[forKey];
                var isAjaxCss = "";
                var status = "";

                // check if this is ajax : actionAjax
                if($this.isAjax(forKey)) {

                    isAjaxCss = $this.getAjaxCss(current);

                    isAjaxCss += " " +forKey+"_"+row[forKey]; //kind of "publish_1" or "publish_0";

                    if(withIcon) {
                        let icon_css = actionAlias["css_"+row[forKey]] || "";
                        iconOrValue = `<i class="js_icon ${icon_css}"></i>`;
                    }

                    //if this user has no rights - disable it
                    if(!$this.can(actionName)) {
                        isAjaxCss += " disabled";
                    };
                    status = row[forKey];
                }

                value = `<a class="${isAjaxCss}" data-value="${status}" data-rowid="${row.id}" data-action="${actionName}" title="${title}" href="${path}">${iconOrValue}</a>`;

                //if edit and
                if(forKey == "id" && $this.actionFields.indexOf("edit") == -1) {
                    value = iconOrValue;
                }



            } else {
                value = row[forKey];
                let media_path = AdminConfig.get("media_path")
                value = (fields.isImage === false) ? value : (value != '' && value != null) ? `<img height="30" src='${media_path}/${value}' />` : value;
            }

            return value;

        }

      },
      getActionValue(row, key) {

          var html = [];

          //this is last column things - edit/delete etc
          if(this.actionFields.length > 0) {

              for(var i=0;i<this.actionFields.length;i++) {
                let current = this.actionFields[i];
                let isAjax = this.getAjaxCss(current);
                let title = current.charAt(0).toUpperCase() + current.slice(1);

                let path = "";

                if(current=="edit") {
                    //path = AdminConfig.admin_path(`${this.dataControllerName}/${row.id}/${current}`);
                    path = AdminConfig.admin_path(`${this.dataControllerName}/${current}/${row.id}`);
                } else if(current=="delete") {

                    //console.log("this.minResultsNeeded <= this.rows.length ", this.minResultsNeeded , this.rows.length)

                    if(this.minResultsNeeded !== -1 && this.minResultsNeeded >= this.rows.length) {
                        console.info(`Hiding delete button, because ${this.minResultsNeeded} record(s) needed.`);
                    } else {
                        path = AdminConfig.admin_path(`${this.dataControllerName}/destroy/${row.id}`);
                    }
                } else {
                    path = AdminConfig.admin_path(`${this.dataControllerName}/${current}/${row.id}`);
                }

                if(path !== "") {
                    let icon = `<i class="js_icon ${this.getIconCSS(current)}"></i>`;
                    let link = `<a class="${isAjax}" data-rowid="${row.id}" data-action="${current}" title="${title}" href="${path}" class="${current}">${icon}</a>`;
                    html.push(link);
                }

              }
          }

          if(this.moreActions.length > 0) {
              for(let i=0;i<this.moreActions.length;i++) {
                  let current = this.moreActions[i];
                  let isAjax = this.getAjaxCss(current);
                  let attributes = "";
                  if(current.attribs) {
                      for(let attr in current.attribs) {
                          if(current.attribs.hasOwnProperty(attr)) {
                              attributes += `${attr}='${current.attribs[attr]}' `;
                          }
                      }
                  }
                  let action = current.action;
                  let value = row[current.action_append_field] || "";
                  let title = current.label.charAt(0).toUpperCase() + current.label.slice(1);
                  let path = AdminConfig.admin_path(`${this.dataControllerName}/${action}/${value}`);
                  let icon = `<i class="js_icon ${current.icon_css}"></i>`;
                  let link = `<a ${attributes} class="${isAjax}" data-rowid="${row.id}" data-action="${action}" title="${title}" href="${path}" class="${current}">${icon}</a>`;
                  html.push(link);
              }
          }

        return html.join("&nbsp;");
      },
      can(rights) {
        return (this.userRights.indexOf(rights) >= 0) ? true : false;
      },
      isAjax(action) {

          return (this.actionAjax.indexOf(action) >= 0) ? true : false;
      },
      getAjaxCss(action) {

          let css = "";
          let action_css = (Object.prototype.toString.call(action) === "[object Object]") ? action.action : action;
          if(this.isAjax(action)) {
              css = "js_action js_ajax js_"+action_css;
          } else {
              css = "js_action js_"+action_css;
          }

          return css;
      },
      getDefaultValue(key, dv) {

          return window.Laravel.htcmsAdminConfig(key) || dv;
      },
      getIconCSS(key) {
        var action_icon_css = this.getDefaultValue("action_icon_css", "");

        return action_icon_css[key] || "";
      },
      getTotalColumns() {
          return this.headings.length;
      },
      getTotalRows() {
          return this.rows.length;
      },
      isZeroResult() {
          return this.getTotalRows()==0;
      },
      highlight() {
          var id = queryBuilder.get("id");
          if(id!="") {
              var row = "row_"+id;
              FX.scrollWinTo(row, function() {
                  FX.highlight(row);
              });
          }
      },
      deleteNow(isOk=1) {

          let $this = this;
          var rowid = -1;
          if(isOk==1) {

              var target = this.currentActionItem;
              if(this.can("delete")) {
                  rowid = "row_"+target.getAttribute("data-rowid");
                  var href = target.getAttribute("href");
                  //Delete in db
                  this.doAjax("delete", href, feedback);

              } else {

                  //sorry you don't have permission.
                  Toast.show($this, "Sorry! You don't have permission to delete.");
                  //some issue - will fix this
                  setTimeout(function () {
                      $this.showHideSpinner($this.currentActionItem, false);
                  }, 1000);
              }
          } else {
              $this.showHideSpinner($this.currentActionItem, false);
          }


          //After delete
          function feedback(res) {

              if(res.status==200) {

                  //var ele = document.getElementById(rowid);
                  //ele.classList.add(...["animated", "hinge", "fadeOut"]);

                  setTimeout(function() {
                      //ele.remove();
                      Toast.show($this, "Record has been deleted", 2000);

                      //delete from index
                      let index = $this.rows.findIndex(function(current) {
                          return current.id == res.data.id;
                      });
                      if(index != -1) {
                          if(rowid > 0 && document.getElementById(rowid)) {
                              //document.getElementById(rowid).remove();
                          }

                          $this.rows.splice(index, 1);
                          EventBus.$emit('pagination-on-delete');

                          $this.bindAction();
                      }


                  }, 500);

              } else {
                  $this.showHideSpinner($this.currentActionItem, false);
                  Toast.show($this, "Ooops! Got some error!");
              }

          }

          this.closeDialog();
      },
      showHideSpinner(event, show=true) {
          let css = ["fa-spinner", "fa-pulse", "fa-fw"];
          //console.log("event", event, show);

          if(event) {
              let current = (event instanceof HTMLAnchorElement) ? event : event.currentTarget;
              if(show===true) {
                  current.firstElementChild.classList.add(...css);
              } else {
                  //current.firstElementChild.classList.remove(...css);
                  remvoveAllSpinners(css);
              }
          } else {
              //remove all spinner
              remvoveAllSpinners(css);
          };

          function remvoveAllSpinners(css) {
              let allSpinners = document.querySelectorAll(".fa-spinner");
              if(allSpinners.length > 0) {
                  allSpinners.forEach(function (current) {
                      current.classList.remove(...css);
                  })
              }
          }

      },
      closeDialog() {
          this.$refs.deleteBox.close();
          //this.currentActionItem = null;
      },
      doAjax(methodType="get",url, success, fail) {

          var $this = this;
          return axios[methodType](url)
              .then(function(res) {

                  if(success) {
                      success.apply(this, arguments);
                      //success(res);

                  };

              })
              .catch(function(res) {
                  if(fail) {
                      //fail(res);
                      fail.apply(fail, arguments);
                  } else {
                      //show default message
                      Toast.show($this, res.message, 5000);
                  };

                  $this.showHideSpinner($this.currentActionItem, false);

              });
      },
      initAjaxAction(event) {

          //@todo: still needs to add more action
          event.preventDefault();
          event.stopPropagation();

          var $this = this;
          var current = event.currentTarget;

          //console.log("current ",current);

          let action = current.getAttribute("data-action");
          let status = current.getAttribute("data-value");
          let href = current.getAttribute("href");
          if(status) {
              href = href + "/"+status;
          }

          this.currentActionItem = current;

          switch (action) {
              //handle special case
              case 'delete':
                  this.alertMessage.current = this.alertMessage.delete;
                  this.isDelete = true;

                  if(this.preventDeleteBox==false) {
                      this.openDeleteAlert(current);
                  } else {
                      this.deleteNow(1);
                  }
                  //this.openPermissionAlert();
                  break;

              default:

                  this.doAjax("get", href, function(res) {

                      $this.showHideSpinner(current, false);

                      let actionFieldProp = $this.isMakeFieldAsLink(action);

                      //remove old class
                      let icon = current.getElementsByClassName("js_icon");
                      let icon_css = actionFieldProp["css_"+status];
                      if(icon.length>0) {
                          icon = icon[0];
                          icon.classList.remove(...(icon_css.split(" ")));
                      }
                      //set new value
                      current.setAttribute("data-value", res.data.status);
                      //css handling
                      icon_css = actionFieldProp["css_"+res.data.status];
                      icon.classList.add(...icon_css.split(" "));

                  }, function(res) {

                      $this.showHideSpinner(current, false);
                      Toast.show($this, res.message, 3000);
                  });
                  break;
          };

      },
      bindAction() {

          var allElement = this.$el.getElementsByClassName("js_action");

          //console.log(allElement);

          for(let i=0;i<allElement.length;i++) {
              let current = allElement[i];
              if(current.classList.contains("js_ajax")) {
                  current.addEventListener("click", this.initAjaxAction, false);
              }
              if(current.classList.contains("js_action")) {
                  current.addEventListener("click", this.showHideSpinner, false);
              }
          };

          //handle edit
          if(this.editHandledByOtherComponent == true) {
              let editElements = this.$el.querySelectorAll("a[data-action='edit']");
              editElements.forEach(function (current, index) {
                  current.addEventListener("click", function (event) {
                      event.preventDefault();
                      EventBus.$emit('list-view-pre-edit', this);
                  })
              })

          }


      },
      openDeleteAlert(current) {
          this.$refs.deleteBox.open(current);
      },
      openPermissionAlert() {
          this.alertMessage.current = this.alertMessage.permission;
          this.isDelete = false;
          this.currentActionItem = null;
          this.$refs.deleteBox.open();
      }


  },
  filters: {
    fieldsName: function (value) {
      value = value.replace(/\.|_/g, " ");
      return value.charAt(0).toUpperCase() + value.slice(1);
    },
    snakeCase: function(value) {
        value = value.replace(/\s/g, "_");
        return value.toLowerCase();
    }
  }
}

</script>
