import {Toast} from "./Common";

export class ErrorMessage  {
    constructor() {
        this.errors = (typeof error_messages == "undefined" ) ? [] : error_messages ;
        this.init();
    }
    init() {
        let $this = this;
        document.addEventListener("DOMContentLoaded", function () {
            for(let i in $this.errors) {
                $this.highlightField(i, $this.errors[i][0]);
            };
            $this.focusFirst();
        });
    }
    highlightField(field, message) {

        let f = field;
        field = document.getElementById(f);
        if(!field) {
            console.error("Could not find "+f);
            return false;
        }
        field.classList.add("is-invalid");

        let div = document.createElement("div");
        div.classList.add("alert", "alert-danger", "margin-top-05");

        if(f.indexOf("lang_") != -1) {
            message  = message.replace("lang ", "")
        }
        div.innerHTML = message;

        field.parentNode.insertBefore(div, field.nextSibling);

    }
    focusFirst() {

        for(let i in this.errors) {
            let element = document.getElementById(i);
            if(element) {
                element.focus();
                break;
            }

        }

    }
}