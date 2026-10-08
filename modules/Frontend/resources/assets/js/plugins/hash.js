(($) => {
    const body = $("body");

    body.on("click", "[data-close-popup]", function (e) {
        e.preventDefault();
        closePopup($(this));
    });

    function closePopup(item) {
        let _target = $(item).data("close-popup");
        let _callBack = function () {};
        if (
            typeof $(item).attr("data-delete-popup") !== "undefined" &&
            $(item).attr("data-delete-popup") === "true"
        ) {
            _callBack = function () {
                $(item).remove();
            };
        }
        $("#" + _target).fadeOut(_callBack);
        body.removeAttr("style");
        remove_hash_from_url();
    }

    function remove_hash_from_url() {
        const uri = window.location.toString();

        if (uri.indexOf("#") > 0) {
            const clean_uri = uri.substring(0, uri.indexOf("#"));
            window.history.replaceState({}, document.title, clean_uri);
        }
    }

    function popUp(item) {
        const _item = $(item);
        _item.fadeIn();
        body.css("overflow", "hidden");
        //     $("body").css({
        //     "overflow": "hidden",
        //     "position": "fixed",
        //     "width": "100%",
        // });
    }

    function handleHash(item) {
        const _item = $(item);
        if (_item.length) {
            if (_item.hasClass("popup")) {
                popUp(_item);
            } else if (_item.hasClass("toggle-content")) {
                const toggleTitle = $(`[data-toggle="${_item.attr("id")}"]`);
                blockToggles(toggleTitle);
            } else if (_item.hasClass("tab-hash")) {
                blockTabs(_item);
            } else {
                _item[0].scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
                // $('html, body').animate({scrollTop: _item.offset().top - 250}, 'fast');
            }
        }
    }

    // function getOffset(el) {
    //     const rect = el[0].getBoundingClientRect();
    //     return {
    //         left: rect.left + window.scrollX,
    //         top: rect.top + window.scrollY
    //     };
    // }

    function blockTabs(tab) {
        const _tab = $(tab);
        const _tab_parent = _tab.closest(".tabs-w");
        _tab_parent
            .find(".tabs-list > li > .tab-item.active")
            .removeClass("active");
        _tab_parent
            .find(".tab-contents > .tab-content.active")
            .removeClass("active")
            .stop()
            .fadeOut();
        const hash = "#" + _tab.attr("id");
        const activeTab = _tab_parent.find('[href="' + hash + '"]');
        activeTab.addClass("active");
        scrollableTabs(_tab_parent, activeTab);
        lineIndicator(_tab_parent, activeTab);
        _tab.addClass("active").stop().fadeIn();

        $("html, body").animate(
            { scrollTop: _tab_parent.offset().top },
            "fast"
        );
    }

    window.addEventListener("hashchange", function (event) {
        if (window.location.hash) {
            handleHash($(window.location.hash));
        }
    });

    $('a[href*="#"]').on("click", function (e) {
        let hash = $(window.location.hash);
        if ($(this).hasClass("dont-show-hash")) {
            e.preventDefault();
            hash = $($(this).attr("href"));
        }
        handleHash(hash);
    });

    window.addEventListener("load", function (event) {
        if (window.location.hash) {
            handleHash($(window.location.hash));
        }
    });
    if (!window.location.hash) {
        $(".tabs-w").each(function (index, tabs) {
            const tab = $(tabs).find(".tab-item").first();
            tab.addClass("active");
            const hash = tab.attr("href");
            $(`${hash}`).addClass("active").stop().slideDown();

            // setTimeout(function () {
            //     lineIndicator($(tabs), tab);
            // }, 400)
        });
    }

    function lineIndicator(element, activeTab) {
        const wrapper = $(element);
        if (wrapper.hasClass("with-line-indicator")) {
            const item = $(activeTab);
            const itemOffset = item.offset().left;
            const itemWidth = item.width();
            const indicatorOffset = itemWidth / 2 + itemOffset;
            const indicator = wrapper.find(".line-indicator").first();
            indicator.stop().animate({
                left: indicatorOffset,
            });
        }
    }

    function scrollableTabs(element, activeTab) {
        const wrapper = $(element);
        if (wrapper.hasClass("scrollable-tabs")) {
            const item = $(activeTab);
            const itemOffset = item.offset().left;
            const itemWidth = item.width();
            const tabsListOffset = itemWidth / 2 + itemOffset;
            const tabsList = wrapper.find(".tabs-list").first();
            tabsList.stop().scrollLeft(tabsListOffset);
        }
    }

    $(document).click(function (event) {
        if (!$(event.target).closest(".popup-container").length) {
            const item = $(event.target).closest(".popup");
            if (item.length) {
                const closeBtn = item.find(".popup-close");
                closePopup(closeBtn);
            }
        }
    });

    // $("[data-toggle]").on("click", function (e) {
    //     e.preventDefault();
    //     const toggle = $(this);
    //     const toggleWrapper = toggle.closest(".toggle-wrapper");
    //     const toggleId = toggle.data("toggle");
    //     const toggleContent = $(`#${toggleId}`);
    
    //     if (!toggleId || !toggleContent.length) return;
    
    //     if (!toggle.hasClass("active")) {
    //         // Close all others
    //         toggleWrapper.find(".toggle-title").removeClass("active");
    //         toggleWrapper.find(".toggle-content").stop().slideUp();
    
    //         // Open this one
    //         toggle.addClass("active");
    //         toggleContent.stop().slideDown();
    //     } else {
    //         // Close this one
    //         toggle.removeClass("active");
    //         toggleContent.stop().slideUp();
    //     }
    // });
    

    function blockToggles(toggle) {
        const _item = $(toggle); // لازم يكون toggle-title
        const toggleWrapper = _item.closest(".toggle-wrapper");
        const toggleId = _item.data("toggle");
        const toggleContent = $(`#${toggleId}`);
    
        if (!toggleId || !toggleContent.length) return;
    
        if (!_item.hasClass("active")) {
            // Close all others
            toggleWrapper.find(".toggle-title").removeClass("active");
            toggleWrapper.find(".toggle-content").stop().slideUp();
        
            // Open this one
            _item.addClass("active");
            toggleContent.stop().slideDown();
        } else {
            // Close this one
            _item.removeClass("active");
            toggleContent.stop().slideUp();
        }
    }
})(jQuery);
