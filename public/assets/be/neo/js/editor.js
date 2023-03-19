/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************************!*\
  !*** ./resources/assets/be/neo/js/editor.js ***!
  \**********************************************/
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var EditorHelper = function () {
  var makeRichEditor = function makeRichEditor(selector, settings) {
    selector = document.querySelector(selector);
    var defaultSettings = {
      selector: "#" + selector.id,
      height: 500,
      theme: 'silver',
      plugins: 'code print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount imagetools textpattern',
      toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist | removeformat | customGallery | image |  code ',
      image_advtab: true,
      template_popup_height: 400,
      template_popup_width: 320,
      valid_elements: '*[*]',
      valid_children: '*[*]',
      document_base_url: AdminConfig.get("app_url") + "/",
      allow_script_urls: true,
      convert_urls: false,
      relative_urls: false,
      remove_script_host: false,
      content_css: ['//fonts.googleapis.com/css?family=Lato:300,300i,400,400i'],
      setup: function setup(editor) {
        editor.ui.registry.addButton('customGallery', {
          text: 'Insert Image',
          onAction: function onAction(_) {
            var _Vue, _Vue$$refs;
            if ((_Vue = Vue) !== null && _Vue !== void 0 && (_Vue$$refs = _Vue.$refs) !== null && _Vue$$refs !== void 0 && _Vue$$refs.imageGallery) {
              Vue.$refs.imageGallery.open(editor);
            }
          }
        });
      }
    };
    defaultSettings = _objectSpread(_objectSpread({}, defaultSettings), settings);
    selector.editor = tinymce.init(defaultSettings);
  };
  return {
    makeRichEditor: makeRichEditor
  };
}();
window.EditorHelper = EditorHelper;

/** Page Manager **/
var PageManager = {
  action: null,
  content_type: null,
  id: null,
  init: function init(action, content_type, id) {
    this.action = action;
    this.content_type = content_type;
    this.id = id;
    if (PageManager.action === "add") {
      document.getElementById("lang_name").addEventListener("change", PageManager.autoUpdateFields);
      document.getElementById("lang_title").addEventListener("change", PageManager.autoUpdateUrls);
      document.getElementById("link_rewrite").addEventListener("keyup", PageManager.linkRewriteUpdated);
      document.getElementById("category_id").addEventListener("change", PageManager.getParentCategory);
    } else {
      this.getParentCategory();
    }
  },
  isBlank: function isBlank(elem) {
    return document.getElementById(elem).value.replace(/\s/g, "") === "";
  },
  cleanForUrl: function cleanForUrl(str) {
    var replaceWith = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "-";
    return str.replace(/\s|'/g, replaceWith);
  },
  autoUpdateFields: function autoUpdateFields() {
    var value = this.value;
    try {
      if (PageManager.isBlank("lang_title")) {
        document.getElementById("lang_title").value = value[0].toUpperCase() + value.slice(1);
        document.getElementById("alias").value = PageManager.cleanForUrl(value.toUpperCase(), "_");
        var active_key = PageManager.cleanForUrl(value.toLowerCase(), "-");
        document.getElementById("lang_active_key").value = active_key;
        document.getElementById("link_rewrite").value = active_key;
      }
    } catch (e) {
      console.error(e.message);
    }
  },
  autoUpdateUrls: function autoUpdateUrls() {
    var value = this.value;
    if (document.getElementById("link_rewrite").edited !== true) {
      value = PageManager.cleanForUrl(value.toUpperCase(), "_");
      value = value.substr(0, 60); //it's limit
      document.getElementById("alias").value = value;
      var active_key = PageManager.cleanForUrl(value.toLowerCase(), "-");
      active_key = value.substr(0, 128); //it's limit
      document.getElementById("lang_active_key").value = active_key;
      document.getElementById("link_rewrite").value = active_key;
    }
  },
  linkRewriteUpdated: function linkRewriteUpdated() {
    document.getElementById("link_rewrite").edited = true;
  },
  getParentCategory: function getParentCategory() {
    var parentcombo = document.getElementById("parent_id");
    var category_id = document.getElementById("category_id").value;
    document.getElementById("parent_id").value = "";
    parentcombo.length = 0;
    parentcombo.options[0] = new Option("Select", "");
    if (category_id > 0) {
      showHideBlock(true);
      var path = AdminConfig.admin_path("page/getParentCategory", {
        content_type: PageManager.content_type,
        category_id: category_id
      });
      axios.get(path).then(function (res) {
        //console.log(res);
        updateCombo(res.data);
      })["catch"](function (res) {
        //console.log(res);
        showHideBlock(false);
      });
    } else {
      showHideBlock(false);
    }
    function updateCombo(res) {
      if (res.length > 0) {
        var index = 1;
        for (var i = 0; i < res.length; i++) {
          var current = res[i];
          if (current.id !== PageManager.id) {
            parentcombo.options[index] = new Option(current.lang.name, current.id);
            index++;
          }
        }
      } else {
        showHideBlock(false);
      }
    }
    function showHideBlock(show) {
      document.getElementById("parent_div").style.display = show === true ? "" : "none";
    }
  }
};
window.PageManager = PageManager;
/******/ })()
;