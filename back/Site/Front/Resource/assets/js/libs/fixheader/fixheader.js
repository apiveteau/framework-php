$(window).on("scroll", function () {
    let scrollHeight = $(window).scrollTop(),
        headerHeight = $("body>header").height();
    if (scrollHeight > headerHeight && !$("body").hasClass("header-fix"))
        $("body").css({"padding-top": headerHeight}).addClass("header-fix").find("header").slideDown(500);
    else if (scrollHeight < headerHeight && $("body").hasClass("header-fix"))
        $("body").css({"padding-top": 0}).removeClass("header-fix").find("header").attr("style", "");
});