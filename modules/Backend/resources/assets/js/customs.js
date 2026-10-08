// function addStyleSubmenu(e) {
//     var t = e.find(".juzaweb__menuLeft__navigation"),
//         n = e.offset().top,
//         i = $(window).scrollTop(),
//         o = n - i - 30,
//         e = n + t.height() + 1,
//         n = 60 + e - $('.juzaweb__layout').height(),
//         i = $(window).height() + i - 50;
//
//     if ((n = o < (n = i < e - n ? e - i : n) ? o : n) > 1 && n > 40) {
//         t.css("margin-top", "-" + n + "px");
//     } else {
//         t.css("margin-top", "");
//     }
// }

$(document).ready(function () {
    let bodyElement = $("body");
    $(".tag-lang").val($(".lang-switch").val());
    $(".appointment_form").closest("form").find(".btn-group").hide();

    bodyElement.on("change", ".show_on_front-change", function () {
        let showOnFront = $(this).val();

        if (showOnFront == "posts") {
            $(".select-show_on_front").prop("disabled", true);
        }

        if (showOnFront == "page") {
            $(".select-show_on_front").prop("disabled", false);
        }
    });

    bodyElement.on("click", ".cancel-button", function () {
        window.location = "";
    });

    bodyElement.on("change", ".generate-slug", function () {
        let title = $(this).val();

        ajaxRequest(
            juzaweb.adminUrl + "/load-data/generateSlug",
            {
                title: title,
            },
            {
                method: "GET",
                callback: function (response) {
                    $("input[name=slug]").val(response.slug).trigger("change");
                },
            }
        );
    });

    bodyElement.on("click", ".slug-edit", function () {
        let slugInput = $(this).closest(".input-group").find("input:first");
        slugInput.prop("readonly", !slugInput.prop("readonly"));
    });

    bodyElement.on("click", ".close-message", function () {
        let id = $(this).data("id");
        ajaxRequest(
            juzaweb.adminUrl + "/remove-message",
            {
                id: id,
            },
            {
                method: "POST",
                callback: function (response) {},
            }
        );
    });

    // $(".juzaweb__menuLeft__submenu").on("mouseover", function () {
    //         if (!$(this).hasClass('juzaweb__menuLeft__submenu--toggled')) {
    //             addStyleSubmenu($(this));
    //         }
    //     }
    // );

    $(".juzaweb__menuLeft__item__link").on("click", function () {
        const menu = $(this).closest(".juzaweb__menuLeft__submenu");
        const subMenu = menu.find(".juzaweb__menuLeft__navigation");
        if (!menu.hasClass("juzaweb__menuLeft__submenu--toggled")) {
            $(
                ".juzaweb__menuLeft .juzaweb__menuLeft__navigation .juzaweb__menuLeft__submenu"
            ).removeClass("juzaweb__menuLeft__submenu--toggled");
            $(
                ".juzaweb__menuLeft .juzaweb__menuLeft__navigation .juzaweb__menuLeft__navigation"
            )
                .stop()
                .slideUp();
            menu.addClass("juzaweb__menuLeft__submenu--toggled");
            subMenu.stop().slideDown();
        } else {
            menu.removeClass("juzaweb__menuLeft__submenu--toggled");
            subMenu.stop().slideUp();
        }
    });

    //Customer Scripts
    $(document).on("click", ".expand-more", function (e) {
        e.preventDefault();
        $(this).closest("td").find(".short-text").toggleClass("expand");
    });

    $(".lang-switch").on("change", function () {
        // Get selected value
        var selectedValue = $(this).val();
        $(".tag-lang").val(selectedValue);
        if ($(".related_ids").length) {
            var go_to_id = $(".related_ids#" + selectedValue).val();
            var currentUrl = window.location.href;
            if (typeof go_to_id != "undefined") {
                var newUrl = currentUrl.replace(
                    /(\d+)\/edit/,
                    go_to_id + "/edit"
                );
                window.location.href = newUrl;
            }
        }
    });

    //Hide not related lang taxonomies
    var formLang = $(".lang-switch").val();
    $(".taxonomy-categories li[data-lang!='" + formLang + "']").remove();

    if ($(".box-custom-seo").length) {
        if (tinyMCE.activeEditor) {
            tinyMCE.get("content-editor").on("change", function () {
                if ($("#model_exists").val() == "") {
                    updateSeoForm();
                }
            });
        }

        function updateSeoForm() {
            let editor = tinyMCE.get("content-editor");
            if (editor) {
                var description = tinyMCE.get("content-editor").getContent();
            } else {
                var description = "";
            }
            var strippedContent = $("<div>").html(description).text();
            var truncatedContent = strippedContent.substring(0, 160);
            $("#meta_description").val(truncatedContent);
            $(".review-description").text(truncatedContent);

            $("#meta_og_description").val(truncatedContent);
            $(".meta_og_description").text(truncatedContent);

            $("#meta_twitter_description").val(truncatedContent);
        }
    }
    $(".slug-edit").click(function (e) {
        alert(
            "Caution! Modifying the slug of a page will have an impact on any associated subpages and posts."
        );
    });

    // $(document).on('click', '.multi-files .add-image-images', function () {
    //     let prefix = juzaweb.adminPrefix + '/file-manager';
    //     let item = $(this).closest('.form-images');
    //     let inputName = item.find('.input-name').val();
    //     juzawebFileManager({
    //         type: 'file&lang=' + formLang,
    //         prefix: prefix,
    //         multichoose: true,
    //     }, function (files) {
    //         let temp = document.getElementById('form-images-template').innerHTML;
    //         let str = "";

    //         $.each(files, function (index, item) {
    //             item.icon = "";
    //             if (!isImage(item.url)) {
    //                 item.icon = "fa fa-file";
    //             }
    //             str += replace_template(temp, {
    //                 name: inputName,
    //                 url: item.url,
    //                 icon: item.icon,
    //                 path: item.path
    //             });
    //         });

    //         item.find('.images-list .image-item:last').before(str);
    //     });
    // });
    //Change post status,show schedule datepicker
    $('select[name="status"]').on("change", function () {
        var selectedValue = $(this).val();
        if (selectedValue === "publish") {
            $("#status_scheduled").show();
        } else {
            $("#status_scheduled").hide();
        }
    });
    //Page Preview before publish
    $(document).on("click", "#preview-post", function (e) {
        e.preventDefault();
        let form = $(this).closest("form");
        let formData = new FormData(form[0]);
        formData.append("status", "preview");
        formData.append("content", tinymce.get("content-editor").getContent());

        let btnsubmit = form.find("button[type=submit]");
        let currentText = btnsubmit.html();
        let currentIcon = btnsubmit.find("i").attr("class");

        btnsubmit.find("i").attr("class", "fa fa-spinner fa-spin");
        btnsubmit.prop("disabled", true);

        if (btnsubmit.data("loading-text")) {
            btnsubmit.html(
                '<i class="fa fa-spinner fa-spin"></i> ' +
                    btnsubmit.data("loading-text")
            );
        }

        sendcustomRequestAjax(
            form,
            formData,
            btnsubmit,
            currentText,
            currentIcon,
            "preview"
        );
    });

    //Copy page
    if ($("#copy-post").length) {
        const params = new URLSearchParams(window.location.search);
        if (params.get("duplicate") === "1") {
            setTimeout(function () {
                $("#copy-post").trigger("click");
            }, 100);
        }
    }
    $(document).on("click", "#copy-post", function (e) {
        e.preventDefault();
        let form = $(this).closest("form");
        let formData = new FormData(form[0]);
        formData.append("status", "draft");
        formData.append("content", tinyMCE.activeEditor.getContent());

        let btnsubmit = form.find("button[type=submit]");
        let currentText = btnsubmit.html();
        let currentIcon = btnsubmit.find("i").attr("class");

        btnsubmit.find("i").attr("class", "fa fa-spinner fa-spin");
        btnsubmit.prop("disabled", true);

        if (btnsubmit.data("loading-text")) {
            btnsubmit.html(
                '<i class="fa fa-spinner fa-spin"></i> ' +
                    btnsubmit.data("loading-text")
            );
        }

        sendcustomRequestAjax(
            form,
            formData,
            btnsubmit,
            currentText,
            currentIcon,
            "copy"
        );
    });
});

window.init_editor = function init_editor(id, themeColors) {
    var colorMap = [];
    for (var color in themeColors) {
        var colorCode = themeColors[color].substring(1);
        colorMap.push(colorCode, themeColors[color]);
    }
    tinymce.init({
        selector: "#" + id,
        convert_urls: true,
        document_base_url: "{{ url(' / storage') }}/",
        urlconverter_callback: function (url, node, on_save, name) {
            url = url.replace("{{ url('/storage') }}/", "");
            return url;
        },
        height: 400,
        plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table directionality",
            "emoticons template paste textpattern advlist",
        ],
        extended_valid_elements:
            "iframe[src|frameborder|style|scrolling|class|width|height|name|align]",

        link_class_list: [
            {
                title: "None",
                value: "",
            },
            {
                title: "Button",
                value: "btn btn-default",
            },
            {
                title: "Link",
                value: "read-more",
            },
        ],
        menu: {
            file: {
                title: "File",
                items: "newdocument restoredraft | preview | print ",
            },
            edit: {
                title: "Edit",
                items: "undo redo | cut copy paste | selectall | searchreplace",
            },
            view: {
                title: "View",
                items: "code | visualaid visualchars visualblocks | spellchecker | preview fullscreen",
            },
            insert: {
                title: "Insert",
                items: "image link media template codesample inserttable | charmap emoticons hr | pagebreak nonbreaking anchor toc | insertdatetime",
            },
            format: {
                title: "Format",
                items: "bold italic underline strikethrough superscript subscript codeformat | formats blockformats fontformats fontsizes align lineheight | forecolor backcolor | removeformat",
            },
            tools: {
                title: "Tools",
                items: "spellchecker spellcheckerlanguage | code wordcount",
            },
            table: {
                title: "Table",
                items: "inserttable | cell row column | tableprops deletetable",
            },
        },
        toolbar: [
            {
                name: "new",
                items: ["newdocument"],
            },
            {
                name: "history",
                items: ["undo", "redo"],
            },
            {
                name: "styles",
                items: ["styleselect"],
            },
            {
                name: "formatting",
                items: ["bold", "italic"],
            },
            {
                name: "alignment",
                items: [
                    "alignleft",
                    "aligncenter",
                    "alignright",
                    "alignjustify",
                ],
            },
            {
                name: "color",
                items: ["forecolor", "backcolor"],
            },

            {
                name: "indentation",
                items: ["outdent", "indent", "bullist", "numlist"],
            },
            {
                name: "media",
                items: ["link", "image", "media"],
            },
            {
                name: "view",
                items: ["code", "preview", "fullscreen"],
            },
        ],
        color_map: colorMap,
        file_picker_callback: function (callback, value, meta) {
            let x =
                window.innerWidth ||
                document.documentElement.clientWidth ||
                document.getElementsByTagName("body")[0].clientWidth;
            let y =
                window.innerHeight ||
                document.documentElement.clientHeight ||
                document.getElementsByTagName("body")[0].clientHeight;
            let cmsURL =
                "/" +
                juzaweb.adminPrefix +
                "/file-manager?editor=" +
                meta.fieldname;

            if (meta.filetype == "image") {
                cmsURL = cmsURL + "&type=image";
            } else {
                cmsURL = cmsURL + "&type=file";
            }

            tinyMCE.activeEditor.windowManager.openUrl({
                url: cmsURL,
                title: "Filemanager",
                width: x * 0.8,
                height: y * 0.8,
                resizable: "yes",
                close_previous: "no",
                onMessage: (api, message) => {
                    callback(message.content);
                },
            });
        },
        setup: function (ed) {
            ed.on("change", function (e) {
                let title = $("input[name=title]").val();
                let description = tinyMCE.activeEditor.getContent();
                if (!$(".review-title").length) {
                    return false;
                }

                var strippedContent = $("<div>").html(description).text();
                $(".box-custom-seo #meta_title").val(title);
                $(".review-title").text(title);

                $("#meta_og_title").val(title);
                $(".meta_og_title").text(title);

                $("#meta_twitter_title").val(title);

                if ($("#meta_description").val() == "") {
                    $("#meta_description").val(strippedContent);
                    $(".review-description").text(strippedContent);
                }
                if ($("#meta_og_description").val() == "") {
                    $("#meta_og_description").val(strippedContent);
                    $(".meta_og_description").text(strippedContent);
                }
            });
        },
    });
};

var previewID = 0;

function sendcustomRequestAjax(
    form,
    data,
    btnsubmit,
    currentText,
    currentIcon,
    type = ""
) {
    let notify = form.data("notify") || false;
    var url = form.attr("action");
    var segments = url.split("/");
    if (/^\d+$/.test(segments[segments.length - 1])) {
        segments.pop(); // Remove the last segment if it's a number
    }
    var form_action = segments.join("/");
    var $method = "POST";
    data.delete("_method");
    if (previewID != 0) {
        form_action = form_action + "/" + previewID;
        data.append("_method", "PUT");
    }
    $.ajax({
        type: $method,
        url: form_action,
        dataType: "json",
        data: data,
        cache: false,
        contentType: false,
        processData: false,
    })
        .done(function (response) {
            btnsubmit.find("i").attr("class", currentIcon);
            btnsubmit.prop("disabled", false);

            if (btnsubmit.data("loading-text")) {
                btnsubmit.html(currentText);
            }

            if (response.status === false) {
                return false;
            }

            if (response.data.path && type == "preview") {
                let a = document.createElement("a");
                a.target = "_blank";
                a.href = response.data.path;
                a.click();
                return false;
            }
            if (response.data.redirect) {
                window.location.href = response.data.redirect;
                return;
            }

            return false;
        })
        .fail(function (response) {
            btnsubmit.find("i").attr("class", currentIcon);
            btnsubmit.prop("disabled", false);

            if (btnsubmit.data("loading-text")) {
                btnsubmit.html(currentText);
            }

            if (notify) {
                show_notify(response);
            } else {
                show_message(response);
            }
            return false;
        });
}

$("#export-excel-btn").click(function (e) {
    e.preventDefault();
    var formData = $("#form-search").serialize();
    var url = $(this).attr("href");
    url += "?" + formData;
    window.location.href = url;
});

$(document).on("click", ".add-repeater-item", function () {
    let form = $(this).closest(".form-repeater");
    let marker = generate_uuid();
    let template = form.find(".repeater-item-template").html();
    template = replace_template(template, { marker: marker });
    form.find(".repeater-items").append(template);
    loadSelectProducts();
    loadTaxonomiesProducts();
});

$(document).on("click", ".remove-repeater-item", function () {
    $(this).closest(".repeater-item").remove();
});

$(".repeater-items-logs").on("click", ".save-log", function () {
    const form = $(this).closest("form");
    let formData = new FormData(form[0]);
    console.log(formData, form[0]);
    let btnsubmit = $(this);
    btnsubmit.prop("disabled", true);

    $.ajax({
        type: form.attr("method"),
        url: form.attr("action"),
        dataType: "json",
        data: formData,
        processData: false,
        contentType: false,
    })
        .done(function (response) {
            show_message(response);
            console.log(response);
            const newRow = `
        <tr>
            <td>${response.data.note}</td>
            <td>${response.data.user}</td>
            <td>${response.data.date}</td>
        </tr>
    `;
            $(".repeater-items-logs").find(".form-addtion").after(newRow);
            form.find('input[name="note"]').val("");
            btnsubmit.prop("disabled", false);
        })
        .fail(function (response) {
            btnsubmit.prop("disabled", false);
            show_message(response);
            return false;
        });
});

function loadTaxonomiesProducts() {
    $(".load-taxonomies").select2({
        allowClear: true,
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        placeholder: function (params) {
            return {
                id: null,
                text: params.placeholder,
            };
        },
        ajax: {
            method: "GET",
            url: juzaweb.adminUrl + "/load-data/loadTaxonomies",
            dataType: "json",
            data: function (params) {
                let postType = $(this).data("post-type");
                let taxonomy = $(this).data("taxonomy");
                let explodes = $(this).data("explodes");
                let parent = $(this).data("parent");
                if (explodes) {
                    explodes = $("." + explodes)
                        .map(function () {
                            return $(this).val();
                        })
                        .get();
                }
                if (parent) {
                    parent = $("#search-" + parent).val();
                }

                return {
                    search: $.trim(params.term),
                    page: params.page,
                    explodes: explodes,
                    post_type: postType,
                    taxonomy: taxonomy,
                    lang: "en",
                    parent: parent,
                };
            },
        },
    });
}

function loadSelectProducts() {
    $(".load-values").select2({
        allowClear: true,
        tags: true,
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        createTag: function (params) {
            return {
                id: params.term + "-new",
                text: params.term,
            };
        },
    });
    $(".load-features").select2({
        allowClear: true,
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        placeholder: function (params) {
            return {
                id: null,
                text: params.placeholder,
            };
        },
    });
    $(".load-manufacturers").select2({
        allowClear: true,
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        placeholder: function (params) {
            return {
                id: null,
                text: params.placeholder,
            };
        },
        ajax: {
            method: "GET",
            url: juzaweb.adminUrl + "/load-data/loadManufacturers",
            dataType: "json",
            data: function (params) {
                return {
                    search: $.trim(params.term),
                };
            },
        },
    });
    $(".load-posts").select2({
        allowClear: true,
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        placeholder: function (params) {
            return {
                id: null,
                text: params.placeholder,
                parent_name: parent_name,
            };
        },
        ajax: {
            method: "GET",
            url: "/" + juzaweb.adminPrefix + "/load-data/loadPosts",
            dataType: "json",
            data: function (params) {
                let type = $(this).data("type") ? $(this).data("type") : null;
                return {
                    search: $.trim(params.term),
                    page: params.page,
                    type: type,
                    lang: $(".load-posts").data("lang"),
                };
            },
        },
        templateResult: function (data) {
            if (!data.id) {
                return $("<span>").text(data.text);
            }
            var parentName = "";
            if (data.parent_name) {
                parentName =
                    "<small style='display:block;color:#bf0603;'>" +
                    data.parent_name +
                    "</small>";
            }
            var optionContainer = $(
                "<div><span>" + data.text + parentName + "</span></div>"
            );
            return optionContainer;
        },
    });

    $(".load-table").select2({
        allowClear: true,
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        placeholder: function (params) {
            return {
                id: null,
                text: params.placeholder,
                parent_name: parent_name,
            };
        },
        ajax: {
            method: "GET",
            url: "/" + juzaweb.adminPrefix + "/load-data/loadTable",
            dataType: "json",
            data: function (params) {
                let table = $(this).data("table")
                    ? $(this).data("table")
                    : null;

                let title_field = $(this).data("title-field")
                    ? $(this).data("title-field")
                    : "title";
                return {
                    search: $.trim(params.term),
                    page: params.page,
                    table: table,
                    title_field: title_field,
                };
            },
        },
        templateResult: function (data) {
            if (!data.id) {
                return $("<span>").text(data.text);
            }
            var parentName = "";
            if (data.parent_name) {
                parentName =
                    "<small style='display:block;color:#bf0603;'>" +
                    data.parent_name +
                    "</small>";
            }
            var optionContainer = $(
                "<div><span>" + data.text + parentName + "</span></div>"
            );
            return optionContainer;
        },
    });

    $(".load-stores").select2({
        allowClear: true,
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        placeholder: function (params) {
            return {
                id: null,
                text: params.placeholder,
            };
        },
        ajax: {
            method: "GET",
            url: juzaweb.adminUrl + "/load-data/loadStores",
            dataType: "json",
            data: function (params) {
                return {
                    search: $.trim(params.term),
                };
            },
        },
    });
    $(".load-store-products").select2({
        allowClear: true,
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        placeholder: function (params) {
            return {
                id: null,
                text: params.placeholder,
                parent_name: parent_name,
            };
        },
        ajax: {
            method: "GET",
            url: "/" + juzaweb.adminPrefix + "/load-data/loadStoreProducts",
            dataType: "json",
            data: function (params) {
                return {
                    search: $.trim(params.term),
                    page: params.page,
                };
            },
        },
    });
}

$("body").on("change", '[name="start_date"], [name="end_date"]', function () {
    let startDate = $('[name="start_date"]').val();
    let endDate = $('[name="end_date"]').val();

    $.ajax({
        type: "get",
        url: juzaweb.adminUrl + "/load-data/loadKpi",
        dataType: "html",
        data: {
            start_date: startDate,
            end_date: endDate,
        },
    })
        .done(function (response) {
            $("#kpi-tables").html(response);
        })
        .fail(function (response) {
            return false;
        });
});

$("body").on("change", '[name="start_date_dater"]', function () {
    let startDate = $('[name="start_date_dater"]').val();

    $.ajax({
        type: "get",
        url: juzaweb.adminUrl + "/load-data/loadDaterKpi",
        dataType: "html",
        data: {
            start_date_dater: startDate,
        },
    })
        .done(function (response) {
            $("#kpi-dater-body").html(response);
        })
        .fail(function (response) {
            return false;
        });
});

$(document).on("click", ".update-qty", function () {
    var $this = $(this);
    var variantId = $(this).data("id");
    var updatedQty = $(this).closest(".input-group").find(".qty-input").val();
    if (updatedQty < 0 || isNaN(updatedQty) || updatedQty == "") {
        alert("Please enter a valid quantity.");
        return;
    }

    $.ajax({
        url: juzaweb.adminUrl + "/ecommerce/stocks/update-quantity",
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id: variantId,
            qty: updatedQty,
        },
        success: function (response) {
            if (response.success) {
                var qtyTd = $this.closest("tr").find(".qty");
                qtyTd.text(updatedQty);
                $this.closest(".input-group").find(".qty-input").val("");
            } else {
                alert("Failed to update the quantity.");
            }
        },
        error: function () {
            alert("Error updating the quantity. Please try again.");
        },
    });
});
