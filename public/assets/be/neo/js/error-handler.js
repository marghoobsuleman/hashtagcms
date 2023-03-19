/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/assets/be/common-old/js/helpers/Common.js":
/*!*************************************************************!*\
  !*** ./resources/assets/be/common-old/js/helpers/Common.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Fetcher": () => (/* binding */ Fetcher),
/* harmony export */   "LeftMenu": () => (/* binding */ LeftMenu),
/* harmony export */   "Loader": () => (/* binding */ Loader),
/* harmony export */   "Modal": () => (/* binding */ Modal),
/* harmony export */   "Storage": () => (/* binding */ Storage),
/* harmony export */   "Toast": () => (/* binding */ Toast),
/* harmony export */   "Utils": () => (/* binding */ Utils),
/* harmony export */   "queryBuilder": () => (/* binding */ queryBuilder)
/* harmony export */ });
/* harmony import */ var _Fx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Fx */ "./resources/assets/be/common-old/js/helpers/Fx.js");
/* harmony import */ var _event_bus__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./event-bus */ "./resources/assets/be/common-old/js/helpers/event-bus.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
/**
 * Created by marghoob.suleman on 8/23/17.
 */




/**
 * Toast
 * Dependecy: library/toastBox.vue
 *
 */
var Toast = /*#__PURE__*/function () {
  function Toast() {
    _classCallCheck(this, Toast);
  }
  _createClass(Toast, null, [{
    key: "show",
    value: function show(vm) {
      var message = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "";
      var timeout = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 1000;
      var position = arguments.length > 3 ? arguments[3] : undefined;
      vm.$root.$refs.globalToaster.show(message, timeout, position);
    }
  }, {
    key: "hide",
    value: function hide(vm) {
      vm.$root.$refs.globalToaster.hide();
    }
  }]);
  return Toast;
}();

/**
 * Loader
 * Dependecy: library/loader.vue
 *
 */
var Loader = /*#__PURE__*/function () {
  function Loader() {
    _classCallCheck(this, Loader);
  }
  _createClass(Loader, null, [{
    key: "show",
    value: function show(vm) {
      var message = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var position = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      vm.$root.$refs.globalLoader.show(message, position);
    }
  }, {
    key: "hide",
    value: function hide(vm) {
      vm.$root.$refs.globalLoader.hide();
    }
  }]);
  return Loader;
}();

/**
 * Modal
 * Dependecy: library/modalBox.vue
 *
 */
var Modal = /*#__PURE__*/function () {
  function Modal() {
    _classCallCheck(this, Modal);
  }
  _createClass(Modal, null, [{
    key: "show",
    value: function show(vm) {
      var message = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "";
      var position = arguments.length > 2 ? arguments[2] : undefined;
      var callback = arguments.length > 3 ? arguments[3] : undefined;
      var timeout = arguments.length > 4 ? arguments[4] : undefined;
      vm.$root.$refs.globalModalBox.show(message, position, callback, timeout);
    }
  }, {
    key: "hide",
    value: function hide(vm) {
      vm.$root.$refs.globalModalBox.hide();
    }
  }, {
    key: "open",
    value: function open() {
      var vm = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var modelref = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      if (vm != null && modelref != null) {
        vm.$refs[modelref].open(callback);
      }
    }
  }, {
    key: "close",
    value: function close() {
      var vm = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var modelref = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      if (vm != null && modelref != null) {
        vm.$refs[modelref].hide();
      }
    }
  }]);
  return Modal;
}();

/**
 * queryBuilder
 * Get Param from query
 *
 */
var queryBuilder = /*#__PURE__*/function () {
  function queryBuilder() {
    _classCallCheck(this, queryBuilder);
  }
  _createClass(queryBuilder, null, [{
    key: "cache",
    get: function get() {
      return {};
    }
  }, {
    key: "get",
    value: function get(param, custom) {
      if (this.cache[param]) {
        return this.cache[param];
      }
      ;
      var query = typeof custom == "undefined" ? window.location.search.substring(1) : custom;
      var query_arr = query.split("&");
      var all = {};
      for (var i = 0; i < query_arr.length; i++) {
        var current = query_arr[i].split("=");
        var key = current[0];
        var value = current[1];
        if (current.length > 2) {
          current.shift();
          value = current.join("=");
        }
        ;
        all[key] = value;
        if (key == param) {
          var val = decodeURIComponent(value);
          this.cache[param] = val;
          return val;
        }
      }
      return param == null ? all : "";
    }
  }, {
    key: "all",
    value: function all(custom) {
      return this.get(null, custom);
    }
  }]);
  return queryBuilder;
}();

/**
 * Shared class
 */
var Storage = /*#__PURE__*/function () {
  function Storage() {
    _classCallCheck(this, Storage);
    this.obj = {};
    this.counter = 1;
  }
  _createClass(Storage, [{
    key: "nextCounter",
    value: function nextCounter() {
      return this.counter++;
    }
  }, {
    key: "store",
    value: function store(name, value) {
      this.obj[name] = value;
    }
  }, {
    key: "fetch",
    value: function fetch(name) {
      return this.obj[name];
    }
  }, {
    key: "clear",
    value: function clear(name) {
      return delete this.obj[name];
    }
  }]);
  return Storage;
}();
var Utils = /*#__PURE__*/function () {
  function Utils() {
    _classCallCheck(this, Utils);
  }
  _createClass(Utils, [{
    key: "serializeFormArray",
    value: function serializeFormArray(form) {
      form = typeof form == 'string' ? document.getElementById(form) : form;
      var field,
        l,
        s = [];
      if (_typeof(form) == 'object' && form.nodeName == "FORM") {
        var len = form.elements.length;
        for (var i = 0; i < len; i++) {
          field = form.elements[i];
          if (field.name && !field.disabled && field.type != 'file' && field.type != 'reset' && field.type != 'submit' && field.type != 'button') {
            if (field.type == 'select-multiple') {
              l = form.elements[i].options.length;
              for (j = 0; j < l; j++) {
                if (field.options[j].selected) s[s.length] = {
                  name: field.name,
                  value: field.options[j].value
                };
              }
            } else if (field.type != 'checkbox' && field.type != 'radio' || field.checked) {
              s[s.length] = {
                name: field.name,
                value: field.value
              };
            }
          }
        }
      }
      return s;
    }
  }]);
  return Utils;
}();
var Fetcher = /*#__PURE__*/function () {
  function Fetcher() {
    _classCallCheck(this, Fetcher);
  }
  _createClass(Fetcher, [{
    key: "get",
    value: function get(url) {
      return axios.get(url);
    }
  }]);
  return Fetcher;
}();
var LeftMenu = /*#__PURE__*/function () {
  function LeftMenu() {
    _classCallCheck(this, LeftMenu);
    this.visible = true;
    this.oldWidth = null;
  }
  _createClass(LeftMenu, null, [{
    key: "init",
    value: function init() {
      var leftElem = document.getElementById("mainLeftContent");
      var display = leftElem.style.display;
      if (display == "" || display == "inline-block") {
        _event_bus__WEBPACK_IMPORTED_MODULE_1__.EventBus.$emit('left-menu-on-show');
        this.visible = true;
      } else {
        _event_bus__WEBPACK_IMPORTED_MODULE_1__.EventBus.$emit('left-menu-on-hide');
        this.visible = false;
      }
    }
  }, {
    key: "isVisible",
    value: function isVisible() {
      return this.visible;
    }
  }, {
    key: "toggleShow",
    value: function toggleShow(show) {
      var css = ["hidden-md", "hidden-xs"];
      var leftElem = document.getElementById("mainLeftContent");
      var rightElem = document.getElementById("mainRightContent");
      var display = leftElem.style.display;
      var width = leftElem.clientWidth;

      //if it's visible then hide it.
      if ((display == "" || display == "inline-block") && width !== 0) {
        this.visible = false;
        this.oldWidth = leftElem.style.width;
        _Fx__WEBPACK_IMPORTED_MODULE_0__.FX.slideLeft("mainLeftContent", function () {
          var _leftElem$classList;
          (_leftElem$classList = leftElem.classList).remove.apply(_leftElem$classList, css);
          //set right side 100%
          rightElem.classList.remove("col-md-10");
          rightElem.classList.add("col-md-12");
        });
        _event_bus__WEBPACK_IMPORTED_MODULE_1__.EventBus.$emit('left-menu-on-hide');
      } else {
        var _leftElem$classList2;
        this.visible = true;
        rightElem.classList.remove("col-md-12");

        //back to normal size of right side
        rightElem.classList.add("col-md-10");
        leftElem.style.display = "inline-block";
        //leftElem.style.width = "16.66666667%";
        (_leftElem$classList2 = leftElem.classList).remove.apply(_leftElem$classList2, css);
        if (this.oldWidth != null) {
          leftElem.style.width = this.oldWidth;
        }
        _event_bus__WEBPACK_IMPORTED_MODULE_1__.EventBus.$emit('left-menu-on-show');
      }
    }
  }]);
  return LeftMenu;
}();

/***/ }),

/***/ "./resources/assets/be/common-old/js/helpers/ErrorMessageHandler.js":
/*!**************************************************************************!*\
  !*** ./resources/assets/be/common-old/js/helpers/ErrorMessageHandler.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "ErrorMessage": () => (/* binding */ ErrorMessage)
/* harmony export */ });
/* harmony import */ var _Common__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Common */ "./resources/assets/be/common-old/js/helpers/Common.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }

var ErrorMessage = /*#__PURE__*/function () {
  function ErrorMessage() {
    _classCallCheck(this, ErrorMessage);
    this.errors = typeof error_messages == "undefined" ? [] : error_messages;
    this.init();
  }
  _createClass(ErrorMessage, [{
    key: "init",
    value: function init() {
      var $this = this;
      document.addEventListener("DOMContentLoaded", function () {
        for (var i in $this.errors) {
          $this.highlightField(i, $this.errors[i][0]);
        }
        $this.focusFirst();
      });
    }
  }, {
    key: "highlightField",
    value: function highlightField(field, message) {
      var f = field;
      field = document.getElementById(f);
      if (!field) {
        console.error("Could not find " + f);
        return false;
      }
      field.classList.add("is-invalid");
      var div = document.createElement("div");
      div.classList.add("alert", "alert-danger", "mt-1");
      if (f.indexOf("lang_") != -1) {
        message = message.replace("lang ", "");
      }
      div.innerHTML = message;
      field.parentNode.insertBefore(div, field.nextSibling);
    }
  }, {
    key: "focusFirst",
    value: function focusFirst() {
      for (var i in this.errors) {
        var element = document.getElementById(i);
        try {
          if (element) {
            element.focus();
            break;
          }
        } catch (e) {
          console.log(e.message);
        }
      }
    }
  }]);
  return ErrorMessage;
}();

/***/ }),

/***/ "./resources/assets/be/common-old/js/helpers/Fx.js":
/*!*********************************************************!*\
  !*** ./resources/assets/be/common-old/js/helpers/Fx.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "FX": () => (/* binding */ FX)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
/** Effects
 * dependency: animcss
 */
var FX = /*#__PURE__*/function () {
  function FX() {
    _classCallCheck(this, FX);
  }
  _createClass(FX, null, [{
    key: "come",
    value: function come(div, cb) {
      div = typeof div == "string" ? document.getElementById(div) : div;
      Velocity(div, "fadeIn").then(function () {
        if (cb) {
          cb.apply(this, arguments);
        }
        ;
      });
    }
  }, {
    key: "out",
    value: function out(div, cb) {
      div = typeof div == "string" ? document.getElementById(div) : div;
      Velocity(div, "fadeOut").then(function () {
        if (cb) {
          cb.apply(this, arguments);
        }
        ;
      });
    }
  }, {
    key: "highlight",
    value: function highlight(div, cb) {
      div = typeof div == "string" ? document.getElementById(div) : div;
      var backgroundColor = div.style.backgroundColor == "" ? "#fff" : div.style.backgroundColor;
      Velocity(div, {
        opacity: 0,
        backgroundColor: "#dcdc2a"
      }, {
        duration: 500
      }).then(function () {
        Velocity(div, {
          opacity: 1,
          backgroundColor: backgroundColor
        }, {
          duration: 500
        }).then(function () {
          if (cb) {
            cb.apply(this, arguments);
          }
        });
      });
    }
  }, {
    key: "slideDown",
    value: function slideDown(div, cb) {
      div = typeof div == "string" ? document.getElementById(div) : div;
      var oldDisplay = div.style.display;
      if (oldDisplay == "none") {
        Velocity(div, "slideDown").then(function (arg) {
          div.style.display = oldDisplay == "none" ? "" : oldDisplay;
          if (cb) {
            cb.apply(this, arguments);
          }
          ;
        });
      }
    }
  }, {
    key: "slideUp",
    value: function slideUp(div, cb) {
      div = typeof div == "string" ? document.getElementById(div) : div;
      Velocity(div, "slideUp").then(function (arg) {
        if (cb) {
          cb.apply(this, arguments);
        }
      });
    }
  }, {
    key: "toggleSlide",
    value: function toggleSlide(div, cb) {
      div = typeof div == "string" ? document.getElementById(div) : div;
      var display = div.style.display;
      if (display == "") {
        this.slideUp(div, cb);
      } else {
        this.slideDown(div, cb);
      }
    }
  }, {
    key: "scrollWinTo",
    value: function scrollWinTo(div, cb) {
      if (document.getElementById(div)) {
        Velocity(document.getElementById(div), "scroll", {
          duration: 500,
          complete: function complete() {
            if (cb) {
              cb.apply(this, arguments);
            }
          }
        });
      }
    }
  }, {
    key: "slideLeft",
    value: function slideLeft(div, cb) {
      div = typeof div == "string" ? document.getElementById(div) : div;
      var old = div.getBoundingClientRect();
      div.oldProp = old;
      Velocity(div, {
        width: 0
      }).then(function () {
        if (cb) {
          cb.apply(this, arguments);
        }
        ;
        div.style.display = "none";
      });
    }
  }, {
    key: "slideRight",
    value: function slideRight(div, cb) {
      div = typeof div == "string" ? document.getElementById(div) : div;
      var width = div.oldProp ? div.oldProp.width : div.getBoundingClientRect().width;
      Velocity(div, {
        width: width
      }).then(function () {
        if (cb) {
          cb.apply(this, arguments);
        }
        ;
      });
    }
  }]);
  return FX;
}();

/***/ }),

/***/ "./resources/assets/be/common-old/js/helpers/event-bus.js":
/*!****************************************************************!*\
  !*** ./resources/assets/be/common-old/js/helpers/event-bus.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "EventBus": () => (/* binding */ EventBus)
/* harmony export */ });
//import Vue from 'vue';
//export const EventBus = new Vue();

var EventBus = {};

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!*****************************************************!*\
  !*** ./resources/assets/be/neo/js/error-handler.js ***!
  \*****************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _common_old_js_helpers_ErrorMessageHandler_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../common-old/js/helpers/ErrorMessageHandler.js */ "./resources/assets/be/common-old/js/helpers/ErrorMessageHandler.js");

new _common_old_js_helpers_ErrorMessageHandler_js__WEBPACK_IMPORTED_MODULE_0__.ErrorMessage();
})();

/******/ })()
;