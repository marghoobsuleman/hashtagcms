(()=>{function r(r){return function(r){if(Array.isArray(r))return t(r)}(r)||function(r){if("undefined"!=typeof Symbol&&null!=r[Symbol.iterator]||null!=r["@@iterator"])return Array.from(r)}(r)||function(r,a){if(r){if("string"==typeof r)return t(r,a);var e={}.toString.call(r).slice(8,-1);return"Object"===e&&r.constructor&&(e=r.constructor.name),"Map"===e||"Set"===e?Array.from(r):"Arguments"===e||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(e)?t(r,a):void 0}}(r)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function t(r,t){(null==t||t>r.length)&&(t=r.length);for(var a=0,e=Array(t);a<t;a++)e[a]=r[a];return e}window.Dashboard={data:[],labelsCategories:[],datasCategories:[],labelsContent:[],datasContent:[],bgColors:["rgba(93, 128, 96, 0.7)","rgba(135, 188, 118, 0.7)","rgba(146, 222, 184, 0.7)","rgba(135, 131, 222, 0.7)","rgba(128, 57, 5, 0.7)","rgba(231, 191, 200, 0.7)","rgba(255, 99, 132, 0.7)","rgba(54, 162, 235, 0.7)","rgba(255, 206, 86, 0.7)","rgba(75, 192, 192, 0.7)","rgba(153, 102, 255, 0.7)","rgba(255, 159, 64, 0.7)"],borderColors:["rgba(82, 117, 85, 1)","rgba(115, 172, 96, 1)","rgba(113, 204, 158, 1)","rgba(103, 99, 206, 1)","rgba(112, 51, 7, 1)","rgba(213, 158, 170, 1)","rgba(255, 99, 132, 1)","rgba(54, 162, 235, 1)","rgba(255, 206, 86, 1)","rgba(75, 192, 192, 1)","rgba(153, 102, 255, 1)","rgba(255, 159, 64, 1)"],createChart:function(r,t,a,e,o){var n=document.getElementById(r);new Chart(n,{type:"bar",data:{labels:t,datasets:[{label:"# of Reads",data:a,backgroundColor:e,borderColor:o,borderWidth:1}]},options:{scales:{y:{beginAtZero:!0}}}})},makeData:function(){for(var r=0,t=this.data.categories.length;r<t;r++)this.labelsCategories.push(this.data.categories[r].link_rewrite),this.datasCategories.push(this.data.categories[r].read_count)},init:function(t){this.data=t,this.makeData(),this.createChart("topCatgories",this.labelsCategories,this.datasCategories,this.bgColors,this.borderColors),this.createChart("topContents",this.labelsContent,this.datasContent,r(this.bgColors).reverse(),r(this.borderColors).reverse())}}})();