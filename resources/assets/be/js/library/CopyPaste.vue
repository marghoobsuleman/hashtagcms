<template>
    <div class="inline">
        <div class="btn-group">
            <button @click="copyNow()" title="Copy" type="button" aria-label="Copy" class="btn btn-default"><i aria-hidden="true" class="fa fa-copy"></i></button>
            <button @click="pasteNow()" title="Paste" type="button" aria-label="Paste" class="btn btn-default"><i aria-hidden="true" class="fa fa-paste"></i></button>
            <a v-if="backURL != '' " :href="backURL" class="btn btn-default">Back</a>
        </div>
    </div>
</template>

<script>

    export default {
        props: [
            'dataIgnoreFields',
            'dataPasteElement',
            'dataForm',
            'dataBackUrl'
        ],
        mounted() {
            //this.init();

        },
        created() {
            let $this = this;
            window.addEventListener("load", function () {
                $this.init();
            });
        },
        computed: {
            form() {

              let holder;
              if(typeof this.dataForm !== "undefined" && document.getElementById(this.dataForm)) {
                  holder = document.getElementById(this.dataForm);
              } else {
                  holder = document;
              }
              return holder;
            },
            pasteElement() {
                let holder;
                if(typeof this.dataPasteElement === "undefined") {
                    holder = this.form.querySelectorAll("input[type='text']")[0];
                } else {
                    holder = this.form.getElementById(this.dataPasteElement);
                }
                //console.log("holder", holder);
                return holder;
            }
        },
        data() {
            return {
                ignoreFields:(typeof this.dataIgnoreFields === "undefined") ? ["id", "site_id", "backURL", "actionPerformed", "tenant_id", "_token", "insert_by", "update_by"] : JSON.parse(this.dataIgnoreFields),
                copyKey:"cms_copy_data",
                backURL:(typeof this.dataBackUrl === "undefined" || this.dataBackUrl=="") ? "" : this.dataBackUrl,
                storage:window.localStorage
            }
        },
        methods:  {

            init() {
                let $this = this;
                //not in use
                this.pasteElement.addEventListener("blur", function () {
                    $this.pasteNow();
                });
            },
            copyNow() {

                let $this = this;

                let inputs = this.form.querySelectorAll("input");
                let textAreas = this.form.querySelectorAll("textarea");
                let selects = this.form.querySelectorAll("select");

                let allElements = [...inputs, ...textAreas, ...selects];

                let store = {};
                allElements.forEach(function(current) {
                    let name = current.getAttribute("name") || current.getAttribute("id");

                    if(name!=null && typeof name !=="object" && hasInArray(name) === -1) {
                        store[name] = current.value;
                    }
                });

                textAreas.forEach(function(current) {
                    let name = current.getAttribute("name") || current.getAttribute("id");

                    if(name!=null && typeof name !=="object" && hasInArray(name) === -1) {
                        store[name] = current.value;
                    }
                });

                this.storage.setItem(this.copyKey, JSON.stringify(store));

                const el = document.createElement('textarea');
                el.value = JSON.stringify(store);
                el.setAttribute('readonly', '');
                el.style.position = 'absolute';
                el.style.left = '-99999px';
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);

                function hasInArray(name) {
                    return $this.ignoreFields.findIndex(function (nm) {
                        return name === nm;
                    });
                }

            },
            pasteNow() {
                let allElements = JSON.parse(this.storage.getItem(this.copyKey));
                let form = this.form;
                if(allElements) {
                    for(var i in allElements) {
                        try {
                            let ele = form[i] || document.getElementById(i);
                            ele.value = allElements[i];
                        } catch (e) {
                            console.info("unable to set value @ "+i);
                        }
                    }
                    this.storage.removeItem(this.copyKey);
                }
            }
        }
    }

</script>
