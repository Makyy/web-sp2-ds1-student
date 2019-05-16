/*
 * Define on document ready
 */
$(document).ready(function () {

    /*
     * Position of all points will be adjusted by actual picture size.
     */
    function movePoints() {

        console.log("adjusting points");

        var imgHeight = $("#body-img").height();
        var imgWidth = $("#body-img").width();

        $(".human-label").each(function () {

            var newTop = $(this).attr("ratiotop") * imgHeight;
            var newLeft = $(this).attr("ratioleft") * imgWidth;

            $(this).css({"top": newTop + "px"});
            $(this).css({"left": newLeft + "px"});
        });
    }

    movePoints();  // adjust points to actual image size

    // define resize event that calls points adjusting to actual image size.
    var timer_id;
    $(window).resize(function() {
        clearTimeout(timer_id);
        timer_id = setTimeout(movePoints(), 50);
    });
    movePoints();
});