

$(window).on('load', function () {
     /* --------------------------------------------
     SECURITY CHECK HUMAN
    -------------------------------------------- */
    if($("#senderHuman").length > 0 ) {
        var a = Math.ceil(Math.random() * 10) + 1;
        var b = Math.ceil(Math.random() * 10) + 1;
        document.getElementById("senderHuman").placeholder = a +" + "+ b +" = ?";
        document.getElementById("checkHuman_a").value = a;
        document.getElementById("checkHuman_b").value = b;
    }
});