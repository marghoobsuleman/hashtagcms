import { animate,easeIn } from "motion";
export class Fx {

    static come(div, cb) {
        document.querySelectorAll(div).forEach((e)=> {
            e.style.display = "";
        });
        animate(div, { opacity: [0,1] },{ duration: 1 }).finished.then(() => {
            if(cb) {
                cb.apply(this, arguments);
            }
        });
    }

    static out(div, cb) {
        //div = (typeof div == "string") ? document.getElementById(div) : div;
        animate(div, { opacity: [1,0] },{ duration: 1 }).finished.then(() => {
            document.querySelectorAll(div).forEach((e)=> {
                e.style.display = "none";
            });
            if(cb) {
                cb.apply(this, arguments);
            }
        });
    }


    static highlight(div, cb) {
        div = (typeof div === "string") ? document.getElementById(div) : div;
        let backgroundColor = div.style.backgroundColor === "" ? "#fff" : div.style.backgroundColor;
        if (div.nodeName === "TR") {
            div = div.querySelectorAll("td");
        }
        animate(div, { "background-color":"#fff3cd" },{ duration: 0.5 }
        ).finished.then(() => {
            animate(div, { "background-color":backgroundColor },{ duration: 1 }).finished.then(() => {
                if(cb) {
                    cb.apply(this, arguments);
                }
            });
        });

    }

    static scrollWinTo(div, cb) {
        let ele = document.querySelector(div);
        if(ele) {
            window.scrollTo({
                top: ele.offsetTop,
                behavior: 'smooth'
            });
            setTimeout(()=> {
                if(cb) {
                    cb.apply(this, arguments);
                }
            }, 700)

        }
    };

    static slideLeft(div, cb) {
        div = document.querySelector(div);
        let old = div.getBoundingClientRect();
        div.oldProp = old;
        animate(
            div,
            { x: 0 },
            { easing: easeIn({ velocity: - (window.outerWidth + div.getBoundingClientRect().width) }) }
        ).finished.then(() => {
            if(cb) {
                cb.apply(this, arguments);
            }
        });
    }

    static slideRight(div, cb) {
        div = document.querySelector(div);
        animate(
            div,
            { x: 0 },
            { easing: easeIn({ velocity: (window.outerWidth + div.getBoundingClientRect().width) }) }
        ).finished.then(() => {
            if(cb) {
                cb.apply(this, arguments);
            }
        });
    }

    static cssAnimate(ele, css) {
        ele = (typeof ele === "string") ? document.querySelector(ele) : ele;
        ele.classList.remove( "animated", css);
        ele.classList.add( "animated", css);
    }
}
