/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/be/js/helpers/Map.js":
/*!***********************************************!*\
  !*** ./resources/assets/be/js/helpers/Map.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return MapAPI; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

/**
 * Created by marghoob.suleman on 8/23/17.
 */

/**
 * MapQuest API
 * https://developer.mapquest.com
 * Dependency: Need API
 */
var MAPQUEST_KEY = "TVArtzF8QacxSVE16JufJBt5SJXVdAZQ";
var MAPQUEST_URL = "http://www.mapquestapi.com/geocoding/v1/";
var MAPQUEST_JS = "https://api.mqcdn.com/sdk/mapquest-js/v1.3.0/mapquest.js";
var MAPQUEST_CSS = "https://api.mqcdn.com/sdk/mapquest-js/v1.3.0/mapquest.css";

var MapAPI = /*#__PURE__*/function () {
  function MapAPI() {
    _classCallCheck(this, MapAPI);

    this.callback = [];
    this.isInit = false;
  }

  _createClass(MapAPI, [{
    key: "init",
    value: function init() {
      if (this.isInit == false) {
        var $this = this;
        var script = document.createElement("script");
        script.src = MAPQUEST_JS;
        var ele = document.childNodes[0].firstElementChild;
        ele.appendChild(script);
        var css = document.createElement("link");
        css.setAttribute("type", "text/css");
        css.setAttribute("rel", "stylesheet");
        css.href = MAPQUEST_CSS;
        ele.appendChild(css);
        this.isInit = true;
      }
    }
  }, {
    key: "getLatLong",
    value: function getLatLong(address) {
      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var url = MAPQUEST_URL + "address?key=" + MAPQUEST_KEY + "&location=" + address;
      return new Promise(function (resolve, reject) {
        axios.get(url).then(function (res) {
          resolve(res);

          if (callback != null && typeof callback == "function") {
            callback.apply(this, arguments);
          }

          ;
        })["catch"](function (e) {
          reject(e);

          if (callback != null && typeof callback == "function") {
            callback.apply(this, e);
          }

          ;
        });
      });
      /*function format(res) {
          let data = [];
           let countryKey = {key:"adminArea1", label:"country"};
          let stateKey = {key:"adminArea3", label:"state"};
          let cityKey = {key:"adminArea5", label:"city"};
           let results = (res.data.results.length == 0 ) ? null : res.data.results[0];
           console.log("results");
          console.log(results);
            if(results != null) {
              let providedLocation = results.providedLocation.location;
              let allLocations = results.locations;
               let keysToStore = ['displayLatLng', 'latLng', 'p    ostalCode', 'sideOfStreet', 'street'];
               allLocations.forEach(function(location, index) {
                  let locationObj = {};
                  locationObj[countryKey.label] = location[countryKey.key];
                  locationObj[stateKey.label] = location[stateKey.key];
                  locationObj[cityKey.label] = location[cityKey.key];
                   keysToStore.forEach(function(key) {
                      locationObj[key] = location[key];
                  });
                  data.push(locationObj);
              });
           }
          return data;
       }*/
    }
  }]);

  return MapAPI;
}();



/***/ }),

/***/ "./resources/assets/be/js/map.js":
/*!***************************************!*\
  !*** ./resources/assets/be/js/map.js ***!
  \***************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _helpers_Map__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./helpers/Map */ "./resources/assets/be/js/helpers/Map.js");

window.MapApi = new _helpers_Map__WEBPACK_IMPORTED_MODULE_0__["default"]();

/***/ }),

/***/ 2:
/*!*********************************************!*\
  !*** multi ./resources/assets/be/js/map.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/marghoobsuleman/www/hashtagcms-package/packages/MarghoobSuleman/HashtagCms/resources/assets/be/js/map.js */"./resources/assets/be/js/map.js");


/***/ })

/******/ });