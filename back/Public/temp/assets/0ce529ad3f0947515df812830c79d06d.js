Wysiwyg = {
    element: $(".wysiwyg"),
    init: function () {
        Wysiwyg.element.each(function () {
            $(this).prev().before("<div class='previewer bg-light'></div>")
        });
        $("body").on("change, keyup", "textarea.wysiwyg", function () {
            $(this).prev().prev().html($(this).val());
        });
    }
};

$(document).ready(function () {
    Wysiwyg.init();
});
