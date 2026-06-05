$(function () {
    //HELLOBAR CONTROL
    $(window).load(function () {
        var HelloKey = window.location.href;

        $.post(BASE + "/assets/widgets/hellobar/hellobar.ajax.php", {url: HelloKey}, function (data) {
            //EFFECTS
            if (data.hello_position === 'center') {
                $("body").prepend(data.hello);

                setTimeout(function () {
                    $(".wc_hellobar").fadeIn(function () {
                        $(".wc_hellobar_box").animate({'opacity': 1, 'top': '100'}, 400);
                        wc_helloclose();
                    });
                }, 1000);
            }
            if (data.hello_position === 'right_top' || data.hello_position === 'right_bottom') {
                $("body").prepend(data.hello);

                setTimeout(function () {
                    $(".wc_hellobar").fadeIn(function () {
                        $(".wc_hellobar").animate({'opacity': 1, 'right': '20'}, 400);
                        wc_helloclose();
                    });
                }, 1000);
            }
            //TRACKING
            $(".wc_hellobar_cta").click(function () {
                var HellobarId = $(this).attr("id");
                var HellobarLink = $(this).attr("href");

                $("#" + HellobarId + ".wc_hellobar").fadeOut(50);
                $.post(BASE + "/assets/widgets/hellobar/hellobar.ajax.php", {
                    action: 'track',
                    hello: HellobarId
                }, function () {
                    window.open(HellobarLink);
                });
                return false;
            });
        }, 'json');
    });
});

//HELLO CLOSE
function wc_helloclose() {
    $(".wc_hellobar_close").click(function () {
        var HelloBar = $(this).attr("id");
        $("#" + HelloBar + ".wc_hellobar").fadeOut();
    });
}
