$(function() {
    if(navigator.userAgent.match(/Android/i)) {
        $(".dayschedule-app").click(function() {
            location.href = $(".dayschedule-app a").eq(0).attr('href');
        }).css("cursor", "pointer");
    } else {
        $(".dayschedule-app a").html("Download (Android only)");
    }

});
