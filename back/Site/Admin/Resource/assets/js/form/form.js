$(document).ready(function () {
});

$('input[data-field="title"]').on("keyup", function () {
    if ($(this).parents("form").find('input[data-field="slug"]').length)
        $(this).parents("form").find('input[data-field="slug"]')
            .val(
                "/" + encodeURI(
                $(this)
                    .val()
                    .toLowerCase()
                    .replace(/[àäâÂÄA]/g, "a")
                    .replace(/[éèêËÊE]/g, "e")
                    .replace(/[ôöÖÔO]/g, "o")
                    .replace(/[ùüûÜÛU]/g, "u")
                    .replace(/[îïÏÎI]/g, "i")
                    .replace(/\s/g, "-")
                )
            );
});

$('select[data-field="parent"]').change(function () {
    if ($(this).parents("form").find('input[data-field="slug"]').length)
        $(this).parents("form").find('input[data-field="slug"]')
            .val(
                "/" +
                encodeURI(
                    $(this).find(":selected")
                        .text()
                        .toLowerCase()
                        .replace(/[àäâÂÄA]/g, "a")
                        .replace(/[éèêËÊE]/g, "e")
                        .replace(/[ôöÖÔO]/g, "o")
                        .replace(/[ùüûÜÛU]/g, "u")
                        .replace(/[îïÏÎI]/g, "i")
                        .replace(/\s/g, "-")
                ) + "/" + $(this).parents("form").find('input[data-field="title"]').val()
                    .toLowerCase()
                    .replace(/[àäâÂÄA]/g, "a")
                    .replace(/[éèêËÊE]/g, "e")
                    .replace(/[ôöÖÔO]/g, "o")
                    .replace(/[ùüûÜÛU]/g, "u")
                    .replace(/[îïÏÎI]/g, "i")
                    .replace(/\s/g, "-")

            );
});
function ajaxForm(type, action, data, form) {
    form.addClass("wait");
    $.ajax({
        type: type,
        url: action,
        data: data,
        error: function (data) {
            console.log("ERROR")
        },
        success: function (data) {
            switch (data.status) {
                case 200:
                    if (form.attr("data-redirect") !== undefined)
                        window.location.replace(form.attr("data-redirect"));
                    break;
                case 301:
                    ajaxFormError(data.error);
                default:
                    location.reload(true);
                    break;
            }
        },
        complete: function () {
            form.removeClass("wait");
        }
    });
}
function ajaxFormError(data) {
    $.each(data, function (index, element) {
        $('input[data-field="' + index + '"]').parent().addClass("error").find(".error").text(element);
    });
}
$("body").on("click", "form.ajax>button", function (event) {
    event.preventDefault();
    if ($(this).find(".desillusion").val() !== "")
        return;
    let data = {},
        action = $(this).parents("form").attr("action"),
        type = $(this).parents("form").attr("method");
    $(this).parents("form").find("input:not(.desillusion), textarea").each(function () {
        if ($(this).parents(".previewer").length === 0)
            data[$(this).attr("data-field")] = $(this).val();
    });
    $(this).find("select").each(function () {
        if ($(this).parents(".previewer").length === 0)
            data[$(this).attr("data-field")] = $(this).find(":selected").val();
    });
    ajaxForm(type, action, data, $(this).parents("form"));
}).on("submit", "form.ajax", function (event) {
    event.preventDefault();
    if ($(this).find(".desillusion").val() !== "")
        return;
    let data = {},
        action = $(this).attr("action"),
        type = $(this).attr("method");
    $(this).find("input:not(.desillusion), textarea").each(function () {
        if ($(this).parents(".previewer").length === 0)
            data[$(this).attr("data-field")] = $(this).val();
    });
    $(this).find("select").each(function () {
        if ($(this).parents(".previewer").length === 0)
            data[$(this).attr("data-field")] = $(this).find(":selected").val();
    });
    ajaxForm(type, action, data, $(this));
}).on("click", "a.form-prefill", function (event) {
    event.preventDefault();
    let src = $(this).attr("data-src"),
        href = $($(this).attr("href"));
    href.addClass("wait");
    $.ajax({
        type: "GET",
        url: src,
        success: function (data) {
            href.find("form").find("input, textarea, select").each(function () {
                if ($(this).find("option").length) {
                    $(this).find('option[value="' + data[$(this).attr("data-field")] + '"').prop('selected', true);
                } else {
                    if (data[$(this).attr("data-field")]) {
                        if (Array.isArray(data[$(this).attr("data-field")]) || typeof data[$(this).attr("data-field")] === "object") {
                            if ($(this).parents(".previewer").length === 0)
                                $(this).val(data[$(this).attr("data-field")]["id"]);
                        } else {
                            if ($(this).hasClass("wysiwyg") && $(this).parents(".previewer").length === 0) {
                                $(this).prev().prev().html(data[$(this).attr("data-field")]);
                                $(this).val(data[$(this).attr("data-field")]);
                            }
                        }
                    }
                }
            });
        },
        complete: function () {
            href.removeClass("wait");
        }
    })
}).on("click", "button.form-prefill", function (event) {
    event.preventDefault();
    let src = $(this).attr("data-src") + $(this).parents("form").find('input[data-field="id"]').val(),
        href = $($(this).attr("data-href"));
    href.addClass("wait");
    $.ajax({
        type: "GET",
        url: src,
        success: function (data) {
            href.find("form").find("input, textarea, select").each(function () {
                if ($(this).find("option").length) {
                    $(this).find('option[value="' + data[$(this).attr("data-field")] + '"').prop('selected', true);
                } else {
                    if (data[$(this).attr("data-field")]) {
                        if (Array.isArray(data[$(this).attr("data-field")]) || typeof data[$(this).attr("data-field")] === "object") {
                            if ($(this).parents(".previewer").length === 0)
                                $(this).val(data[$(this).attr("data-field")]["id"]);
                        } else {
                            if ($(this).hasClass("wysiwyg") && $(this).parents(".previewer").length === 0) {
                                $(this).prev().prev().html(data[$(this).attr("data-field")]);
                                $(this).val(data[$(this).attr("data-field")]);
                            }
                        }
                    }
                }
            });
        },
        complete: function () {
            href.removeClass("wait");
        }
    })
});