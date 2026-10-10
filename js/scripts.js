function initRenstate() {
    "use strict";
    $(".loader-wrap").hide();
    $("#main").css("opacity", "1");
    //   Background image ------------------
    var a = $(".bg");
    a.each(function (a) {
        if ($(this).attr("data-bg")) $(this).css("background-image", "url(" + $(this).data("bg") + ")");
    });
    //   Isotope------------------
    function n() {
        if ($(".gallery-items").length) {
            var a = $(".gallery-items").isotope({
                singleMode: true,
                columnWidth: ".grid-sizer, .grid-sizer-second, .grid-sizer-three",
                itemSelector: ".gallery-item, .gallery-item-second, .gallery-item-three",
                singleMode: true,
                transformsEnabled: true,
                transitionDuration: "900ms"
            });
            a.imagesLoaded(function () {
                a.isotope("layout");
            });
            $(".gallery-filters").on("click  ", "a.gallery-filter", function (b) {
                b.preventDefault();
                var c = $(this).attr("data-filter"),
                    d = $(this).text();
                a.isotope({
                    filter: c
                });
                $(".gallery-filters a").removeClass("gallery-filter-active");
                $(this).addClass("gallery-filter-active");
            });
            var jk = $(".gallery-item").length;
            $(".all-album , .num-album").html(jk);
            a.isotope("on", "layoutComplete", function (a, b) {
                var b = a.length;
                $(".num-album").html(b);
            });
        }
    }
    n();
    // function postGrid() {
    //     if ($(".post-items").length) {
    //         var $grid2 = $(".post-items").isotope({
    //             singleMode: true,
    //             columnWidth: ".grid-sizer, .grid-sizer-second, .grid-sizer-three",
    //             itemSelector: ".post-item",
    //         });
    //         $grid2.imagesLoaded(function () {
    //             $grid2.isotope("layout");
    //         });
    //     }
    // }
    // postGrid();
    function lg() {
        if ($(".listing-grid").length) {
            var a = $(".listing-grid").isotope({
                singleMode: true,
                columnWidth: ".grid-sizer, .grid-sizer-second, .grid-sizer-three",
                itemSelector: ".listing-grid-item",
                singleMode: true,
                transformsEnabled: true,
                transitionDuration: "700ms"
            });
            a.imagesLoaded(function () {
                a.isotope("layout");
            });
            $(".gallery-filters").on("click  ", "a.gallery-filter", function (b) {
                b.preventDefault();
                var c = $(this).attr("data-filter"),
                    d = $(this).text();
                a.isotope({
                    filter: c
                });
                $(".gallery-filters a").removeClass("gallery-filter-active");
                $(this).addClass("gallery-filter-active");
            });
        }
    }
    lg();
    //   accordion ------------------
    $(".accordion a.toggle").on("click", function (a) {
        a.preventDefault();
        $(".accordion a.toggle").removeClass("act-accordion");
        $(this).addClass("act-accordion");
        if ($(this).next('div.accordion-inner').is(':visible')) {
            $(this).next('div.accordion-inner').slideUp();
        } else {
            $(".accordion a.toggle").next('div.accordion-inner').slideUp();
            $(this).next('div.accordion-inner').slideToggle();
        }
    });
    

    


    if ($(".clients-carousel").length > 0) {
        var ms2 = new Swiper(".clients-carousel .swiper-container", {
            loop: true,
            grabCursor: true,
            autoHeight: false,
            centeredSlides: false,
            slidesPerView: 4,
            spaceBetween: 20,
            speed: 2000,
            mousewheel: false,
            navigation: {
                nextEl: '.cc-button-next',
                prevEl: '.cc-button-prev',
            },
            autoplay: true,
            breakpoints: {
                1064: {
                    slidesPerView: 2,
                    spaceBetween: 10,
                },
                768: {
                    slidesPerView: 1,
                    spaceBetween: 0,
                },
            }
        });
    }
    if ($(".city-carousel").length > 0) {
        var ms2 = new Swiper(".city-carousel .swiper-container", {
            loop: true,
            grabCursor: true,
            autoHeight: false,
            centeredSlides: false,
            slidesPerView: 3,
            spaceBetween: 20,
            speed: 1400,
            parallax: true,
            navigation: {
                nextEl: '.csc-button-next',
                prevEl: '.csc-button-prev',
            },
            pagination: {
                el: '.city-pag-init',
                clickable: true,
            },
            breakpoints: {
                1064: {
                    slidesPerView: 2,
                    spaceBetween: 10,
                },
                768: {
                    slidesPerView: 1,
                    spaceBetween: 0,
                },
            }
        });
    }
    if ($(".synk-slider").length > 0) {
        var j2thumnails = new Swiper(".synk-slider-thumbnails .swiper-container", {
            preloadImages: false,
            slidesPerView: 3,
            spaceBetween: 10,
            loop: false,
            autoHeight: false,
            grabCursor: true,
            centeredSlides: true,
            direction: "vertical",
            navigation: {
                nextEl: '.ss-thumb_btn_next',
                prevEl: '.ss-thumb_btn_prev',
            },
        });
        var j24 = new Swiper(".synk-slider .swiper-container", {
            preloadImages: false,
            slidesPerView: 1,
            spaceBetween: 0,
            loop: false,

            autoHeight: true,
            grabCursor: true,
            centeredSlides: true,
            pagination: {
                el: '.synk-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.fw-carousel-button-next',
                prevEl: '.fw-carousel-button-prev',
            },
        });
        j24.controller.control = j2thumnails;
        j2thumnails.controller.control = j24;
    }
if ($(".single-carousel").length > 0) {
    var $carousel = $(".single-carousel");
    var $container = $carousel.find(".swiper-container");
    var totalSlides = $container.find(".swiper-slide").length;

    // Function to determine slidesPerView based on screen width
    function getSlidesPerView() {
        var w = window.innerWidth;
        if (w <= 768) return 1;
        if (w <= 1064) return 2;
        return 3;
    }

    var currentSlidesPerView = getSlidesPerView();
    var loopEnabled = totalSlides > currentSlidesPerView;

    // Add classes or hide arrows if not enough slides
    if (!loopEnabled) {
        $carousel.addClass("only-one-slide");
        $carousel.parent().addClass("only-one-slide-parent");

        // Hide arrows
        $carousel.find(".ss-carousel-button-prev").hide();
        $carousel.find(".ss-carousel-button-next").hide();
    }

    // Initialize Swiper
    var scw = new Swiper($container[0], {
        preloadImages: false,
        slidesPerView: 3,
        spaceBetween: 20,
        loop: loopEnabled,
        autoHeight: false,

        allowTouchMove: false,
        simulateTouch: false,
        grabCursor: false,

        mousewheel: false,
        pagination: {
            el: '.ss-carousel-pagination_init',
            clickable: true,
        },

        navigation: {
            nextEl: '.ss-carousel-button-next',
            prevEl: '.ss-carousel-button-prev',
        },

        breakpoints: {
            1064: {
                slidesPerView: 2,
                spaceBetween: 10,
            },
            768: {
                slidesPerView: 1,
                spaceBetween: 0,
                autoHeight: true,
            },
        }
    });

    // // Optional: re-check on resize for dynamic adjustments
    // $(window).on('resize', function () {
    //     // Reload or re-init logic (more advanced if needed)
    //     location.reload();
    // });
}

    if ($(".single-slider").length > 0) {
        var j2 = new Swiper(".single-slider .swiper-container", {
            preloadImages: false,
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            autoHeight: true,
            grabCursor: true,
            mousewheel: false,
            pagination: {
                el: '.ss-slider-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.fw-carousel-button-next',
                prevEl: '.fw-carousel-button-prev',
            },
        });
    }
    if ($(".testimonilas-carousel").length > 0) {
        var ms1 = new Swiper(".testimonilas-carousel .swiper-container", {
            loop: true,
            grabCursor: true,
            autoHeight: false,
            centeredSlides: true,
            slidesPerView: 3,
            spaceBetween: 20,
            speed: 1400,
            navigation: {
                nextEl: '.tc-button-next',
                prevEl: '.tc-button-prev',
            },
            pagination: {
                el: '.tcs-pagination_init',
                clickable: true,
            },
            breakpoints: {
                1064: {
                    slidesPerView: 2,
                    spaceBetween: 10,
                },
                768: {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    autoHeight: true,
                },
            }
        });
        var totalSlides2 = $(".testimonilas-carousel  .swiper-slide:not(.swiper-slide-duplicate)").length;
        $('.ts_total').html('0' + totalSlides2);
        ms1.on('slideChange', function () {
            var csli2 = ms1.realIndex + 1,
                curnum2 = $('.ts_current');
            curnum2.html('0' + csli2);
        });
    }
    if ($(".exc-listing-carousel").length > 0) {
        var ms1 = new Swiper(".exc-listing-carousel .swiper-container", {
            loop: true,
            grabCursor: true,
            autoHeight: false,
            centeredSlides: false,
            slidesPerView: 3,
            spaceBetween: 20,
            speed: 1400,
            navigation: {
                nextEl: '.tc-button-next',
                prevEl: '.tc-button-prev',
            },
            pagination: {
                el: '.tcs-pagination_init',
                clickable: true,
            },
            breakpoints: {
                1064: {
                    slidesPerView: 2,
                    spaceBetween: 10,
                },
                768: {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    autoHeight: true,
                },
            }
        });
        var totalSlides2 = $(".exc-listing-carousel .swiper-slide:not(.swiper-slide-duplicate)").length;
        $('.ts_total').html('0' + totalSlides2);
        ms1.on('slideChange', function () {
            var csli2 = ms1.realIndex + 1,
                curnum2 = $('.ts_current');
            curnum2.html('0' + csli2);
        });
    }
    $(window).on('load', function () {
    if ($(".fw-carousel").length > 0) {
        var ec = new Swiper(".fw-carousel .swiper-container", {
            preloadImages: false,
            lazy: {
                loadOnTransitionStart: true,
                loadPrevNext: true,
            },
            loop: true,
            centeredSlides: true,
            freeMode: false,
            slidesPerView: "auto",
            spaceBetween: 10,
            grabCursor: true,
            mousewheel: false,
            speed: 1400,
            navigation: {
                nextEl: '.fw-carousel-button-next',
                prevEl: '.fw-carousel-button-prev',
            },
            pagination: {
                el: '.fwc-pagination',
                clickable: true,
            },
        });
    }
    });

    if ($(".agnet-carousel").length > 0) {
        var ms1 = new Swiper(".agnet-carousel .swiper-container", {
            loop: true,
            grabCursor: true,
            autoHeight: false,
            centeredSlides: false,
            slidesPerView: 3,
            spaceBetween: 10,
            speed: 1400,
            navigation: {
                nextEl: '.ac-button-next',
                prevEl: '.ac-button-prev',
            },

            breakpoints: {
                1064: {
                    slidesPerView: 2,
                    spaceBetween: 10,
                },
                768: {
                    slidesPerView: 1,
                    spaceBetween: 0,
                },
            }
        });

    }
    if ($(".slideshow-container_wrap").length > 0) {
        $(".slider-progress-bar").addClass("act-slider");
        var ms1 = new Swiper(".slideshow-container_wrap .swiper-container", {
            preloadImages: false,
            loop: true,
            speed: 1400,
            spaceBetween: 0,
            effect: "fade",
            autoplay: {
                delay: 2500,
                disableOnInteraction: false
            },
            pagination: {
                el: '.slideshow-container-pag-init',
                clickable: true,
            },
        });

    }
    //   lightGallery------------------
    $(".image-popup , .single-popup-image").lightGallery({
        selector: "this",
        cssEasing: "cubic-bezier(0.25, 0, 0.25, 1)",
        download: false,
        counter: false,
        loop: true
    });
    $(".lightgallery").lightGallery({
        selector: ".lightgallery a.popup-image , .lightgallery  a.popgal",
        cssEasing: "cubic-bezier(0.25, 0, 0.25, 1)",
        download: false,
        loop: true,
        counter: false
    });
    $(".custom-gallery").lightGallery({
        selector: ".popup-image",
        cssEasing: "cubic-bezier(0.25, 0, 0.25, 1)",
        download: false,
        loop: true,
        counter: false
    });
    $('#career-video').lightGallery({
        selector: 'this',
        counter: false,
        download: false,
        zoom: false
    });
    $('#html5-videos , .inithtml5video').lightGallery({
        selector: 'this',
        counter: false,
        download: false,
        zoom: false
    });
    $(document).ready(function () {
        var vid_src = $(".popup_video").data("videolink");
        $(".lg-video-object source").attr("src", vid_src);
    });
    //   scrollToFixed ------------------	
    $(".fixed-form").scrollToFixed({
        minWidth: 1068,
        zIndex: 112,
        marginTop: 110,
        removeOffsets: true,
        limit: function () {
            var a = $(".limit-box").offset().top - $(".fixed-form").outerHeight(true) - 20;
            return a;
        }
    });
    $(".btf_init").scrollToFixed({
        minWidth: 768,
        marginTop: 100,
        removeOffsets: true,
        dontSetWidth: false,
        limit: function () {
            var a = $(".limit-box").offset().top - $(".btf_init").outerHeight() - 1;
            return a;
        },
        preFixed: function () {
            $(this).addClass("vis_btf")
        },
        postFixed: function () {
            $(this).removeClass("vis_btf")
        }
    });
    if ($(".fixed-bar").outerHeight(true) < $(".post-container").outerHeight(true)) {
        $(".fixed-bar").addClass("fixbar-action");
        $(".fixbar-action").scrollToFixed({
            minWidth: 1068,
            marginTop: function () {
                var a = $(window).height() - $(".fixed-bar").outerHeight(true) - 120;
                if (a >= 0) return 20;
                return a;
            },
            removeOffsets: true,
            limit: function () {
                var a = $(".limit-box").offset().top - $(".fixed-bar").outerHeight();
                return a;
            }
        });
    } else $(".fixed-bar").removeClass("fixbar-action");
    $(".init-fix-column").scrollToFixed({
        minWidth: 1068,
        marginTop: 110,
        removeOffsets: true,
        limit: function () {
            var a = $(".limit-box").offset().top - $(".init-fix-column").outerHeight(true);
            return a;
        }
    });
    //   appear------------------
    $(".stats").appear(function () {
        $(".num").countTo();
    });
    $(".graph-price").appear(function () {
        $(".graph-price").find(".graph").each(function () {
            $(this).find(".gil").delay(600).animate({
                height: $(this).attr("data-percent")
            }, 1000);
        });
    });
    // share------------------	
    $(".share-container").share({
        networks: ['facebook', 'pinterest', 'tumblr', 'twitter', 'linkedin']
    });
    //   Video------------------	
    if ($(".video-holder-wrap").length > 0) {
        function videoint() {
            var w = $(".background-vimeo").data("vim"),
                bvc = $(".background-vimeo"),
                bvmc = $(".media-container"),
                bvfc = $(".background-vimeo iframe "),
                vch = $(".video-container");
            bvc.append('<iframe src="//player.vimeo.com/video/' + w + '?background=1"  frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen ></iframe>');
            $(".video-holder").height(bvmc.height());
            if ($(window).width() > 1024) {
                if ($(".video-holder").length > 0)
                    if (bvmc.height() / 9 * 16 > bvmc.width()) {
                        bvfc.height(bvmc.height()).width(bvmc.height() / 9 * 16);
                        bvfc.css({
                            "margin-left": -1 * $("iframe").width() / 2 + "px",
                            top: "-75px",
                            "margin-top": "0px"
                        });
                    } else {
                        bvfc.width($(window).width()).height($(window).width() / 16 * 9);
                        bvfc.css({
                            "margin-left": -1 * $("iframe").width() / 2 + "px",
                            "margin-top": -1 * $("iframe").height() / 2 + "px",
                            top: "50%"
                        });
                    }
            } else if ($(window).width() < 760) {
                $(".video-holder").height(bvmc.height());
                bvfc.height(bvmc.height());
            } else {
                $(".video-holder").height(bvmc.height());
                bvfc.height(bvmc.height());
            }
            vch.css("width", $(window).width() + "px");
            vch.css("height", Number(720 / 1280 * $(window).width()) + "px");
            if (vch.height() < $(window).height()) {
                vch.css("height", $(window).height() + "px");
                vch.css("width", Number(1280 / 720 * $(window).height()) + "px");
            }
        }
        videoint();
    }
    $(".custom-scroll-link").on("click", function () {
        var a = 70;
        if (location.pathname.replace(/^\//, "") == this.pathname.replace(/^\//, "") || location.hostname == this.hostname) {
            var b = $(this.hash);
            b = b.length ? b : $("[name=" + this.hash.slice(1) + "]");
            if (b.length) {
                $("html,body").animate({
                    scrollTop: b.offset().top - a
                }, {
                    queue: false,
                    duration: 1200,
                    easing: "easeInOutExpo"
                });
                return false;
            }
        }
    });
    $(".to-top").on("click", function (a) {
        a.preventDefault();
        $("html, body").animate({
            scrollTop: 0
        }, 800);
        return false;
    });
    //   Contact form------------------
    $("#contactform").submit(function () {
        var a = $(this).attr("action");
        $("#message").slideUp(750, function () {
            $("#message").hide();
            $("#submit").attr("disabled", "disabled");
            $.post(a, {
                name: $("#name").val(),
                email: $("#email").val(),
                comments: $("#comments").val()
            }, function (a) {
                document.getElementById("message").innerHTML = a;
                $("#message").slideDown("slow");
                $("#submit").removeAttr("disabled");
                if (null != a.match("success")) $("#contactform").slideDown("slow");
            });
        });
        return false;
    });
    $("#contactform input, #contactform textarea").keyup(function () {
        $("#message").slideUp(1500);
    });
    function cardRaining() {
        $.fn.duplicate = function (a, b) {
            var c = [];
            for (var d = 0; d < a; d++) $.merge(c, this.clone(b).get());
            return this.pushStack(c);
        };
        var cr = $(".star-rating");
        cr.each(function (cr) {
            var starcount = $(this).attr("data-starrating");
            $("<i class='fa-solid fa-star'></i>").duplicate(starcount).prependTo(this);
        });
    }
    cardRaining();
    function csselem() {
        $(".height-emulator").css({
            height: $(".main-footer").outerHeight(true)
        });
    }
    csselem();
    // Mob Menu------------------
    $(".nav-button-wrap").on("click", function () {
        $(".main-menu").toggleClass("vismobmenu");
        $(this).toggleClass("vis_nbwc");
        $('.mob-nav-overlay').fadeToggle(200);
    });
    $(".mob-nav-overlay").on("click", function () {
        $(".main-menu").removeClass("vismobmenu");
        $(".nav-button-wrap").removeClass("vis_nbwc");
        $(this).fadeOut(200);
    });
    function mobMenuInit() {
        var ww = $(window).width();
        if (ww < 1068) {
            $(".menusb").remove();
            $(".main-menu").removeClass("nav-holder");
            $(".main-menu nav").clone().addClass("menusb").appendTo(".main-menu");
            $(".menusb").menu();
            $(".menusb.scroll-init a").on("click", function () {
                $(".main-menu").removeClass("vismobmenu");
            });
        } else {
            $(".menusb").remove();
            $(".main-menu").addClass("nav-holder");
        }
    }
    mobMenuInit();
    //   css ------------------
    var $window = $(window);
    $window.on("resize", function () {
        csselem();
        mobMenuInit();
    });
    //  forms ------------------		
    $('.chosen-select').niceSelect();
    // date picker------------------
    $('input[name="datepicker-here"]').daterangepicker({
        autoUpdateInput: false,
        parentEl: $(".date-container"),
        singleDatePicker: true,

        locale: {
            cancelLabel: 'Clear'
        }
    });
    $('input[name="datepicker-here-time"]').daterangepicker({
        autoUpdateInput: false,
        parentEl: $(".date-container2"),
        singleDatePicker: true,
        timePicker: true,
        locale: {
            cancelLabel: 'Clear'
        }
    });
    $('input[name="datepicker-here-time"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY hh:mm A'));
    });
    $('input[name="datepicker-here-time"]').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });
    $('input[name="datepicker-here"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY'));
    });
    $('input[name="datepicker-here"]').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });
    $('input[name="dates"]').daterangepicker({
        autoUpdateInput: false,
        parentEl: $(".date-container3"),
        locale: {
            cancelLabel: 'Clear'
        }
    });
    $('input[name="dates"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });
    $('input[name="dates"]').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });
    var sliders_init = $(".price-range");
    sliders_init.ionRangeSlider({
        type: "single",
    });
    var sliders_init2 = $(".price-range-double");
    sliders_init2.ionRangeSlider({
        type: "double",
    });
    $('.quantity-item').each(function () {
        var spinner = $(this),
            input = spinner.find('input[type="text"]'),
            btnUp = spinner.find('.plus'),
            btnDown = spinner.find('.minus'),
            min = input.attr('data-min'),
            max = input.attr('data-max');
        btnUp.click(function () {
            var oldValue = parseFloat(input.val());
            if (oldValue >= max) {
                var newVal = oldValue;
            } else {
                var newVal = oldValue + 1;
            }
            spinner.find("input.qty").val(newVal);
            spinner.find("input.qty").trigger("change");
        });
        btnDown.click(function () {
            var oldValue = parseFloat(input.val());
            if (oldValue <= min) {
                var newVal = oldValue;
            } else {
                var newVal = oldValue - 1;
            }
            spinner.find("input.qty").val(newVal);
            spinner.find("input.qty").trigger("change");
        });
    });
    $(".show-mob-filter").on("click", function () {
        $(".lws_mobile").animate({
            left: 0
        }, 600);

        $(".mob-filter-overlay").fadeIn(100);
    });
    $(".cmf").on("click", function () {
        $(".lws_mobile").animate({
            left: "-500px"
        }, 600);
        $(".mob-filter-overlay").fadeOut(100);
    });
    $(".more_search-btn").on("click", function () {
        $(".hidden-listing-filter").fadeToggle(500);
        $(this).toggleClass("mfilopact");
    });
    $('.tariff-toggle').on("click", function () {
        if ($('#yearly-1').is(':checked')) {
            $('.price-item').addClass('year-mont');
        }
        if ($('#monthly-1').is(':checked')) {
            $('.price-item').removeClass('year-mont');
        }
    });
    $(".view-pass").on("click touchstart", function () {
        var epi = $(this).parent(".pass-input-wrap").find("input");
        if (epi.attr("type") === "password") {
            epi.attr("type", "text");
        } else {
            epi.attr("type", "password");
        }
    });
    $('.fuzone input').each(function () {
        $(this).on('change', function () {
            var pufzone = $(this).parents(".fuzone").find('.photoUpload-files');
            pufzone.empty();
            var files = $(this)[0].files;
            for (var i = 0; i < files.length; i++) {
                $("<span></span>").text(files[i].name).appendTo(pufzone);
            }
        });
    });
    var $window = $(window);
    $window.scroll(function () {
        var a = $(document).height();
        var b = $(window).height();
        var c = $(this).scrollTop();
        var d = c / (a - b) * 100;
        $(".progress-bar").css({
            width: d + "%"
        });
        if ($(this).scrollTop() > 150) {
            $("header.header_half , .logo-holder").addClass("scroll-sticky");
        } else {
            $("header.header_half , .logo-holder").removeClass("scroll-sticky");
        }
    });
    var hsb = $(".header-search-btn"),
        hsw = $(".header-search-wrap"),
        swl = $(".swl");
    function showSearch() {
        hsw.fadeIn(1).addClass("vis-search").removeClass("novis_search");
        hsb.addClass("hsbclose");
        $(".search-form-overlay").fadeIn(300)
    }
    function hideSearch() {
        hsw.removeClass("vis-search").addClass("novis_search");
        hsb.removeClass("hsbclose");
        $(".search-form-overlay").fadeOut(300)
        hsw.delay(300).fadeOut(1);
    }
    hsb.on("click", function () {
        if (hsw.hasClass("novis_search")) showSearch();
        else hideSearch();
    });

    $(".close-search-form").on("click", function () {
        hideSearch();
    });
    function showWisllist() {
        $(".wish-list-wrap ").addClass("vis-wishlist");
        $(".wishlist-wrap-overlay").fadeIn(300);

    }
    function hideWisllist() {
        $(".wish-list-wrap ").removeClass("vis-wishlist");
        $(".wishlist-wrap-overlay").fadeOut(300);
    }
    $(".swl_btn").on("click", function () {
        $(".wish-list-wrap ").fadeIn(1).addClass("vis-wishlist");
        $(".wishlist-wrap-overlay").fadeIn(300);
        hideSearch();
    });
    $(".clwl_btn").on("click", function () {
        hideWisllist();
    });
    $(".modal-open").on("click", function () {
        showModal();
    });
    function showModal() {
        $(".main-register-container").css('display', 'flex');
        setTimeout(function () {
            $(".main-register-wrap").addClass("vis_mr");
        }, 200);
        $(".reg-overlay").fadeIn(200);
        hideSearch();
    }
    function hideModal() {
        $(".reg-overlay").delay(200).fadeOut(200);
        $(".main-register-wrap").removeClass("vis_mr");
        setTimeout(function () {
            $(".main-register-container").css('display', 'none');
        }, 400);
    }
    $(".close-reg-form").on("click", function () {
        hideModal();
    });
    
    // loan moadal
    $(".loan-modal-open").on("click", function () {
        showLoanModal();
    });
    
    function showLoanModal() {
        $(".loan-register-container").css('display', 'flex');
        setTimeout(function () {
            $(".loan-register-wrap").addClass("vis_mr");
        }, 200);
        $(".loan-overlay").fadeIn(200);
        hideSearch(); // assuming this is defined globally
    }
    
    function hideLoanModal() {
        $(".loan-overlay").delay(200).fadeOut(200);
        $(".loan-register-wrap").removeClass("vis_mr");
        setTimeout(function () {
            $(".loan-register-container").css('display', 'none');
        }, 400);
    }
    
    $(".close-loan-form").on("click", function () {
        hideLoanModal();
    });
    
    //more filter 1 modal
    $(".f1-modal-open").on("click", function () {
        showFModal();
    });

    function showFModal() {
        $(".filterh1-container").css('display', 'flex');
        setTimeout(function () {
            $(".filterh1-wrap").addClass("vis_mr");
        }, 200);
        $(".filterh1-overlay").fadeIn(200);
        hideSearch(); // assuming this is defined globally
    }

    function hideF1Modal() {
        $(".filterh1-overlay").delay(200).fadeOut(200);
        $(".filterh1-wrap").removeClass("vis_mr");
        setTimeout(function () {
            $(".filterh1-container").css('display', 'none');
        }, 400);
    }

    $(".close-filterh1-form").on("click", function () {
        hideF1Modal();
    });

    //more filter 2 modal
    $(".f2-modal-open").on("click", function () {
        showF2Modal();
    });

    function showF2Modal() {
        $(".filterh2-container").css('display', 'flex');
        setTimeout(function () {
            $(".filterh2-wrap").addClass("vis_mr");
        }, 200);
        $(".filterh2-overlay").fadeIn(200);
        hideSearch(); // assuming this is defined globally
    }

    function hideF2Modal() {
        $(".filterh2-overlay").delay(200).fadeOut(200);
        $(".filterh2-wrap").removeClass("vis_mr");
        setTimeout(function () {
            $(".filterh2-container").css('display', 'none');
        }, 400);
    }

    $(".close-filterh2-form").on("click", function () {
        hideF2Modal();
    });


    // tabs ------------------
    $(".tabs-menu a").on("click", function (a) {
        a.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var b = $(this).attr("href");
        $(this).parents(".tabs-act").find(".tab-content").not(b).css("display", "none");
        $(b).fadeIn();
    });
    $(".show-messenger-links").on("click", function (ev) {
        ev.preventDefault();
        $(this).parents(".sh-links").find(".messenger-links-container").toggleClass("mlc_vis");
    });
    //   mailchimp------------------
    $("#subscribe").ajaxChimp({
        language: "eng",
        url: "https://gmail.us1.list-manage.com/subscribe/post?u=1fe818378d5c129b210719d80&amp;id=a2792f681b"
    });
    //   scrollnav  ------------------
    $(".scroll-init ul").singlePageNav({
        filter: ":not(.external)",
        updateHash: false,
        offset: 120,
        threshold: 0,
        speed: 1200,
        currentClass: "act-scrlink"
    });
    //   Parallax ------------------
    var b = new Scrollax();
    b.reload();
    b.init();
    $('.svg-corner_dark').append('<svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M40 40V0C40 22.0914 22.0914 40 0 40H40Z" fill="#000000"></path></svg>');
    $('.svg-corner_white').append('<svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M40 40V0C40 22.0914 22.0914 40 0 40H40Z" fill="#ffffff"></path></svg>');
    var maxLp = 130
    // $(".geodir-category-content p").each(function (i, div) {
    //     var textp = $(div).text();
    //     if (textp.length > maxLp) {
    //         var beginp = textp.substr(0, maxLp),
    //             end = textp.substr(maxLp);
    //         $(div).html(beginp).append($('<div class="hidden" />').html(end));
    //     }
    // });
    var extraChars = 20; // Increase this value to show more text initially

  



    // $(document).on("click", ".read-more", function (e) {
    //     e.preventDefault();
    //     var $this = $(this);
    //     $this.siblings(".more-text").removeClass("hidden");
    //     $this.siblings(".dots").remove();
    //     $this.remove();
    // });

    function heroAnim() {
        function a(a) {
            var b = a.length,
                c, d;
            while (b) {
                d = Math.floor(Math.random() * b--);
                c = a[b];
                a[b] = a[d];
                a[d] = c;
            }
            return a;
        }
        var b = $(".api-bg-pin");
        $(a(b).slice(0, $(".api-wrap-bg").data("ran"))).each(function (a) {
            var bc = $(this);
            b.removeClass("api-bg-pin-vis")
            bc.addClass("api-bg-pin-vis");

        });
    }
    setInterval(function () {
        heroAnim();
    }, 3000);

}
$('head').append('<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">');
document.addEventListener('gesturestart', function (e) {
    e.preventDefault();
});
//   Init All ------------------
$(document).ready(function () {
    initRenstate();
});
// --- Tab switching logic for property search ---
const tabButtons = document.querySelectorAll('.tab-btn-custom');
const tabContents = document.querySelectorAll('.tab-content-custom');

tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        tabButtons.forEach(b => b.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));

        btn.classList.add('active');
        document.getElementById(`tab-${btn.dataset.tab}`).classList.add('active');
    });
});
function openFilterPopup() {
    document.getElementById("fullScreenFilter").style.display = "flex";
}

function closeFilterPopup() {
    document.getElementById("fullScreenFilter").style.display = "none";
}
// INT TEL INPUT
document.addEventListener("DOMContentLoaded", function () {
    const inputs = document.querySelectorAll(".phone-input");

    inputs.forEach(input => {
        var iti = window.intlTelInput(input, {
            nationalMode: true,
            preferredCountries: ['ae'],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.16/build/js/utils.js",
        });
    });
});
document.addEventListener("DOMContentLoaded", function() {
    if (window.location.href.indexOf('gallery.php') !== -1) {
        document.body.classList.add('hide-lg-buttons');
    }
});
$(document).ready(function () {
    function updateHeaderStyle() {
        var scroll = $(window).scrollTop();
        if (scroll > 10) {
            $(".main-header").css({
                "background": "#ab9889",
            });
        } else {
            $(".main-header").css({
                "background": "none",
            });
        }
    }

    $(window).scroll(updateHeaderStyle);

    updateHeaderStyle();
});