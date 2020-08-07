editor.addButton('mybutton', {
    text: "Image Gallery",
    onclick: function () {
        alert("My Button clicked!");
    }
});

function autoUpdateFields() {
    console.log(this.value);
}

(function() {
    document.getElementById("lang_name").addEventListener("keyup",autoUpdateFields);
})()
document.getElementById("lang_name").addEventListener("keyup",autoUpdateFields);
