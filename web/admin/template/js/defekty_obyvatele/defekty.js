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

function getWidth(size){
    var n = size.indexOf("px");
    return parseFloat(size.substring(0, n));
}

function getHeight(size){
    var n = size.indexOf("px");
    var sub = size.substring(n + 2, size.length - 1);
    return parseFloat(sub);
}

function setPointVisibility(point, isvisible){

    var visibility = "visible";
    if (isvisible == false){
        visibility = "hidden";
    }

    $(point).css({"visibility" : visibility});
}

function checkPointVisibility(point, top, left){

    var img = $("#body-img");

    var imgtop = img.offset().top;
    var imgleft = img.offset().left;
    var imgheight = img.height() + imgtop;
    var imgwidth = img.width() + imgleft;


    if (top < 0){
        setPointVisibility(point, false);
        return;
    }
    if (left < 0){
        setPointVisibility(point, false);
        return;
    }
    if (top > imgheight){
        setPointVisibility(point, false);
        return;
    }
    if (left > imgwidth){
        setPointVisibility(point, false);
        return;
    }
    setPointVisibility(point, true);

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

        var img = document.getElementById("body-img");
        var stringSize = img.style.backgroundSize;
        var stringPosition = img.style.backgroundPosition;

        var imgHeight = getHeight(stringSize);
        var imgWidth = getWidth(stringSize);

        var imgTop = getHeight(stringPosition);
        var imgLeft = getWidth(stringPosition);

        $(".human-label").each(function () {
            movePoint(this, imgHeight, imgWidth, imgTop, imgLeft);
        });
    }

    function movePoint(point, imgHeight, imgWidth, offsetTop, offsetLeft) {

        var ratiotop = $(point).attr("ratiotop");
        var ratioleft = $(point).attr("ratioleft");
        var adjustment = getPointAdjustment(ratioleft);

        // var newTop = ratiotop * imgHeight + adjustment[0] - Math.abs(offsetTop);
        // var newLeft = ratioleft * imgWidth + adjustment[1] - Math.abs(offsetLeft);
        var newPosition = getNewPosition(ratiotop, ratioleft, imgHeight, imgWidth, adjustment, offsetTop, offsetLeft);

        $(point).css({"top": newPosition[0] + "px"});
        $(point).css({"left": newPosition[1] + "px"});

        checkPointVisibility(point, newPosition[0]- adjustment[0], newPosition[1] - adjustment[1]);
    }

    function getNewPosition(ratioTop, ratioLeft, imgHeight, imgWidth, adjustment, offsetTop, offsetLeft){
        var newTop = ratioTop * imgHeight + adjustment[0] - Math.abs(offsetTop);
        var newLeft = ratioLeft * imgWidth + adjustment[1] - Math.abs(offsetLeft);

        return [newTop, newLeft];
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

        //TODO: tyhle počty ukládat nějak normálně
        var img = document.getElementById("body-img");
        var stringPosition = img.style.backgroundPosition;
        var imgTop = getHeight(stringPosition);
        var imgLeft = getWidth(stringPosition);

        var adjustment = getPointAdjustment(ratioleft);
        var actualPosition = getNewPosition(ratiotop, ratioleft, 4010, 5940, adjustment, imgTop, imgLeft);

        definePointOnClick(pointdiv);

        //X = left, Y = top
        savePoint(actualPosition[0], actualPosition[1]);
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

    /**
     * Ukládání bodu do databáze
     *
     * @param x
     * @param y
     */
    function savePoint(x, y){
        $.ajax({
            url: window.location.href + "&action=add_point",
            data: {
                "x": x,
                "y": y
            },
            method: "POST"
        }).done(function (result) {
            addDefectInput(result);
        });
    }

    /**
     * Přidání vstupního prvku pro editaci
     *
     * @param id : ID defektu v databázi
     */
    function addDefectInput(id) {
        let form = $("form#defect-form");

        form.find("input:submit").before('<div class="input-group mt-2"><label for="' + id + '">' + id + '.</label> <input type="text" class="form-control" name="def[' + id + ']" value="DEFECT"></div>');
    }

    // define resize event that calls points adjusting to actual image size.
    $("#body-img").resize(movePoints);

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
    wheelzoom(document.querySelector('img.zoom'));

    var img = document.getElementById("body-img");
    img.addEventListener('wheel', function(){
        movePoints();
    });

    img.addEventListener('mousemove',  function(){
        movePoints();
    });
});