function renderTableHeader(key, show, edition) {
    let headerhtml = "<thead><tr>";
    if (edition === "true")
        headerhtml += "<th class='edit'><i class='fi fi-hashtag'></i></th>";
    key.forEach(function (value) {
        if (show.indexOf(value) !== -1)
            headerhtml += "<th class='" + value + "'>" + value + "</th>";
        else
            headerhtml += "<th class='hidden " + value + "'>" + value + "</th>";
    });
    return headerhtml += "</tr></thead>";
}

function renderEditLink(id) {
    return "<td class='edit'><a href='#user-modal-edit' class='btn btn-dark edit form-prefill toggle-open' data-src='/admin/api/user/get/" + id +"'><i class='fi fi-spinner-cog'></i></a><a href='/admin/api/user/delete/" + id + "' class='ajax validation btn btn-secondary' title='Delete'><i class=\"fi fi-trash\" data-tags=\"interfaces\"></i></a></td>";
}

function renderTableAjaxDiv(parent, elements, edition) {
    let html = "<table>",
        bodyhtml = "<tbody>",
        key = false;
    $.each(elements, function (index, element) {
        if (!key)
            key = Object.keys(element);
        bodyhtml += "<tr data-id='" + element.id + "'>";
        if (edition === "true")
            bodyhtml += renderEditLink(element.id);
        $.each(element, function (property, value) {
            if (parent.attr("data-show").split(",").indexOf(property) !== -1)
                bodyhtml += "<td class='" + value + "'>" + value + "</td>";
            else
                bodyhtml += "<td class='hidden " + value + "'>" + value + "</td>";
        });
        bodyhtml += "</tr>";
    });
    bodyhtml +="</tbody>";
    html += renderTableHeader(key, parent.attr("data-show").split(","), edition) + bodyhtml + "</table>";
    $(parent).html(html);
}
function renderAjaxDiv(parent, elements, render, edition) {
    switch (render) {
        case "table":
            renderTableAjaxDiv(parent, elements, edition);
            break;
        default:
            return;
    }
}

$(document).ready(function () {
    $("div.ajax").each(function () {
        let url = $(this).attr("data-target"),
            render = $(this).attr("data-render"),
            edition = $(this).attr("data-edition"),
            parent = $(this);
        $.ajax({
            type: "GET",
            url: url,
            success: function (data) {
                renderAjaxDiv(parent, data, render, edition);
            }
        })
    });
});

$("body").on("click", "a.ajax:not(.validation)", function (event) {
    let refresh = $(this).attr("data-refresh"),
        redirect = $(this).attr("data-redirect");
    event.preventDefault();
    $.ajax({
        type: "GET",
        url: $(this).attr("href"),
        success: function (data) {
            if (refresh === "true")
                location.reload(true);
            if (redirect !== undefined)
                window.location.replace(redirect);
        }
    })
}).on("click", "a.ajax.validation", function (event) {
    let refresh = $(this).attr("data-refresh"),
        redirect = $(this).attr("data-redirect");
    event.preventDefault();
    if (!confirm("Are you sure ?"))
        return;
    $.ajax({
        type: "GET",
        url: $(this).attr("href"),
        success: function (data) {
            if (refresh === "true")
                location.reload(true);
            if (redirect !== undefined)
                window.location.replace(redirect);
        }
    })
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
                            $(this).val(data[$(this).attr("data-field")]["id"]);
                        } else {
                            $(this).val(data[$(this).attr("data-field")]);
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