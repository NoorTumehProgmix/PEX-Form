import $ from "../jquery.js";

export function initPexNav() {
    const $navbar = $("#header");
    const $hamburger = $("#hamburger");
    const $mobileMenu = $("#mobile-menu");

    if (!$navbar.length || !$hamburger.length || !$mobileMenu.length) {
        return;
    }

    const onScroll = () => {
        $navbar.toggleClass("scrolled", $(window).scrollTop() > 50);
    };

    $(window).on("scroll", onScroll);
    onScroll();

    const closeMenu = () => {
        $mobileMenu.removeClass("open");
        $hamburger.removeClass("open").attr("aria-expanded", "false");
    };

    $hamburger.on("click", () => {
        const isOpen = !$mobileMenu.hasClass("open");
        $mobileMenu.toggleClass("open", isOpen);
        $hamburger.toggleClass("open", isOpen).attr("aria-expanded", String(isOpen));
    });

    $mobileMenu.find("a").on("click", closeMenu);

    $('a[href^="#"]').on("click", function (event) {
        const href = $(this).attr("href");
        if (!href || href === "#" || href.length < 2) {
            return;
        }

        const $target = $(href);
        if (!$target.length) {
            return;
        }

        event.preventDefault();
        const offset = 76;
        const top = $target.offset().top - offset;
        $("html, body").stop().animate({ scrollTop: top }, 400);
    });
}
