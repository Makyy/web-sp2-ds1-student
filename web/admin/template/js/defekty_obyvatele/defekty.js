function movePoints(){

    console.log("start with points moving");

    var imgHeight = $("#body-img").height();
    var imgWidth = $("#body-img").width();

    // tohle cely kurva nefunguje
    $(".human-label").each(function () {

        // neumi to precist customize atribut ratiotop & ratioright - wtf nevim co za ma problem
        var newTop = ($(this).attr("ratiotop") * imgHeight);
        var newLeft = ($(this).attr("ratioright")* imgWidth);

        // nenastavi to styl - dela to hovno
        $(this).css({"top" : newTop + "px"});
        $(this).css({"left" : newLeft + "px"});

        console.log("top & left adjusted");
    });
}