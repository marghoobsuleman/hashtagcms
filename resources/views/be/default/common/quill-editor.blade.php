@push('links')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush
@push('scripts')
    <script src="{{htcms_admin_asset('js/quill.js')}}"></script>
@endpush
@push('scripts')
    <script>
       var quill;
        document.addEventListener("DOMContentLoaded", function() {
            console.log("editor ",document.getElementById("editor"));
            quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    'toolbar': { container: '#editor-toolbar' }
                }
            });
        })

    </script>
@endpush
<!--
<div id="editor-toolbar">
    <select class="ql-size">
        <option value="10px">Small</option>
        <option value="13px" selected>Normal</option>
        <option value="18px">Large</option>
        <option value="32px">Huge</option>
    </select>
    <button class="ql-bold"></button>
</div>
<div id="editor"></div>
-->
