/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*************************************************!*\
  !*** ./resources/assets/be/neo/js/dashboard.js ***!
  \*************************************************/
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
window.Dashboard = {
  data: [],
  labelsCategories: [],
  datasCategories: [],
  labelsContent: [],
  datasContent: [],
  bgColors: ['rgba(93, 128, 96, 0.7)', 'rgba(135, 188, 118, 0.7)', 'rgba(146, 222, 184, 0.7)', 'rgba(135, 131, 222, 0.7)', 'rgba(128, 57, 5, 0.7)', 'rgba(231, 191, 200, 0.7)', 'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)'],
  borderColors: ['rgba(82, 117, 85, 1)', 'rgba(115, 172, 96, 1)', 'rgba(113, 204, 158, 1)', 'rgba(103, 99, 206, 1)', 'rgba(112, 51, 7, 1)', 'rgba(213, 158, 170, 1)', 'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'],
  createChart: function createChart(id, labels, datas, bgColors, boderColors) {
    //console.log(id, labels, datas);
    var ctx = document.getElementById(id);
    //console.log(ctx);
    var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: '# of Reads',
          data: datas,
          backgroundColor: bgColors,
          borderColor: boderColors,
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  },
  makeData: function makeData() {
    for (var i = 0, len = this.data.categories.length; i < len; i++) {
      this.labelsCategories.push(this.data.categories[i].link_rewrite);
      this.datasCategories.push(this.data.categories[i].read_count);
    }
  },
  init: function init(data) {
    this.data = data;
    this.makeData();
    this.createChart('topCatgories', this.labelsCategories, this.datasCategories, this.bgColors, this.borderColors);
    this.createChart('topContents', this.labelsContent, this.datasContent, _toConsumableArray(this.bgColors).reverse(), _toConsumableArray(this.borderColors).reverse());
  }
};
/******/ })()
;