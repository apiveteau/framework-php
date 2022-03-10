$(window).on("scroll", function () {
    let scrollHeight = $(window).scrollTop(),
        headerHeight = $("body>header").height();
    if (scrollHeight > headerHeight && !$("body").hasClass("tools-fix"))
        $("body").css({"padding-top": headerHeight}).addClass("tools-fix").find("#tools>.scroller").fadeIn(500);
    else if (scrollHeight < headerHeight && $("body").hasClass("tools-fix"))
        $("body").css({"padding-top": 0}).removeClass("tools-fix").find("#tools>.scroller").fadeOut(500);
});
