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

            var adjusttop = -18;
            var adjustleft = 10;

            var newTop = $(this).attr("ratiotop") * imgHeight + adjusttop;
            var newLeft = $(this).attr("ratioleft") * imgWidth + adjustleft;

            $(this).css({"top": newTop + "px"});
            $(this).css({"left": newLeft + "px"});
        });
    }

    /*
     * Adds new point
     */
    function addPoint(top, left) {

        var imgHeight = $("#body-img").height();
        var imgWidth = $("#body-img").width();

        var ratiotop = top / imgHeight;
        var ratioleft = left / imgWidth;;

        var image = document.createElement("img");
        image.setAttribute("src", "/admin/template/img/defekty_obyvatel/path-top-right.png");
        image.setAttribute("alt", "defect point");

        var label = document.createElement("label");
        label.innerHTML = "DEFECT";

        var pointdiv = document.createElement("div");
        pointdiv.style.cssText = "top:" + top + "px; left:" + left + "px;";
        pointdiv.setAttribute("class", "human-label");
        pointdiv.setAttribute("ratiotop", ratiotop);
        pointdiv.setAttribute("ratioleft", ratioleft);

        pointdiv.appendChild(image);
        pointdiv.appendChild(label);
        document.getElementById("body-div").appendChild(pointdiv);
        movePoints();
    }

    movePoints();  // adjust points to actual image size

    // define resize event that calls points adjusting to actual image size.
    var timer_id;
    $(window).resize(function() {
        clearTimeout(timer_id);
        timer_id = setTimeout(movePoints(), 50);
    });

    $("#body-img").on("click", function(e) {

        var offset_t = $(this).offset().top - $(window).scrollTop();
        var offset_l = $(this).offset().left - $(window).scrollLeft();

        var left = Math.round( (e.clientX - offset_l) );
        var top = Math.round( (e.clientY - offset_t) );

        addPoint(top, left);
    });

    movePoints();
});