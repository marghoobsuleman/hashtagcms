(()=>{function e(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(e);t&&(i=i.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,i)}return n}function t(t){for(var i=1;i<arguments.length;i++){var a=null!=arguments[i]?arguments[i]:{};i%2?e(Object(a),!0).forEach((function(e){n(t,e,a[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(a)):e(Object(a)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(a,e))}))}return t}function n(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var i={makeRichEditor:function(e,n){var i={selector:e,height:500,theme:"silver",plugins:"code print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount imagetools textpattern",toolbar1:"formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist | removeformat | code",image_advtab:!0,template_popup_height:400,template_popup_width:320,valid_elements:"*[*]",valid_children:"*[*]",allow_script_urls:!0,convert_urls:!1,relative_urls:!1,remove_script_host:!1,content_css:["//fonts.googleapis.com/css?family=Lato:300,300i,400,400i"],setup:function(e){e.ui.registry.addButton("customInsertButton",{text:"Insert Image",icon:"image",onAction:function(e){}})}};i=t(t({},i),n),tinymce.init(i)}};window.EditorHelper=i;var a={action:null,content_type:null,id:null,init:function(e,t,n){this.action=e,this.content_type=t,this.id=n,"add"==a.action?(document.getElementById("lang_name").addEventListener("change",a.autoUpdateFields),document.getElementById("lang_title").addEventListener("change",a.autoUpdateUrls),document.getElementById("link_rewrite").addEventListener("keyup",a.linkRewriteUpdated),document.getElementById("category_id").addEventListener("change",a.getParentCategory)):this.getParentCategory()},isBlank:function(e){return""===document.getElementById(e).value.replace(/\s/g,"")},autoUpdateFields:function(){var e=this.value;if(a.isBlank("lang_title")){document.getElementById("lang_title").value=e[0].toUpperCase()+e.slice(1),document.getElementById("alias").value=e.toUpperCase().replace(/\s/g,"_");var t=e.toLowerCase().replace(/\s/g,"-");document.getElementById("lang_active_key").value=t,document.getElementById("link_rewrite").value=t}},autoUpdateUrls:function(){var e=this.value;if(1!=document.getElementById("link_rewrite").edited){e=(e=e.toUpperCase().replace(/\s/g,"_")).substr(0,60),document.getElementById("alias").value=e;var t=e.toLowerCase().replace(/\s/g,"-");t=e.substr(0,128),document.getElementById("lang_active_key").value=t,document.getElementById("link_rewrite").value=t}},linkRewriteUpdated:function(){document.getElementById("link_rewrite").edited=!0},getParentCategory:function(){var e=document.getElementById("parent_id"),t=document.getElementById("category_id").value;if(document.getElementById("parent_id").value="",e.length=0,e.options[0]=new Option("Select",""),t>0){i(!0);var n=AdminConfig.admin_path("page/getParentCategory",{content_type:a.content_type,category_id:t});axios.get(n).then((function(t){!function(t){if(t.length>0)for(var n=1,r=0;r<t.length;r++){var o=t[r];o.id!=a.id&&(e.options[n]=new Option(o.lang.name,o.id),n++)}else i(!1)}(t.data)})).catch((function(e){i(!1)}))}else i(!1);function i(e){document.getElementById("parent_div").style.display=1==e?"":"none"}}};window.PageManager=a})();