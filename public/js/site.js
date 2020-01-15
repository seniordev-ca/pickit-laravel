/**
 * Title:   Dashcore - HTML App Landing Page - Main Javascript file
 * Author:  http://themeforest.net/user/5studiosnet
 **/


// Place any code in here.
$(function() {
    'use strict';

    /** navbar reference **/
    var $navbar = $(".main-nav"),
        stickyPoint = 90;

    /** Perspective mockups reference **/
    var $perspectiveMockups = $(".perspective-mockups");

    // This element is used as reference for relocation of the mockups on mobile devices.
    // If you remove it please be sure you add another reference element preferably within the same section and/or position the button was.
    // You can change the selector (".learn-more") to one that uniquely identifies the reference element.
    var $topReference = $(".learn-more", ".lightweight-template");

    var setMockupsTop = function() {
        // check if the perspective mockups elements are on the page, if you're not going to use them, you can remove all its references
        if (!$perspectiveMockups.length) return;

        if ($(window).outerWidth() < 768) {
            $perspectiveMockups.css({top: $topReference.offset().top + "px"});
            return;
        }

        $perspectiveMockups.removeAttr("style");
    };

    var navbarSticky = function() {
        if ($(window).scrollTop() >= stickyPoint) {
            $navbar.addClass("navbar-sticky");
        } else {
            $navbar.removeClass("navbar-sticky");
        }
    };

    /**
     * STICKY MENU
     **/
    $(window).on("scroll", navbarSticky);

    navbarSticky();

    /**
     * SCROLLING NAVIGATION
     * Enable smooth transition animation when scrolling
     **/
    $('a.scrollto').on('click', function (event) {
        event.preventDefault();

        var scrollAnimationTime = 1200;
        var target = this.hash;

        $('html, body').stop().animate({
            scrollTop: $(target).offset().top - 45
        }, scrollAnimationTime, 'easeInOutExpo', function () {
            window.location.hash = target;
        });
    });

    /**
     *  NAVBAR SIDE COLLAPSIBLE - On Mobiles
     **/
    $(".navbar-toggler", $navbar).on("click", function() {
        if (!$navbar.is('.st-nav')) $navbar.toggleClass("navbar-expanded");
    });

  

    /**
     * Position the perspective mockups at the end of the first content section on mobile
     **/
    $perspectiveMockups.removeClass("hidden-preload");
    $(window).on("resize", setMockupsTop);

    setMockupsTop();


    /**
     * Prettyprint
     **/
    window.prettyPrint && prettyPrint();

    /**
     * AOS
     * Cool scrolling animations
     **/
    AOS.init({
        offset: 100,
        duration: 1500,
        disable: 'mobile'
    });

});
