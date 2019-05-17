/*
 * Returns top position on given image of given click event
 */
function getCurrentImageTop(img, e) {

    var offset_t = $(img).offset().top - $(window).scrollTop();
    return Math.round( (e.clientY - offset_t) );
}

/*
 * Returns left position on given image of given click event
 */
function getCurrentImageLeft(img, e) {

    var offset_l = $(img).offset().left - $(window).scrollLeft();
    return Math.round( (e.clientX - offset_l) );
}

/*
 * Returns class name based on point position
 */
function getPointDivClass(isleft){

    if (isleft == true){
        return "human-label right-side";
    }
    else {
        return "human-label left-side";
    }
}

/*
 * Returns path to point picture based on its position
 */
function getPointPicturePath(isleft){

    if (isleft == true){
        return "/admin/template/img/defekty_obyvatel/path-top-left.png";
    }
    else {
        return "/admin/template/img/defekty_obyvatel/path-top-right.png";
    }
}

/*
 * Finds out left or right side based on given horizontal
 * ratio of point to human body picture
 */
function isOnLeftSide(leftratio){

    var border = 0.5;
    if (leftratio > border){
        return true;
    }
    else {
        return false;
    }
}

/*
 * Define on document ready
 */
$(document).ready(function () {

    function getPointAdjustment(leftratio){

        var isleft = isOnLeftSide(leftratio);
        if (isleft ==  true){
            return [-18, -60];
        }
        else {
            return [-18, 10];
        }
    }
    /*
     * Position of all points will be adjusted by actual picture size.
     */
    function movePoints() {

        console.log("adjusting points");

        var imgHeight = $("#body-img").height();
        var imgWidth = $("#body-img").width();

        $(".human-label").each(function () {

            var ratiotop = $(this).attr("ratiotop");
            var ratioleft = $(this).attr("ratioleft");
            var adjustment = getPointAdjustment(ratioleft);

            var newTop = ratiotop * imgHeight + adjustment[0];
            var newLeft = ratioleft * imgWidth + adjustment[1];

            $(this).css({"top": newTop + "px"});
            $(this).css({"left": newLeft + "px"});
        });
    }
    movePoints();  // adjust points to actual image size

    /*
     * Adds new point
     */
    function addPoint(top, left) {

        var imgHeight = $("#body-img").height();
        var imgWidth = $("#body-img").width();

        var ratiotop = top / imgHeight;
        var ratioleft = left / imgWidth;
        var isleft = isOnLeftSide(ratioleft);

        var image = document.createElement("img");
        image.setAttribute("src", getPointPicturePath(isleft));
        image.setAttribute("alt", "defect point");

        var label = document.createElement("label");
        label.innerHTML = "DEFECT";

        var pointdiv = document.createElement("div");
        pointdiv.style.cssText = "top:" + top + "px; left:" + left + "px;";
        pointdiv.setAttribute("class", getPointDivClass(isleft));
        pointdiv.setAttribute("ratiotop", ratiotop);
        pointdiv.setAttribute("ratioleft", ratioleft);

        pointdiv.appendChild(image);
        pointdiv.appendChild(label);
        document.getElementById("body-div").appendChild(pointdiv);
        movePoints();
        definePointOnClick(pointdiv);
    }

    /*
     * Define on click event for point
     */
    function definePointOnClick(div){

        div.onclick = function (e) {

            var img = $("#body-img");

            var top = getCurrentImageTop(img, e);
            var left = getCurrentImageLeft(img, e);

            addPoint(top, left);
        };
    }

    // define resize event that calls points adjusting to actual image size.
    var timer_id;
    $(window).resize(function() {
        clearTimeout(timer_id);
        timer_id = setTimeout(movePoints(), 50);
    });

    // define onclick event for human body image - enables to add points to this image
    $("#body-img").on("click", function(e) {

        var top = getCurrentImageTop(this, e);
        var left = getCurrentImageLeft(this, e);

        addPoint(top, left);
    });

    // define for already added points onclick event (enables add new point that are over this point)
    $(".human-label").each(function () {
        definePointOnClick(this);
    });

    movePoints();
});