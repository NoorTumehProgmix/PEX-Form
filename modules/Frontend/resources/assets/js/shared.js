import $ from "./jquery.js";
import "lazysizes";
import { initPexNav } from "./pex/nav.js";

initPexNav();

window.generateRandomString = function (length = 8) {
    let result = "";
    const characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    const charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
        result += characters.charAt(
            Math.floor(Math.random() * charactersLength),
        );
    }
    return result;
};

document.addEventListener("lazybeforeunveil", function (e) {
    var bg = e.target.getAttribute("data-bg");
    if (bg) {
        e.target.style.backgroundImage = "url(" + bg + ")";
    }
});

const isRtl = $("html").attr("dir") === "rtl";

(($) => {
    const header = $(".header");
    let lastScrollTop = 0;
    const stickyHeader = () => {
        let scrollTop = $(window).scrollTop();
        if (scrollTop < lastScrollTop && scrollTop !== 0) {
            header.addClass("sticky");
        } else {
            header.removeClass("sticky");
        }
        lastScrollTop = scrollTop;
    };

    const UpBtn = $("#upBtn");

    function toTop() {
        if ($(window).scrollTop() > 300) {
            UpBtn.addClass("show");
        } else {
            UpBtn.removeClass("show");
        }
    }

    UpBtn.on("click", function (e) {
        e.preventDefault();
        $("html, body").stop().animate({ scrollTop: 0 }, 300);
    });

    $(window).on("scroll", function () {
        stickyHeader();
        toTop();
    });

    stickyHeader();
    toTop();

    const body = $("body");

    $(".burger-menu").on("click", function () {
        const btn = $(this);
        const menu = $(".menu-list");
        if (!btn.hasClass("open")) {
            btn.addClass("open");
            menu.addClass("open");
            body.css("overflow", "hidden");
        } else {
            btn.removeClass("open");
            menu.removeClass("open");
            body.removeAttr("style");
        }
    });

    function mobileMenu() {
        if ($(window).width() < 992) {
            $(".main-menu-list .has-dropdown > .item").on(
                "click",
                function (e) {
                    e.preventDefault();
                    const parent = $(this).closest(".has-dropdown");
                    const menu = parent.find("> .dropdown-menu");
                    if (!menu.hasClass("open")) {
                        menu.addClass("open");
                        menu.stop().slideDown();
                    } else {
                        menu.removeClass("open");
                        menu.stop().slideUp();
                    }
                },
            );
        }
    }

    mobileMenu();

    // const containerWidth = $(".container").first().width() || 1400;
    // const containerFluidOffset = () => {
    //     const windowWidth = $(window).width();
    //     const padding = (windowWidth - containerWidth) / 2 + 80;
    //     if ($(window).width() >= 1400) {
    //         if (isRtl) {
    //             $(".offset-right").css("padding-left", padding);
    //             $(".offset-left").css("padding-right", padding);
    //         } else {
    //             $(".offset-left").css("padding-left", padding);
    //             $(".offset-right").css("padding-right", padding);
    //         }
    //     }
    // };

    // containerFluidOffset();

    $(window).on("resize", function () {
        mobileMenu();
        // containerFluidOffset();
    });

    if ($(window).width() < 576) {
        $(".footer-item.footer-item-mobile .footer-item--title").on(
            "click",
            function (e) {
                e.preventDefault();
                const btn = $(this);
                const wrapper = btn.closest(".footer-item-mobile");
                const content = wrapper.find(".footer-item-content");
                if (!wrapper.hasClass("active")) {
                    wrapper.addClass("active");
                    content.stop().slideDown();
                } else {
                    wrapper.removeClass("active");
                    content.stop().slideUp();
                }
            },
        );
    }
})(jQuery);
