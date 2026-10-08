import $ from "../jquery.js";

/* زر "تفاصيل الجلسة" (Accordion) — agenda.blade.php */
export function initAgendaAccordion() {
    $(document).on("click", ".agenda-toggle", function () {
        var $btn = $(this);
        var $card = $btn.closest(".agenda-card");
        var open = $card.toggleClass("is-open").hasClass("is-open");
        $btn.attr("aria-expanded", String(open));
        $card.find(".agenda-desc").first().prop("hidden", !open);
    });
}
