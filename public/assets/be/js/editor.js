(()=>{function e(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(e);t&&(i=i.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,i)}return n}function t(t){for(var i=1;i<arguments.length;i++){var r=null!=arguments[i]?arguments[i]:{};i%2?e(Object(r),!0).forEach((function(e){n(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):e(Object(r)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}function n(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var i={makeRichEditor:function(e,n){var i={selector:e,height:500,theme:"silver",plugins:"code print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount imagetools textpattern",toolbar1:"formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist | removeformat | code",image_advtab:!0,template_popup_height:400,template_popup_width:320,valid_elements:"*[*]",valid_children:"*[*]",document_base_url:AdminConfig.get("app_url")+"/",allow_script_urls:!0,convert_urls:!1,relative_urls:!1,remove_script_host:!1,content_css:["//fonts.googleapis.com/css?family=Lato:300,300i,400,400i"],setup:function(e){e.ui.registry.addButton("customInsertButton",{text:"Insert Image",icon:"image",onAction:function(e){}})}};i=t(t({},i),n),e.editor=tinymce.init(i)}};window.EditorHelper=i;var r={action:null,content_type:null,id:null,init:function(e,t,n){this.action=e,this.content_type=t,this.id=n,"add"===r.action?(document.getElementById("lang_name").addEventListener("change",r.autoUpdateFields),document.getElementById("lang_title").addEventListener("change",r.autoUpdateUrls),document.getElementById("link_rewrite").addEventListener("keyup",r.linkRewriteUpdated),document.getElementById("category_id").addEventListener("change",r.getParentCategory)):this.getParentCategory()},isBlank:function(e){return""===document.getElementById(e).value.replace(/\s/g,"")},cleanForUrl:function(e){return e.replace(/\s|'/g,"_")},autoUpdateFields:function(){var e=this.value;if(r.isBlank("lang_title")){document.getElementById("lang_title").value=e[0].toUpperCase()+e.slice(1),document.getElementById("alias").value=this.cleanForUrl(e.toUpperCase(),"_");var t=this.cleanForUrl(e.toLowerCase(),"-");document.getElementById("lang_active_key").value=t,document.getElementById("link_rewrite").value=t}},autoUpdateUrls:function(){var e=this.value;if(!0!==document.getElementById("link_rewrite").edited){e=(e=this.cleanForUrl(e.toUpperCase(),"_")).substr(0,60),document.getElementById("alias").value=e;var t=this.cleanForUrl(e.toLowerCase(),"-");t=e.substr(0,128),document.getElementById("lang_active_key").value=t,document.getElementById("link_rewrite").value=t}},linkRewriteUpdated:function(){document.getElementById("link_rewrite").edited=!0},getParentCategory:function(){var e=document.getElementById("parent_id"),t=document.getElementById("category_id").value;if(document.getElementById("parent_id").value="",e.length=0,e.options[0]=new Option("Select",""),t>0){i(!0);var n=AdminConfig.admin_path("page/getParentCategory",{content_type:r.content_type,category_id:t});axios.get(n).then((function(t){!function(t){if(t.length>0)for(var n=1,a=0;a<t.length;a++){var o=t[a];o.id!==r.id&&(e.options[n]=new Option(o.lang.name,o.id),n++)}else i(!1)}(t.data)})).catch((function(e){i(!1)}))}else i(!1);function i(e){document.getElementById("parent_div").style.display=!0===e?"":"none"}}};window.PageManager=r})();