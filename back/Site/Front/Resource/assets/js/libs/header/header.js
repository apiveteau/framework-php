$("body").on("click", ".toggle-open", function (event)
{
    event.preventDefault();
    $($(this).attr("href")).toggleClass("open")
    if ($(this).attr("data-effect") === "blur") {
        $("body>section, body>header, body>footer").toggleClass("effect-blur");
    }
});