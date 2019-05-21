/**
 * Returns top position on given image of given click event.
 * @param img image
 * @param e click event
 * @returns {number} top offset of click on image
 */
function getCurrentImageTop(img, e) {

    var offset_t = $(img).offset().top - $(window).scrollTop();
    return Math.round( (e.clientY - offset_t) );
}

/**
 * Returns left position on given image of given click event
 * @param img image
 * @param e click event
 * @returns {number} left offset of click on image
 */
function getCurrentImageLeft(img, e) {

    var offset_l = $(img).offset().left - $(window).scrollLeft();
    return Math.round( (e.clientX - offset_l) );
}

/**
 * Returns class name based on point position.
 * @param isleft true if picture is on left side, false on right
 * @returns {string} class name for point div
 */
function getPointDivClass(isleft){

    if (isleft == true){
        return "human-label right-side";
    }
    else {
        return "human-label left-side";
    }
}

/**
 * Returns path to point picture based on its position.
 * @param isleft true if picture is on left side, false on right
 * @returns {string} path to image
 */
function getPointPicturePath(isleft){

    if (isleft == true){
        return "/admin/template/img/defekty_obyvatel/path-top-left.png";
    }
    else {
        return "/admin/template/img/defekty_obyvatel/path-top-right.png";
    }
}

/**
 * Finds out left or right side based on given horizontal
 * ratio of point to human body picture.
 * @param leftratio ratio of point to width of origin image width.
 * @returns {boolean} true if picture is on left side, false on right
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

/**
 * Gets width parameter from given string.
 * @param size given string
 * @returns {number} width
 */
function getWidth(size){
    var n = size.indexOf("px");
    return parseFloat(size.substring(0, n));
}

/**
 * Gets height parameter from given string.
 * @param size given string
 * @returns {number} height
 */
function getHeight(size){
    var n = size.indexOf("px");
    var sub = size.substring(n + 2, size.length - 1);
    return parseFloat(sub);
}

/**
 * Sets visibility of given point.
 * @param point
 * @param isvisible true if visible, false if not
 */
function setPointVisibility(point, isvisible){

    var visibility = "visible";
    if (isvisible == false){
        visibility = "hidden";
    }

    $(point).css({"visibility" : visibility});
}

/**
 * Checks given point if is outside of picture. If so,
 * to point will be set hidden visibility.
 * @param point
 * @param top point coordinate
 * @param left point coordinate
 */
function checkPointVisibility(point, top, left){

    var img = $("#body-img");
    var imgheight = img.height();
    var imgwidth = img.width();

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


$(document).ready(function () {

    /**
     * Gets andjustment for point picture (for visual purpose
     * - to put picture exactly on coordinates). Adjustment is
     * based on side of body image (left or right), where point is.
     * @param leftratio ratio of point to width of origin image width.
     * @returns {number[]} point picture adjustment.
     */
    function getPointAdjustment(leftratio){

        var isleft = isOnLeftSide(leftratio);
        if (isleft ==  true){
            return [-18, -60];
        }
        else {
            return [-18, 10];
        }
    }

    /**
     * Move position of all points. Position will be
     * adjusted by actual picture size.
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

    /**
     * Moves given point.
     * @param point
     * @param imgHeight actual image height
     * @param imgWidth actual image width
     * @param offsetTop top offset of image frame in case o zoom
     * @param offsetLeft left offset of image frame in case o zoom
     */
    function movePoint(point, imgHeight, imgWidth, offsetTop, offsetLeft) {

        var ratiotop = $(point).attr("ratiotop");
        var ratioleft = $(point).attr("ratioleft");
        var adjustment = getPointAdjustment(ratioleft);

        var newPosition = getNewPosition(ratiotop, ratioleft, imgHeight, imgWidth, adjustment, offsetTop, offsetLeft);

        $(point).css({"top": newPosition[0] + "px"});
        $(point).css({"left": newPosition[1] + "px"});

        checkPointVisibility(point, newPosition[0]- adjustment[0], newPosition[1] - adjustment[1]);
    }

    /**
     * Calculates position by given ratio of coordinates to origin image size
     * @param ratioTop ratio of coordinate to origin image height
     * @param ratioLeft ratio of coordinate to origin image width
     * @param imgHeight actual image height
     * @param imgWidth actual image width
     * @param adjustment adjustment for point image (for visual purpose - to put image exactly on the coordinates)
     * @param offsetTop top offset of image frame in case o zoom
     * @param offsetLeft left offset of image frame in case of zoom
     * @returns {number[]} new point position
     */
    function getNewPosition(ratioTop, ratioLeft, imgHeight, imgWidth, adjustment, offsetTop, offsetLeft){
        var newTop = ratioTop * imgHeight + adjustment[0] - Math.abs(offsetTop);
        var newLeft = ratioLeft * imgWidth + adjustment[1] - Math.abs(offsetLeft);

        return [newTop, newLeft];
    }

    /**
     * Adds new point (new point div over body image).
     * @param top
     * @param left
     */
    function addPoint(top, left) {

        var img = document.getElementById("body-img");
        var stringSize = img.style.backgroundSize;
        var stringPosition = img.style.backgroundPosition;

        var imgTop = getHeight(stringPosition);
        var imgLeft = getWidth(stringPosition);

        var imgHeight = getHeight(stringSize);
        var imgWidth = getWidth(stringSize);

        top = top - imgTop;
        left = left - imgLeft;

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

        definePointOnClick(pointdiv);

        var imgFrame = $("#body-img");
        var imgFrameHeight = imgFrame.height();
        var imgFrameWidth = imgFrame.width();

        if (imgWidth != imgFrameWidth) {
            top = imgFrameHeight * ratiotop;
            left = imgFrameWidth * ratioleft;
        }

        let elements = {
            pointdiv: pointdiv,
            image: image,
            label: label,
            actualPosition: [top, left]
        };

        savePoint(elements);
    }

    /**
     * Sets 'canaddpoint' attribute to body image.
     * @param canAdd true if points can be added
     * @constructor
     */
    function SetAbilityToAddPoints(canAdd){
        if (canAdd == true){
            $("#body-img").attr("canaddpoint", "true");
            $("#body-img").attr("class", "cross-cursor zoom");
        }
        else {
            $("#body-img").attr("canaddpoint", "false");
            $("#body-img").attr("class", "default-cursor zoom");
        }
    }

    /**
     * Define on click event for point
     * @param div
     */
    function definePointOnClick(div){

        div.onclick = function (e) {

            var img = $("#body-img");
            var canAddPoint = img.attr("canaddpoint");

            // if points can be added
            if (canAddPoint === "false") return;

            var top = getCurrentImageTop(img, e);
            var left = getCurrentImageLeft(img, e);

            addPoint(top, left);
        };
    }

    /**
     * Ukládání bodu do databáze
     *
     * @param elements
     */
    function savePoint(elements){
        $.ajax({
            url: window.location.href + "&action=add_point",
            data: {
                "x": elements.actualPosition[0],
                "y": elements.actualPosition[1]
            },
            method: "POST"
        }).done(function (result) {
            addDefectInput(result, elements.actualPosition[0], elements.actualPosition[1]);
            addDefectImage(elements, result);
            SetAbilityToAddPoints(false);
        });
    }

    /**
     * Přidání vstupního prvku pro editaci
     *
     * @param id : ID defektu v databázi
     */
    function addDefectInput(id, x, y) {
        let submitButton = $("form#defect-form input[type=submit]");
        if (submitButton.is(":hidden")) {
            submitButton.show();
        }

        let form = $("form#defect-form");
        let inputs = $("form#defect-form input[type=text]");
        let len = inputs.length + 1;

        form.find("input:submit").before('<div class="input-group mt-2" id="defect-group-'+id+'"><label style="align-content: center; width: 20px; text-align: center; padding-top: 6px;"  for="' + id + '">' + len + '.</label> <input type="text" class="form-control" name="def[' + id + ']" value="DEFECT"><a class="btn btn-danger btn-sm ml-1" style="padding-top:6px;color: #fafafa" id="delete-'+ id +'"><i class="fa fa-fw fa-times"></i></a></div>');

        registerRemoveOnclickHandler(id);
    }

    /**
     * Přidání bodu do obrázku
     *
     * @param elements
     * @param id
     */
    function addDefectImage(elements, id) {
        var divgroup = document.createElement("div");
        divgroup.setAttribute("id", "defect-image-group-" + id);

        divgroup.appendChild(elements.pointdiv);
        elements.pointdiv.appendChild(elements.image);
        elements.pointdiv.appendChild(elements.label);
        document.getElementById("body-div").appendChild(divgroup);

        movePoints();
    }

    /**
     * Odstranění defektu z DB
     *
     * @param id
     */
    function removeDefect(id) {
        $.ajax({
            url: window.location.href + "&action=remove_point",
            data: {
                "defect_id": id
            },
            method: "POST"
        }).done(function (data) {
            if (data) {
                removeDefectInput(id);
            } else {
                alert("Nepodařilo se odstranit defekt.");
            }
        });
    }

    /**
     * Odstranení vstupního prvku a bodu z obrázku po odstranění z DB
     *
     * @param id
     */
    function removeDefectInput(id){
        $("form#defect-form #defect-group-" + id).remove();
        $("#defect-image-group-" + id).remove();

        let inputs = $("form#defect-form input[type=text]");
        if (inputs.length === 0) {
            $("form#defect-form input[type=submit]").hide();
        }
    }

    /**
     * Zaregistrování onclick handleru pro tlačítko na odstranění defektu
     *
     * @param id : int|null pokud je vyplněno nastaví se handler přímo pro dané tlačítko, pokud ne, nastaví se pro
     *                      všechny mazací tlačítka ve formuláři
     */
    function registerRemoveOnclickHandler(id) {
        if (id === null) {
            $("form#defect-form a").each(function () {

                let idStr = this.getAttribute("id");

                if (idStr == null) return;
                if (idStr.includes("delete-") == false) return;

                let id = idStr.replace("delete-", "");

                this.onclick = function () {
                    if (confirm("Opravdu chcete odstranit defekt?")) {
                        removeDefect(id);
                    }
                };
            })
        } else {
            $("form#defect-form #delete-" + id).click(function () {
                if (confirm("Opravdu chcete odstranit defekt?")) {
                    removeDefect(id);
                }
            });
        }
    }

    $(document).ready(function () {
        movePoints();  // adjust points to actual image size

        // zaregistrování onlick eventu pro odstranění defektu
        registerRemoveOnclickHandler(null);
    });

    // define resize event that calls points adjusting to actual image size.
    $("#body-img").resize(movePoints);

    // define onclick event for human body image - enables to add points to this image
    $("#body-img").on("click", function(e) {

        var img = $("#body-img");
        var canAddPoint = img.attr("canaddpoint");

        if (canAddPoint === "false") return;

        console.log("can edit");

        var top = getCurrentImageTop(this, e);
        var left = getCurrentImageLeft(this, e);

        addPoint(top, left);
    });

    // define for already added points onclick event (enables add new point that are over this point)
    $(".human-label").each(function () {
        definePointOnClick(this);
    });

    // register zooming
    wheelzoom(document.querySelector('img.zoom'));

    // register moving points on zooming
    var img = document.getElementById("body-img");
    img.addEventListener('wheel', function(){
        movePoints();
    });
    img.addEventListener('mousemove', function(){
        movePoints();
    });

    // click event for adding point - ensures enabling adding
    $("#add-point-to-body-btn").on("click", function(e) {
        SetAbilityToAddPoints(true);
    });

});