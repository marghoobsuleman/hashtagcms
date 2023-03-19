let EditorHelper =(function() {

    let makeRichEditor = (selector, settings)=> {
        selector = document.querySelector(selector);
        let defaultSettings = {
            selector: "#"+selector.id,
            height: 500,
            theme: 'silver',
            plugins: 'code print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount imagetools textpattern',
            toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist | removeformat | customGallery | image |  code ',
            image_advtab: true,
            template_popup_height: 400,
            template_popup_width: 320,
            valid_elements : '*[*]',
            valid_children:'*[*]',
            document_base_url: AdminConfig.get("app_url")+"/",
            allow_script_urls: true,
            convert_urls: false,
            relative_urls: false,
            remove_script_host: false,
            content_css: [
                '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i'
            ],
            setup: function (editor) {
                editor.ui.registry.addButton('customGallery', {
                    text: 'Insert Image',
                    onAction: function (_) {
                        if (Vue?.$refs?.imageGallery) {
                            Vue.$refs.imageGallery.open(editor);
                        }

                    }
                });

            }
        };
        defaultSettings = {...defaultSettings, ...settings};
        selector.editor = tinymce.init(defaultSettings);

    };

    return {makeRichEditor:makeRichEditor};

})();

window.EditorHelper = EditorHelper;


/** Page Manager **/
let PageManager = {
    action:null,
    content_type: null,
    id: null,
    init: function (action, content_type, id) {
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
    isBlank: function (elem) {
        return (document.getElementById(elem).value.replace(/\s/g, "") === "");
    },
    cleanForUrl: function(str, replaceWith="-") {
        return str.replace(/\s|'/g, replaceWith);
    },
    autoUpdateFields: function () {
        let value = this.value;
        try {
            if (PageManager.isBlank("lang_title")) {
                document.getElementById("lang_title").value = value[0].toUpperCase() + value.slice(1);
                document.getElementById("alias").value = PageManager.cleanForUrl(value.toUpperCase(), "_");
                let active_key = PageManager.cleanForUrl(value.toLowerCase(), "-");
                document.getElementById("lang_active_key").value = active_key;
                document.getElementById("link_rewrite").value = active_key;
            }
        } catch (e) {
            console.error(e.message);
        }

    },
    autoUpdateUrls: function () {
        let value = this.value;
        if (document.getElementById("link_rewrite").edited !== true) {
            value = PageManager.cleanForUrl(value.toUpperCase(), "_");
            value = value.substr(0, 60); //it's limit
            document.getElementById("alias").value = value;

            let active_key = PageManager.cleanForUrl(value.toLowerCase(), "-");
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
            document.getElementById("parent_div").style.display = (show === true) ? "" : "none";
        }
    }
};
window.PageManager = PageManager;
