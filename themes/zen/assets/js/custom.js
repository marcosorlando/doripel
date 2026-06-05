(function () {
    "use strict";
    var Core = {
        initialized: false,
        initialize: function () {
            
            if (this.initialized)
                return;
            this.initialized = true;
            this.build();
        },
        build: function () {
            // Bind critical UI toggles first, even if plugin init fails later.
            this.toggleSearch();
            this.toggleAccount();
            // Dropdown menu
            this.cotentslide();
            // Owl carousel init
            this.initOwlCarousel();
            // Stick slider init
            this.initStickSlider();
            // Fixed header
            this.fixedHeader();
            // Progress bar animation
            this.progressBarAnimation();
            // Wow init
            this.wowInit();
            // Loader
            this.loaderInit();
            // Start video
            this.startVideo();
            // Top slider init
            this.initSliderPro();
            // Init fancybox
            this.initFancyBox();
            // Init fancybox video
            this.initFancyBoxVideo();
            
        },
        initFancyBox: function () {
            $('.fancybox').fancybox();
        },
        initFancyBoxVideo: function () {
            $(".fancybox-video").click(function () {
                $.fancybox({
                    'padding': 0,
                    'autoScale': false,
                    'transitionIn': 'none',
                    'transitionOut': 'none',
                    'title': this.title,
                    'width': 680,
                    'height': 495,
                    'href': this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
                    'type': 'swf',
                    'swf': {
                        'wmode': 'transparent',
                        'allowfullscreen': 'true'
                    }
                });
                
                return false;
            });
        },
        cotentslide: function (options) {
            
            var scrollPane = $(".scroll-pane"),
                scrollContent = $(".scroll-content");
            //build slider
            var scrollbar = $(".scroll-bar").slider({
                slide: function (event, ui) {
                    if (scrollContent.width() > scrollPane.width()) {
                        scrollContent.css("margin-left", Math.round(
                            ui.value / 100 * (scrollPane.width() - scrollContent.width())
                        ) + "px");
                    } else {
                        scrollContent.css("margin-left", 0);
                    }
                }
            });
            //append icon to handle
            var handleHelper = scrollbar.find(".ui-slider-handle")
            .mousedown(function () {
                scrollbar.width(handleHelper.width());
            })
            .mouseup(function () {
                scrollbar.width("100%");
            })
            .append("<span class='ui-icon ui-icon-grip-dotted-vertical'></span>")
            .wrap("<div class='ui-handle-helper-parent'></div>").parent();
            //change overflow to hidden now that slider handles the scrolling
            scrollPane.css("overflow", "hidden");
            
            //size scrollbar and handle proportionally to scroll distance
            function sizeScrollbar() {
                var remainder = scrollContent.width() - scrollPane.width();
                var proportion = remainder / scrollContent.width();
                var handleSize = scrollPane.width() - (proportion * scrollPane.width());
                scrollbar.find(".ui-slider-handle").css({
                    width: handleSize,
                    "margin-left": -handleSize / 2
                });
                handleHelper.width("").width(scrollbar.width() - handleSize);
            }
            
            //change handle position on window resize
            $(window).resize(function () {
                sizeScrollbar();
            });
            //init scrollbar size
            setTimeout(sizeScrollbar, 10); //safari wants a timeout
            
        },
        initStickSlider: function (options) {
            $(".enable-stick-slider").each(function (i) {
                var $stick = $(this);
                $stick.slick({
                    responsive: [
                        {
                            breakpoint: 500,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
            });
            
        },
        initOwlCarousel: function (options) {
            
            $(".enable-owl-carousel").each(function (i) {
                var $owl = $(this);
                var navigationData = $owl.data('navigation');
                var paginationData = $owl.data('pagination');
                var singleItemData = $owl.data('single-item');
                var autoPlayData = $owl.data('auto-play');
                var transitionStyleData = $owl.data('transition-style');
                var mainSliderData = $owl.data('main-text-animation');
                var afterInitDelay = $owl.data('after-init-delay');
                var stopOnHoverData = $owl.data('stop-on-hover');
                var min600 = $owl.data('min600');
                var min800 = $owl.data('min800');
                var min1200 = $owl.data('min1200');
                $owl.owlCarousel({
                    navigation: navigationData,
                    pagination: paginationData,
                    singleItem: singleItemData,
                    autoPlay: autoPlayData,
                    transitionStyle: transitionStyleData,
                    stopOnHover: stopOnHoverData,
                    navigationText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
                    itemsCustom: [
                        [0, 1],
                        [600, min600],
                        [800, min800],
                        [1200, min1200]
                    ],
                    afterInit: function (elem) {
                        if (mainSliderData) {
                            setTimeout(function () {
                                $('.main-slider_zoomIn').css('visibility', 'visible').removeClass('zoomIn').addClass('zoomIn');
                                $('.main-slider_fadeInLeft').css('visibility', 'visible').removeClass('fadeInLeft').addClass('fadeInLeft');
                                $('.main-slider_fadeInLeftBig').css('visibility', 'visible').removeClass('fadeInLeftBig').addClass('fadeInLeftBig');
                                $('.main-slider_fadeInRightBig').css('visibility', 'visible').removeClass('fadeInRightBig').addClass('fadeInRightBig');
                            }, afterInitDelay);
                        }
                    },
                    beforeMove: function (elem) {
                        if (mainSliderData) {
                            $('.main-slider_zoomIn').css('visibility', 'hidden').removeClass('zoomIn');
                            $('.main-slider_slideInUp').css('visibility', 'hidden').removeClass('slideInUp');
                            $('.main-slider_fadeInLeft').css('visibility', 'hidden').removeClass('fadeInLeft');
                            $('.main-slider_fadeInRight').css('visibility', 'hidden').removeClass('fadeInRight');
                            $('.main-slider_fadeInLeftBig').css('visibility', 'hidden').removeClass('fadeInLeftBig');
                            $('.main-slider_fadeInRightBig').css('visibility', 'hidden').removeClass('fadeInRightBig');
                        }
                    },
                    afterMove: sliderContentAnimate,
                    afterUpdate: sliderContentAnimate,
                });
            });
            
            function sliderContentAnimate(elem) {
                var $elem = elem;
                var afterMoveDelay = $elem.data('after-move-delay');
                var mainSliderData = $elem.data('main-text-animation');
                if (mainSliderData) {
                    setTimeout(function () {
                        $('.main-slider_zoomIn').css('visibility', 'visible').addClass('zoomIn');
                        $('.main-slider_slideInUp').css('visibility', 'visible').addClass('slideInUp');
                        $('.main-slider_fadeInLeft').css('visibility', 'visible').addClass('fadeInLeft');
                        $('.main-slider_fadeInRight').css('visibility', 'visible').addClass('fadeInRight');
                        $('.main-slider_fadeInLeftBig').css('visibility', 'visible').addClass('fadeInLeftBig');
                        $('.main-slider_fadeInRightBig').css('visibility', 'visible').addClass('fadeInRightBig');
                    }, afterMoveDelay);
                }
            }
        },
        fixedHeader: function (options) {
            if ($(window).width() > 767) {
                // Fixed Header
                var topOffset = $(window).scrollTop();
                if (topOffset > 0) {
                    $('body').addClass('fixed-header');
                }
                $(window).on('scroll', function () {
                    var fromTop = $(this).scrollTop();
                    if (fromTop > 0) {
                        $('body').addClass('fixed-header');
                    } else {
                        $('body').removeClass('fixed-header');
                    }
                    
                });
            }
        },
        progressBarAnimation: function (options) {
            $('.skills').waypoint(function () {
                $('.skills-animated').each(function () {
                    var persent = $(this).attr('data-percent');
                    $(this).find('.progress').animate({
                        width: persent + '%'
                    }, 300);
                });
            }, {
                offset: '100%',
                triggerOnce: true
            });
        },
        wowInit: function () {
            var scrollingAnimations = $('body').data("scrolling-animations");
            if (scrollingAnimations) {
                new WOW().init();
            }
        },
        loaderInit: function () {
            $(window).on('load', function () {
                var $preloader = $('#page-preloader'),
                    $spinner = $preloader.find('.spinner');
                $spinner.fadeOut();
                $preloader.delay(350).fadeOut(800);
            });
        },
        startVideo: function () {
            if (typeof Modernizr === 'undefined' || !Modernizr.touch) {
                $(".video-play").mb_YTPlayer();
            }
        },
        toggleSearch: function () {
            $(document).on('click', "#search-open, #search-close", function (e) {
                e.preventDefault();
                $('.header').toggleClass('search-open');
            });
            window.__zenSearchHandlerBound = true;
        },
        toggleAccount: function () {
            $(document).on('click', "#account-open, #account-close", function () {
                $('.header').toggleClass('account-open');
            });
        },
        initSliderPro: function () {
            if ($('#topSlider').length > 0) {
                
                $('#topSlider').sliderPro({
                    width: 1600,
                    height: 800,
                    fade: true,
                    arrows: true,
                    buttons: false,
                    waitForLayers: true,
                    thumbnailPointer: false,
                    touchSwipe: false,
                    autoplay: true,
                    autoScaleLayers: false,
                    captionFadeDuration: 100
                    
                });
                
            }
        }
    };
    Core.initialize();
})();

//MASCARA TELEFONE CONTATO
function mask_fone(field) {
    if (document.getElementById(field).value.length === 2) {
        document.getElementById(field).value = "(" + document.getElementById(field).value + ") ";
    }
    if (document.getElementById(field).value.length === 10) {
        document.getElementById(field).value = document.getElementById(field).value + "-";
    }
}

//PAGINAÇÃO - TRAMPOS
$('html, body').on('click', '.j_control', function (e) {
    var Prevent = $(this);
    var ControlId = $(this).attr('id');
    var Link_atual = $(this).attr('rel');
    var Callback = $(this).attr('callback');
    var Callback_action = $(this).attr('callback_action');
    var WcAjax = $("link[rel='base']").attr('href') + "/themes/new/ajax/" + Callback + '.ajax.php';
    
    $.post(WcAjax, {
        callback: Callback,
        callback_action: Callback_action,
        controll_id: ControlId,
        link_atual: Link_atual
    }, function (data) {
        
        if (data.redirect) {
            $('.workcontrol_upload').fadeIn().css('display', 'flex');
            window.setTimeout(function () {
                window.location.href = data.redirect;
                if (window.location.hash) {
                    window.location.reload();
                }
            }, 500);
        }
        //CONTENT UPDATE
        if (data.content) {
            $('.j_content').fadeTo('300', '0.5', function () {
                $(this).html(data.content).fadeTo('300', '1');
            });
        }
        //INPUT CLEAR
        if (data.inpuval) {
            $('.wc_value').val(data.inpuval);
        }
        //DINAMIC CONTENT
        if (data.divcontent) {
            $(data.divcontent[0]).html(data.divcontent[1]);
        }
    }, 'json');
    
    e.preventDefault();
    e.stopPropagation();
});

//CAROUSSEL RESPONSIVO - CLIENTES
$('.responsive').slick({
    dots: true,
    infinite: false,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 4,
    responsive: [
        {
            breakpoint: 1024,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 4,
                infinite: true,
                dots: true
            }
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});

//ALERTA LINKS SISTEMAS - DEMOS
$("#tabDesign a").click(function () {
    alert("Ooppps! Volte mais tarde, area em manutenção no momento.");
    $("#tabDesign a").attr({href: "#tabDesign", target: ""});
});
