$(document).ready(function () {
    $("section>main>div").fadeIn(500);
    if ($("#page-module").length) {
        $("#page-module>aside>ul>li>div").each(function () {
            if (parseInt($(this).attr("data-parent")) !== 0) {
                $("#pagelink-" + $(this).attr("data-parent") + ">ul")
                    .prepend("<li>" + $(this).html() + "</li>");
                $(this).parent().remove();
            }
        });
    }
    $(".modal").prependTo("body");
});
$("body").on("click", ".toggle-open:not(.close)", function (event) {
    event.preventDefault();
    $($(this).attr("href")).parent().find(".open").removeClass("open").hide();
    $($(this).attr("href")).toggleClass("open").fadeIn(500);
}).on("click", ".toggle-open.close", function (event)
{
    event.preventDefault();
    $($(this).attr("href")).toggleClass("open").fadeOut(500);
});
