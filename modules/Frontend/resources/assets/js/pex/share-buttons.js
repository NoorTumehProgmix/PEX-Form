import $ from "../jquery.js";

/* أزرار "مشاركة" في قسم التواصل — contact.blade.php
   الروابط تأتي من data-linkedin-link / data-whatsapp-link
   (مُعبّأة في Blade من إعدادات الـ CMS العامة). */
export function initShareButtons() {
    $("#share-linkedin").on("click", function () {
        var url = $(this).data("linkedin-link");
        if (url) window.open(url, "_blank", "noopener,noreferrer");
    });
    $("#share-whatsapp").on("click", function () {
        var url = $(this).data("whatsapp-link");
        if (url) window.open(url, "_blank", "noopener,noreferrer");
    });
}
