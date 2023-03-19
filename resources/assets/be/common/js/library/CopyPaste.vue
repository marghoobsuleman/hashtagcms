<template>
    <button v-if="showCopy" aria-label="Copy" class="btn btn-outline-secondary" title="Copy" type="button" @click="copyNow()"><i
        aria-hidden="true" class="fa fa-copy"></i></button>
    <button v-if="showPaste"  aria-label="Paste" class="btn btn-outline-secondary" title="Paste" type="button" @click="pasteNow()"><i
        aria-hidden="true" class="fa fa-paste"></i></button>
</template>

<script>
import {CopyToClipboard, PasteFromClipboard, Toast, IsJson} from '../helpers/common';
import {EventBus} from "../helpers/event-bus";
export default {
    props: [
        'dataIgnoreFields',
        'dataPasteElement',
        'dataForm',
        'dataBackUrl',
        'dataCopyPasteAutoInit',
        'dataShowCopy',
        'dataShowPaste'
    ],
    mounted() {
        if (this.autoInit) {
            this.init();
        }
    },
    created() {


    },
    data() {
        return {
            ignoreFields: (typeof this.dataIgnoreFields === "undefined") ? ["id", "site_id", "backURL", "actionPerformed", "platform_id", "_token", "insert_by", "update_by"] : JSON.parse(this.dataIgnoreFields),
            copyKey: "cms_copy_data",
            backURL: (typeof this.dataBackUrl === "undefined" || this.dataBackUrl === "") ? "" : this.dataBackUrl,
            autoInit:(this.dataCopyPasteAutoInit === undefined || this.dataCopyPasteAutoInit?.toString() === "true"),
            showCopy:(this.dataShowCopy === undefined || this.dataShowCopy.toString() === "true"),
            showPaste:(this.dataShowPaste === undefined || this.dataShowPaste.toString() === "true")
        }
    },
    computed: {
        form() {

            let holder;
            if (typeof this.dataForm !== "undefined" && document.getElementById(this.dataForm)) {
                holder = document.getElementById(this.dataForm);
            } else {
                holder = document;
            }
            return holder;
        },
        pasteElement() {
            let holder;
            if (typeof this.dataPasteElement === "undefined") {
                holder = this.form.querySelectorAll("input[type='text']")[0];
            } else {
                holder = this.form.getElementById(this.dataPasteElement);
            }
            //console.log("holder", holder);
            return holder;
        }
    },
    methods: {

        init() {
            let $this = this;
            if (this.pasteElement) {
                this.pasteElement.addEventListener("blur", function () {
                    if (IsJson(this.value)) {
                        $this.pasteNow();
                    }
                });
            }

        },
        copyNow() {

            let $this = this;

            let inputs = this.form.querySelectorAll("input");
            let textAreas = this.form.querySelectorAll("textarea");
            let selects = this.form.querySelectorAll("select");

            let allElements = [...inputs, ...textAreas, ...selects];

            let store = {};
            allElements.forEach(function (current) {
                let name = current.getAttribute("name") || current.getAttribute("id");

                if (name != null && typeof name !== "object" && shouldIgnore(name) === -1) {
                    store[name] = (current.type === "checkbox" || current.type === "radio") ? current.checked : current.value;
                }
            });

            let data = JSON.stringify(store);
            CopyToClipboard(data);

            EventBus.emit('on-copy', data);
            Toast.show(this, "Copied...", 1000);

            function shouldIgnore(name) {
                return $this.ignoreFields.findIndex(function (nm) {
                    return name === nm;
                });
            }

        },
        fillData: function (data) {
            let form = this.form;
            if (data) {
                for (let i in data) {
                    try {
                        let ele = form[i] || document.getElementById(i);
                        let val = data[i];
                        if(ele.type === "checkbox" || ele.type === "radio") {
                            ele.checked = val;
                        }  else {
                            ele.value = val;
                        }
                    } catch (e) {
                        console.info("unable to set value @ " + i);
                    }
                }
            }
        },
        pasteNow(cb) {
            PasteFromClipboard().then((res)=> {
                if (IsJson(res)) {
                    this.fillData(JSON.parse(res));
                }
                if (cb) {
                    cb(JSON.parse(res));
                }
                EventBus.emit('on-paste', res);
            }).catch(res=> {
                console.log("unable to paste")
            });

        }
    }
}

</script>
