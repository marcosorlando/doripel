//$("body").focus();
$(function () {
    $('.link-alto-contraste').click(function () {

        let classActive = $(this).attr('data-class')
        $('body').removeClass().addClass(classActive).fadeIn('fast');

        Cookies.remove('Contrast_Active')
        Cookies.set('Contrast_Active', classActive, {expires: 30})
        console.log(Cookies.get())
    });

    $(document).ready(function () {
        let cookieValue = Cookies.get('Contrast_Active')
        let classAtive = document.body.className

        if (cookieValue != classAtive) {
            $('body').removeClass().addClass(cookieValue);
        }
            $('* img').addClass('on-contrast-force-gray');

    });

    /*Visualizar senha*/
    $(".toggle-password").on("click mouseover", function () {
        $(this).toggleClass("fa-eye fa-eye-slash");

        var input = $('#' + $(this).attr("toggle"));

        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
});
// CONTRASTE END
