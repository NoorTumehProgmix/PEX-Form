import $ from "../jquery.js";

/* أداة مساعدة: الأحرف الأولى من الاسم (رجوع عند غياب الصورة) */
function initials(name) {
    var parts = String(name || "").trim().split(/\s+/).filter(Boolean);
    return (parts[0] || "").charAt(0) + (parts[1] ? parts[1].charAt(0) : "");
}

/* يبني دائرة الصورة (صورة أو حروف أولى) لشخص واحد داخل عنصر $wrap —
   نظير partials/avatar.blade.php، لكن هنا لأن الشخص يتغيّر مع كل نقرة
   على بطاقة مختلفة، فلا يمكن توليده من بلايد وقت الطلب. */
function renderAvatar($wrap, person, extraCls) {
    $wrap.empty();
    var hasPhoto = !!person.photo;
    var $av = $(
        '<div class="speaker-avatar' +
            (extraCls ? " " + extraCls : "") +
            (hasPhoto ? " has-photo" : "") +
            '"></div>',
    );
    $av.attr("data-initials", initials(person.name));
    if (hasPhoto) {
        $av.append($("<img>").attr({ src: person.photo, alt: person.name, loading: "lazy" }));
    } else {
        $av.attr("aria-hidden", "true").text(initials(person.name));
    }
    $wrap.append($av);
    return $av;
}

/* نافذة التعريف الموحّدة — تُستخدم من قسم المتحدثين وقسم الأجندة معاً.
   هيكل النافذة بالكامل HTML ثابت مُولَّد من بلايد
   (partials/profile-modal.blade.php) — هذا الملف يتعامل فقط مع
   سلوكها (فتح/إغلاق/تنقّل/سحب)، ويقرأ بياناته مباشرة من data-name /
   data-session / data-session-label الموجودة مسبقاً على كل بطاقة/شخص
   قابل للنقر — بلا أي قائمة بحث JS عامة وبلا window.*Data. */
export function initProfileModal() {
    var $overlay = $("#profile-modal");
    if (!$overlay.length) return;

    var $dialog = $overlay.find(".profile-dialog");
    var $avatarWrap = $("#profile-avatar-wrap");
    var $name = $("#profile-name");
    var $role = $("#profile-role");
    var $bio = $("#profile-bio");
    var $desc = $("#profile-desc");
    var $session = $("#profile-session");
    var $track = $("#profile-track");
    var $prev = $("#profile-prev");
    var $next = $("#profile-next");
    var $close = $("#profile-close");

    var state = { participants: [], index: 0, lastFocus: null };

    function renderMain() {
        var sp = state.participants[state.index];
        if (!sp) return;
        renderAvatar($avatarWrap, sp, "profile-avatar");
        $name.text(sp.name || "");
        $role.text(sp.role || "");
        $bio.text(sp.bio || "");
        $desc.text(sp.description || "");

        var single = state.participants.length <= 1;
        $prev.prop("disabled", single);
        $next.prop("disabled", single);
        $track.children().each(function (i) {
            var $c = $(this);
            $c.toggleClass("active", i === state.index);
            $c.attr("aria-current", i === state.index ? "true" : "false");
        });
        var $active = $track.children().eq(state.index);
        if ($active.length && $active[0].scrollIntoView) {
            $active[0].scrollIntoView({ inline: "center", block: "nearest" });
        }
    }

    function renderSession() {
        var multi = state.participants.length > 1;
        $session.css("display", multi ? "" : "none");
        $track.empty();
        state.participants.forEach(function (sp, i) {
            var $mini = $('<button type="button" class="profile-mini"></button>');
            $mini.attr("aria-label", sp.name + (sp.role ? " — " + sp.role : ""));
            renderAvatar($mini, sp, "mini-avatar");
            $mini.append($('<span class="profile-mini-name"></span>').text(sp.name || ""));
            $mini.on("click", function () {
                state.index = i;
                renderMain();
            });
            $track.append($mini);
        });
    }

    function goTo(delta) {
        var n = state.participants.length;
        if (n <= 1) return;
        state.index = (state.index + delta + n) % n;
        renderMain();
    }

    function open(name, participants, label) {
        participants = Array.isArray(participants) && participants.length ? participants : [];
        if (!participants.length) return;
        var idx = participants.findIndex(function (p) {
            return p.name === name;
        });
        state.participants = participants;
        state.index = idx > -1 ? idx : 0;
        state.lastFocus = document.activeElement;
        renderSession();
        renderMain();
        $overlay.prop("hidden", false);
        $("body").css("overflow", "hidden");
        $close.trigger("focus");
        $(document).on("keydown", onKeydown);
    }

    function close() {
        $overlay.prop("hidden", true);
        $("body").css("overflow", "");
        $(document).off("keydown", onKeydown);
        if (state.lastFocus && state.lastFocus.focus) state.lastFocus.focus();
    }

    function onKeydown(e) {
        if (e.key === "Escape") {
            close();
            return;
        }
        if (e.key === "ArrowRight") {
            goTo(-1);
            return;
        }
        if (e.key === "ArrowLeft") {
            goTo(1);
            return;
        }
        if (e.key === "Tab") {
            var $f = $dialog.find("button:not([disabled])");
            if (!$f.length) return;
            var first = $f[0],
                last = $f[$f.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                $(last).trigger("focus");
                e.preventDefault();
            } else if (!e.shiftKey && document.activeElement === last) {
                $(first).trigger("focus");
                e.preventDefault();
            }
        }
    }

    $close.on("click", close);
    $prev.on("click", function () {
        goTo(-1);
    });
    $next.on("click", function () {
        goTo(1);
    });
    $overlay.on("click", function (e) {
        if (e.target === $overlay[0]) close();
    });

    // سحب على الهاتف للتنقل بين المشاركين
    var sx = null;
    $dialog.on("touchstart", function (e) {
        sx = e.originalEvent.touches[0].clientX;
    });
    $dialog.on("touchend", function (e) {
        if (sx === null) return;
        var dx = e.originalEvent.changedTouches[0].clientX - sx;
        if (Math.abs(dx) > 45) goTo(dx > 0 ? -1 : 1);
        sx = null;
    });

    // أي بطاقة/شخص قابل للنقر (بطاقة متحدث أو شخص في الأجندة) يحمل
    // data-name + data-session (مصفوفة JSON لملفات تعريف كاملة) +
    // data-session-label — لا حاجة لأي قائمة بحث عامة.
    $(document).on("click", ".is-clickable[data-name]", function () {
        var $el = $(this);
        open($el.data("name"), $el.data("session"), $el.data("session-label"));
    });

    // رجوع لحروف الاسم عند فشل تحميل أي صورة شخص (بطاقات/نافذة التعريف)
    $(document).on("error", ".speaker-avatar.has-photo img", function () {
        var $av = $(this).closest(".speaker-avatar");
        $av.removeClass("has-photo").attr("aria-hidden", "true").empty().text($av.data("initials"));
    });
}
