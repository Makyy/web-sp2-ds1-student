const FORM_PROGRESS_ACTION_URL = "/admin/index.php/plugin/human?action=save_progress";

/**
 * Returns top position on given image of given click event.
 * @param img image
 * @param e click event
 * @returns {number} top offset of click on image
 */
function getCurrentImageTop(img, e) {

    let offset_t = $(img).offset().top - $(window).scrollTop();
    return Math.round( (e.clientY - offset_t) );
}

/**
 * Returns left position on given image of given click event
 * @param img image
 * @param e click event
 * @returns {number} left offset of click on image
 */
function getCurrentImageLeft(img, e) {

    let offset_l = $(img).offset().left - $(window).scrollLeft();
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

    let border = 0.5;
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
    let n = size.indexOf("px");
    return parseFloat(size.substring(0, n));
}

/**
 * Gets height parameter from given string.
 * @param size given string
 * @returns {number} height
 */
function getHeight(size){
    let n = size.indexOf("px");
    let sub = size.substring(n + 2, size.length - 1);
    return parseFloat(sub);
}

/**
 * Sets visibility of given point.
 * @param point
 * @param isvisible true if visible, false if not
 */
function setPointVisibility(point, isvisible){

    let visibility = "visible";
    if (isvisible == false) visibility = "hidden";

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

    let img = $("#body-img");
    let pointH = 30;

    if ((top - pointH) < 0){// top edge
        setPointVisibility(point, false);
        return;
    }
    if (left < 0){          // left edge
        setPointVisibility(point, false);
        return;
    }
    if (top > img.height()){   // bottom edge
        setPointVisibility(point, false);
        return;
    }
    if ((left) > img.width()){   // right edge
        setPointVisibility(point, false);
        return;
    }
    setPointVisibility(point, true);
}

/**
 * Resets animation to given element.
 * @param el element
 */
function resetAnimation(el) {
    el.style.animation = 'none';
    el.offsetHeight; /* trigger reflow */
    el.style.animation = null;
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

        let isleft = isOnLeftSide(leftratio);
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

        let img = document.getElementById("body-img");
        let stringSize = img.style.backgroundSize;
        let stringPosition = img.style.backgroundPosition;

        let imgHeight = getHeight(stringSize);
        let imgWidth = getWidth(stringSize);

        let imgTop = getHeight(stringPosition);
        let imgLeft = getWidth(stringPosition);

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

        let ratiotop = $(point).attr("ratiotop");
        let ratioleft = $(point).attr("ratioleft");
        let adjustment = getPointAdjustment(ratioleft);

        let newPosition = getNewPosition(ratiotop, ratioleft, imgHeight, imgWidth, adjustment, offsetTop, offsetLeft);

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
        let newTop = ratioTop * imgHeight + adjustment[0] - Math.abs(offsetTop);
        let newLeft = ratioLeft * imgWidth + adjustment[1] - Math.abs(offsetLeft);

        return [newTop, newLeft];
    }

    /**
     * Adds new point (new point div over body image).
     * @param top
     * @param left
     */
    function addPoint(top, left) {

        let img = document.getElementById("body-img");
        let stringSize = img.style.backgroundSize;
        let stringPosition = img.style.backgroundPosition;

        let imgTop = getHeight(stringPosition);
        let imgLeft = getWidth(stringPosition);

        let imgHeight = getHeight(stringSize);
        let imgWidth = getWidth(stringSize);

        top = top - imgTop;
        left = left - imgLeft;

        let ratiotop = top / imgHeight;
        let ratioleft = left / imgWidth;
        let isleft = isOnLeftSide(ratioleft);

        let image = document.createElement("img");
        image.setAttribute("src", getPointPicturePath(isleft));
        image.setAttribute("alt", "defect point");

        let label = document.createElement("label");
        label.innerHTML = "DEFECT";

        let pointdiv = document.createElement("div");
        pointdiv.style.cssText = "top:" + top + "px; left:" + left + "px;";
        pointdiv.setAttribute("class", getPointDivClass(isleft));
        pointdiv.setAttribute("ratiotop", ratiotop);
        pointdiv.setAttribute("ratioleft", ratioleft);

        definePointOnClick(pointdiv);

        let imgFrame = $("#body-img");
        let imgFrameHeight = imgFrame.height();
        let imgFrameWidth = imgFrame.width();

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
            $("#body-img").removeClass("default-cursor");
            $("#body-img").addClass("cross-cursor");        // set cross cursor over image
        }
        else {
            $("#body-img").attr("canaddpoint", "false");
            $("#body-img").removeClass("cross-cursor");
            $("#body-img").addClass("default-cursor");      // set default cursor over image
        }
    }

    /**
     * Define on click event for point
     * @param div
     */
    function definePointOnClick(div){

        div.onclick = function (e) {

            let img = $("#body-img");

            if (img.attr("canaddpoint") === "false") return; // if points can be added

            let top = getCurrentImageTop(img, e);
            let left = getCurrentImageLeft(img, e);

            addPoint(top, left);
        };
    }

    /**
     * Saves defect to database.
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
     * Adds defect input for editing.
     *
     * @param id : Defect id  from database
     */
    function addDefectInput(id, x, y) {
        let form = $("form#defect-form");
        let inputs = $("form#defect-form input[type=text]");
        let len = inputs.length + 1;

        showFormSaveButton();

        form.find("input:submit").before('<div class="input-group mt-2" id="defect-group-'+id+'"><label style="align-content: center; width: 20px; text-align: center; padding-top: 6px;"  for="' + id + '">' + len + '.</label> <input type="text" class="form-control" name="def[' + id + ']" value="DEFECT"><a class="btn btn-primary btn-sm ml-1" style="padding-top:6px;color: #fafafa" data-toggle="modal" data-target="#prubeh-'+ id +'" title="Přidat průběh"><i class="fa fa-fw fa-search"></i></a><a class="btn btn-danger btn-sm ml-1" style="padding-top:6px;color: #fafafa" id="delete-'+ id +'"><i class="fa fa-fw fa-times"></i></a></div>');

        registerRemoveOnclickHandler(id);

        addProgressModal(id);
    }

    /**
     * Shows form submit button if hidden
     */
    function showFormSaveButton(){
        let submitButton = $("form#defect-form input[type=submit]");
        if (submitButton.is(":hidden")) {
            submitButton.show();
        }
    }

    /**
     * Adds defect point to body image.
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
     * Deletes defect from database.
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
                alert("Nepodařilo se smazat defekt.");
            }
        });
    }

    /**
     * Resets animation for defect point div.
     * @param id of defect
     */
    function resetDefectDivAnimation(id) {

        let div_id = "defect-image-group-" + id;
        let div = document.getElementById(div_id);

        resetDivAnimation(div);
    }

    /**
     * Resets animation for image and label in given div.
     * @param div
     */
    function resetDivAnimation(div) {

        resetAnimation(div.getElementsByTagName("IMG")[0]);     // restart image animation
        resetAnimation(div.getElementsByTagName("LABEL")[0]);   // restart label animation
    }

    /**
     * Deletes defect with its input after deleting defect from database.
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
     * Registers onclick handler to button for defect deleting.
     *
     * @param id : int|null if not null, handler is set directly to given button,
     *             if not handler is set to every deleting button in form.
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
            $("form#defect-form #defect-group-" + id + " input").focusin(function () { //register method of focus to defect input
                resetDefectDivAnimation(id);
            });
        }
    }

    /**
     * Handles submitting of the progress form by AJAX
     */
    function handleSubmitProgressForm()
    {
        $("form[id^=form-progress]").each(function ()
        {
            $(this).submit(function (e)
            {
                e.preventDefault();

                let data = $(this).serialize();
                let dataArray = {};
                let form = $(this);

                $($(this).serializeArray()).each(function (i, field)
                {
                    dataArray[field.name] = field.value;
                });

                $.ajax({
                    url: window.location.href.replace('action=detail', 'action=save_progress'),
                    data: data,
                    method: "POST"
                }).done(function (result)
                {
                    addProgressRow(dataArray['defekt_id'], result);

                    // clear form values
                    form.trigger("reset");
                }).fail(function ()
                {
                    alert('Průběh se nepodařilo uložit.');
                });
            });
        })
    }

    /**
     * Adds new row into the table in the modal dialog for defect progress
     *
     * @param defectId
     * @param data
     */
    function addProgressRow(defectId, data)
    {
        $("table[id=table-progress-" + defectId + "] > tbody:last-child").append("<tr><td>"+data.popis+"</td><td>"+data.status+"</td><td>"+data.datum_vytvoreni+"</td></tr>");
    }

    /**
     * Function adds new modal form for defect progress
     *
     * @param defectId
     */
    function addProgressModal(defectId)
    {
        let html = '<div class="modal" id="prubeh-' + defectId + '">';
                html += '<div class="modal-dialog">';
                    html += '<div class="modal-content">';
                        html += '<div class="modal-header">';
                            html += '<h4 class="modal-title">Průběh defektu</h4>';
                            html += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                        html += '</div>';
                        html += '<div class="modal-body">';
                            html += '<table class="table table-sm table-bordered table-striped table-hover" id="table-progress-' + defectId + '">';
                                html += '<thead class="thead-light">';
                                    html += '<tr>';
                                        html += '<th>popis</th>';
                                        html += '<th>stav</th>';
                                        html += '<th>datum</th>';
                                    html += '</tr>';
                                html += '</thead>';
                                html += '<tbody>';
                                html += '</tbody>';
                            html += '</table>';

                            html += '</hr>';

                            html += '<form class="form" action="' + FORM_PROGRESS_ACTION_URL + '" method="POST" id="form-progress-' + defectId + '">';
                                html += '<div class="row">';
                                    html += '<div class="col-3">';
                                        html += '<label style="padding-top: 6px;" for="popis">Popis</label>';
                                    html += '</div>';
                                    html += '<div class="col-9">';
                                        html += '<input class="form-control" type="text" name="popis">';
                                    html += '</div>';
                                html += '</div>';

                                html += '<div class="row mt-2">';
                                    html += '<div class="col-3">';
                                        html += '<label style="padding-top: 6px;" for="stav">Stav</label>';
                                    html += '</div>';
                                    html += '<div class="col-9">';
                                        html += '<select class="form-control" name="stav">';
                                            html += '<option value="0">stejný</option>';
                                            html += '<option value="1">zlepšení</option>';
                                            html += '<option value="2">zhoršení</option>';
                                        html += '</select>';
                                    html += '</div>';
                                html += '</div>';

                                html += '<input type="hidden" name="defekt_id" value="' + defectId + '">';

                                html += '<div class="row mt-5 pull-right">';
                                    html += '<div class="col-3">';
                                        html += '<input type="submit" value="Aktualizovat průběh" class="btn btn-primary">';
                                    html += '</div>';
                                html += '</div>';

                            html += '</form>';
                        html += '</div>';
                    html += '</div>';
                html += '</div>';
            html += '</div>';

        $("#progress-modals").append(html);
        handleSubmitProgressForm();
    }

    $(document).ready(function () {
        $("#human-defects .human-label").each(function () {
            $(this).removeClass("hidden-label");
            resetDivAnimation(this);
        });

        registerRemoveOnclickHandler(null); // register on click - delete defect

        movePoints();  // adjust points to actual image size

        handleSubmitProgressForm(); // prevent default, send by AJAX
    });

    // define resize event that calls points adjusting to actual image size.
    $("#body-img").resize(movePoints);

    // define onclick event for human body image - enables to add points to this image
    $("#body-img").on("click", function(e) {

        if ($(this).attr("canaddpoint") === "false") return;    // if adding a point is enabled

        let top = getCurrentImageTop(this, e);
        let left = getCurrentImageLeft(this, e);

        addPoint(top, left);
    });

    // define for already added points onclick event (enables add new point that are over this point)
    $(".human-label").each(function () {
        definePointOnClick(this);
    });

    // register zooming
    wheelzoom(document.querySelector('img.zoom'));

    // register moving points on zooming & dragging
    let img = document.getElementById("body-img");
    img.addEventListener('wheel',movePoints);
    img.addEventListener('mousemove',movePoints);

    // click event for adding point - ensures enabling adding
    $("#add-point-to-body-btn").on("click", function(e) {
        SetAbilityToAddPoints(true);
    });

    // register to input on focus that restart animation of defect point
    $("form#defect-form .point-tb").each(function () {
        $(this).focusin(function () {
            resetDefectDivAnimation($(this).attr("id"));
        });
    });

    // register function for button that ensures zooming out.
    $("#human-defects #body-div #zoom-out-button").on("click", function() {
        document.querySelector('img.zoom').dispatchEvent(new CustomEvent('wheelzoom.reset'));
        movePoints();
    });
});