
/** Effects
 * dependency: animcss
 */
export class FX {

    static come(div, cb) {
        div = (typeof div == "string") ? document.getElementById(div) : div;
        Velocity(div, "fadeIn").then(function() {
            if(cb) {
                cb.apply(this, arguments);
            };
        });
    };

    static out(div, cb) {
        div = (typeof div == "string") ? document.getElementById(div) : div;
        Velocity(div, "fadeOut").then(function() {
            if(cb) {
                cb.apply(this, arguments);
            };
        });
    };


    static highlight(div, cb) {
        div = (typeof div == "string") ? document.getElementById(div) : div;
        let backgroundColor = div.style.backgroundColor == "" ? "#fff" : div.style.backgroundColor;

        Velocity(div, {opacity:0, backgroundColor:"#dcdc2a"}, {duration:500}).then(function() {
            Velocity(div, {opacity:1, backgroundColor:backgroundColor}, {duration:500}).then(function () {
                if(cb) {
                    cb.apply(this, arguments);
                }
            });
        });

    }

    static slideDown(div, cb) {
        div = (typeof div == "string") ? document.getElementById(div) : div;
        var oldDisplay = div.style.display;
        if(oldDisplay=="none") {
            Velocity(div, "slideDown").then(function(arg) {
                div.style.display = (oldDisplay=="none") ? "" : oldDisplay;
                if(cb) {
                    cb.apply(this, arguments);
                };
            });
        }
    };

    static slideUp(div, cb) {
        div = (typeof div == "string") ? document.getElementById(div) : div;
        Velocity(div, "slideUp").then(function(arg) {
            if(cb) {
                cb.apply(this, arguments);
            }
        });
    }

    static toggleSlide(div, cb) {
        div = (typeof div == "string") ? document.getElementById(div) : div;
        var display = div.style.display;
        if(display == "") {
            this.slideUp(div, cb);
        } else {
            this.slideDown(div,  cb);
        }
    };

    static scrollWinTo(div, cb) {
        if(document.getElementById(div)) {
            Velocity(document.getElementById(div), "scroll", {
                duration: 500,
                complete: function () {
                    if(cb) {
                        cb.apply(this, arguments);
                    }
                }
            });
        }
    };

    static slideLeft(div, cb) {
        div = (typeof div == "string") ? document.getElementById(div) : div;
        let old = div.getBoundingClientRect();
        div.oldProp = old;
        Velocity(div, {width:0}).then(function() {
            if(cb) {
                cb.apply(this, arguments);
            };
            div.style.display = "none";
        });
    };

    static slideRight(div, cb) {
        div = (typeof div == "string") ? document.getElementById(div) : div;
        let width = (div.oldProp) ? div.oldProp.width : div.getBoundingClientRect().width;
        Velocity(div, {width:width}).then(function() {
            if(cb) {
                cb.apply(this, arguments);
            };
        });
    }
}
