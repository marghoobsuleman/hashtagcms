let EditorHelper =(function() {

    let makeRichEditor = (selector, settings)=> {
        let defaultSettings = {
            selector: selector,
            height: 500,
            theme: 'silver',
            plugins: 'code print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount imagetools textpattern',
            toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist | removeformat | code',
            image_advtab: true,
            template_popup_height: 400,
            template_popup_width: 320,
            valid_elements : '*[*]',
            valid_children:'*[*]',
            content_css: [
                '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i'
            ],
            setup: function (editor) {
                //customInsertButton |
                editor.ui.registry.addButton('customInsertButton', {
                    text: 'Insert Image',
                    icon: 'image',
                    onAction: function (_) {
                        //tinymce.activeEditor.execCommand('mceInsertContent', false, '<p>My ' + pet +'\'s name is: <strong>' + data.catdata + '</strong></p>');
                        //editor.insertContent('&nbsp;<strong>It\'s my button!</strong>&nbsp;');
                    }
                });

            }
        };
        defaultSettings = {...defaultSettings, ...settings};
        let editor = tinymce.init(defaultSettings);
    };

    return {makeRichEditor:makeRichEditor};

})();

window.EditorHelper = EditorHelper;


/** Page Manager **/
var PageManager = {
    action:null,
    content_type: null,
    id: null,
    init: function (action, content_type, id) {
        this.action = action;
        this.content_type = content_type;
        this.id = id;

        if (PageManager.action == "add") {
            document.getElementById("lang_name").addEventListener("change", PageManager.autoUpdateFields);
            document.getElementById("lang_title").addEventListener("change", PageManager.autoUpdateUrls);
            document.getElementById("link_rewrite").addEventListener("keyup", PageManager.linkRewriteUpdated);
            document.getElementById("category_id").addEventListener("change", PageManager.getParentCategory);
        } else {
            this.getParentCategory();
        }
    },
    isBlank: function (elem) {
        return (document.getElementById(elem).value.replace(/\s/g, "") === "");
    },
    autoUpdateFields: function () {
        let value = this.value;
        if (PageManager.isBlank("lang_title")) {
            document.getElementById("lang_title").value = value[0].toUpperCase() + value.slice(1);
            document.getElementById("alias").value = value.toUpperCase().replace(/\s/g, "_");
            let active_key = value.toLowerCase().replace(/\s/g, "-");
            document.getElementById("lang_active_key").value = active_key;
            document.getElementById("link_rewrite").value = active_key;
        }
    },
    autoUpdateUrls: function () {
        let value = this.value;
        if (document.getElementById("link_rewrite").edited != true) {
            value = value.toUpperCase().replace(/\s/g, "_");
            value = value.substr(0, 60); //it's limit
            document.getElementById("alias").value = value;

            let active_key = value.toLowerCase().replace(/\s/g, "-");
            active_key = value.substr(0, 128); //it's limit
            document.getElementById("lang_active_key").value = active_key;
            document.getElementById("link_rewrite").value = active_key;
        }

    },
    linkRewriteUpdated: function () {
        document.getElementById("link_rewrite").edited = true;
    },
    getParentCategory() {
        let parentcombo = document.getElementById("parent_id");
        let category_id = document.getElementById("category_id").value;
        document.getElementById("parent_id").value = "";

        parentcombo.length = 0;
        parentcombo.options[0] = new Option("Select", "");

        if (category_id > 0) {
            showHideBlock(true);
            let path = AdminConfig.admin_path("page/getParentCategory", {
                content_type: PageManager.content_type,
                category_id: category_id
            });
            axios.get(path).then(function (res) {
                //console.log(res);
                updateCombo(res.data);
            }).catch(function (res) {
                //console.log(res);
                showHideBlock(false);
            });
        } else {
            showHideBlock(false);
        }

        function updateCombo(res) {
            if (res.length > 0) {
                let index = 1;
                for (let i = 0; i < res.length; i++) {
                    let current = res[i];
                    if (current.id != PageManager.id) {
                        parentcombo.options[index] = new Option(current.lang.name, current.id);
                        index++;
                    }
                }
            } else {
                showHideBlock(false);
            }
        }

        function showHideBlock(show) {
            document.getElementById("parent_div").style.display = (show == true) ? "" : "none";
        }
    }
};
window.PageManager = PageManager;