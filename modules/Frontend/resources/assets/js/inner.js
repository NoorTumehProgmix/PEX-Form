import $ from "./jquery.js";

import "./shared";

const isRtl = $("html").attr("dir") === "rtl";

(($) => {
    // const galleries = $(".gallery");
    // if (galleries.length) {
    //     galleries.each(function (index, gallery) {
    //         $(gallery).magnificPopup({
    //             delegate: "a.gallery-item",
    //             type: "iframe",
    //             mainClass: "mfp-zoom-in mfp-with-fade",
    //             removalDelay: 160,
    //             preloader: false,
    //             fixedContentPos: false,
    //             callbacks: {
    //                 elementParse: function (item) {
    //                     const classes = item.el[0].className;
    //                     if (classes.includes("video")) {
    //                         item.type = "iframe";
    //                         item.iframe = {
    //                             srcAction: "iframe_src",
    //                         };
    //                     } else {
    //                         item.type = "image";
    //                     }
    //                 },
    //             },
    //             gallery: {
    //                 enabled: true,
    //                 navigateByImgClick: true,
    //                 preload: [0, 1],
    //             },
    //         });
    //     });
    // }
})(jQuery);
