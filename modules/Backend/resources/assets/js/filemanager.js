window.juzawebFileManager = function juzawebFileManager(options, cb) {
    $("#juzawebFileManagerModal").modal("hide");
    $(".modal-backdrop").remove();
    let type = options.type || "image";
    let routePrefix = options.prefix;
    let multichoose = options.multichoose || false;
    let btn = options.button || "";

    if (routePrefix[0] !== "/") {
        routePrefix = "/" + routePrefix;
    }

    let url =
        routePrefix + "?type=" + type + (multichoose ? "&multichoose=1" : "");
    // Load the file manager view via AJAX
    $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
            $(".filemanager-wrapper").html(response);
            $("#fab").empty();
            ($("#fab").fab({
                buttons: [
                    {
                        icon: "fa fa-upload",
                        label: lang["nav-upload"],
                        attrs: { id: "upload" },
                    },
                    {
                        icon: "fa fa-folder",
                        label: lang["nav-new"],
                        attrs: { id: "add-folder" },
                    },
                ],
            }),
                actions.reverse().forEach(function (t) {
                    $("#nav-buttons > ul").prepend(
                        $("<li>")
                            .addClass("nav-item")
                            .append(
                                $("<a>")
                                    .addClass("nav-link d-none")
                                    .attr("data-action", t.name)
                                    .attr("data-multiple", t.multiple)
                                    .append(
                                        $("<i>").addClass(
                                            "fa fa-fw fa-" + t.icon,
                                        ),
                                    )
                                    .append($("<span>").text(t.label)),
                            ),
                    );
                }),
                performLfmRequest("errors").done(function (t) {
                    JSON.parse(t).forEach(function (t) {
                        $("#alerts").append(
                            $("<div>")
                                .addClass("alert alert-warning")
                                .append(
                                    $("<i>").addClass(
                                        "fa fa-exclamation-circle",
                                    ),
                                )
                                .append(" " + t),
                        );
                    });
                }),
                $(window).on("dragenter", function () {
                    $("#uploadModal").modal("show");
                }),
                usingWysiwygEditor() && $("#multi_selection_toggle").hide());
            $("#juzawebFileManagerModal").modal("show");

            loadFolders();
            btn.css({ "pointer-events": "", opacity: "" });
        },
        error: function () {
            $("#juzawebFileManagerModal .modal-body").html(
                '<p class="text-danger text-center">Failed to load file manager.</p>',
            );
        },
    });

    // Set callback function
    window.SetUrl = function (selectedUrl) {
        if (cb && typeof cb === "function") {
            cb(selectedUrl);
        }
        $("#juzawebFileManagerModal").modal("hide");
    };
};

$.fn.filemanager = function (type, options) {
    let element = this;
    let prefix = juzaweb.adminPrefix + "/file-manager";
    this.on("click", function (e) {
        juzawebFileManager(
            {
                type: type,
                prefix: prefix,
            },
            function (files) {
                let file = files[0];

                if (element.data("input")) {
                    let targetInput = $("#" + element.data("input"));
                    targetInput.val(file.path);
                }

                if (element.data("preview")) {
                    let targetPreview = $("#" + element.data("preview"));
                    targetPreview.html(
                        '<img src="' + file.url + '" alt="' + file.name + '">',
                    );
                }

                if (element.data("name")) {
                    let targetName = $("#" + element.data("name"));
                    targetName.html(file.name);
                }
            },
        );
    });
};

$(document).ready(function () {
    const bodyElement = $("body");

    bodyElement.on("click", ".file-manager", function () {
        let type = $(this).data("type") || "image";
        let input = $(this).data("input");
        let preview = $(this).data("preview");
        let name = $(this).data("name");
        let prefix = juzaweb.adminPrefix + "/file-manager";

        juzawebFileManager(
            {
                type: type,
                prefix: prefix,
                multichoose: true,
            },
            function (files) {
                let file = files[0];

                if (input) {
                    let targetInput = $("#" + input);
                    targetInput.val(file.path);
                }

                if (preview) {
                    let targetPreview = $("#" + preview);
                    targetPreview.html('<img src="' + file.url + '" alt="">');
                }

                if (name) {
                    let targetName = $("#" + name);
                    targetName.html(file.name);
                }
            },
        );
    });

    bodyElement.on("click", ".filemanager-manager", function () {
        let prefix = juzaweb.adminPrefix + "/file-manager";
        var type = "image";

        var fileManagerWindow = juzawebFileManager(
            {
                type: type,
                prefix: prefix,
                multichoose: true,
            },
            function (files) {
                location.reload();
            },
        );

        let checkWindowClosedInterval = setInterval(function () {
            if (fileManagerWindow.closed) {
                clearInterval(checkWindowClosedInterval);
                location.reload();
            }
        }, 200);
    });

    function setFacebookTwitterImage(element, file, previewElement = null) {
        let item = element.closest(".form-image");
        let targetPreview = item.find(".dropify-render");
        let targetName = item.find(".dropify-filename-inner");

        element.val(file.path);
        targetPreview.html('<img src="' + file.url + '" alt="">');
        targetName.html(file.name);
        item.addClass("previewing");
        item.find(".image-hidden").show();

        if (previewElement) {
            let imagePreview = previewElement.find('img[name="image_preview"]');
            imagePreview.attr("src", file.url);
        }
    }

    bodyElement.on("click", ".form-image", function (e) {
        let item = $(this);
        item.css({ "pointer-events": "none", opacity: "0.5" });

        let targetInput = item.find(".input-path");
        let targetPreview = item.find(".dropify-render");
        let targetName = item.find(".dropify-filename-inner");
        let prefix = juzaweb.adminPrefix + "/file-manager";
        var type = "image";
        if (item.closest(".banner-item").length) {
            type = "file";
        }
        if (item.attr("data-file") == "true") {
            type = "file";
        }

        var dataType = item.data("type");
        if (dataType) {
            type = dataType;
        }

        juzawebFileManager(
            {
                type: type,
                prefix: prefix,
                button: item,
            },
            function (files) {
                let file = files[0];
                targetInput.val(file.path);
                if (targetInput.prop("name") == "icon") {
                    targetInput.trigger("change");
                }
                targetPreview.html('<img src="' + file.url + '" alt="">');
                targetName.html(file.name);
                item.addClass("previewing");
                item.find(".image-hidden").show();
                if (item.attr("data-file") == "true") {
                    item.find(".dropify-filename-inner").text(file.name);
                    item.find(".dropify-infos").css("opacity", 1);
                    console.log(item.find(".dropify-filename-inner"));
                    console.log(file.name);
                }
                if (targetInput.prop("name") == "thumbnail") {
                    setFacebookTwitterImage(
                        $('input[name="meta_og_image"]'),
                        file,
                        $(".facebook-preview"),
                    );
                    setFacebookTwitterImage(
                        $('input[name="meta_twitter_image"]'),
                        file,
                    );
                    $('input[name="meta_twitter_image_alt"]').val(file.text);
                }
                if (targetInput.prop("name") == "meta_og_image") {
                    let imagePreview1 = $(".facebook-preview").find(
                        'img[name="image_preview"]',
                    );
                    imagePreview1.attr("src", file.url);
                }

                if (targetInput.prop("name") == "meta_twitter_image") {
                    $('input[name="meta_twitter_image_alt"]').val(file.text);
                }
            },
        );
    });

    bodyElement.on("click", ".form-image .image-clear", function (e) {
        e.stopPropagation();
        let item = $(this).closest(".form-image");
        let targetInput = item.find(".input-path");
        let targetPreview = item.find(".dropify-render");
        let targetName = item.find(".dropify-filename-inner");
        targetInput.val("");
        if (targetInput.prop("name") == "icon") {
            targetInput.trigger("change");
        }
        targetPreview.html("");
        targetName.html("");
        item.removeClass("previewing");
        item.find(".image-hidden").hide();
    });

    bodyElement.on("click", ".add-image-images", function (e) {
        e.preventDefault();

        let button = $(this);
        button.css({ "pointer-events": "none", opacity: "0.5" });

        let prefix = juzaweb.adminPrefix + "/file-manager";
        let item = $(this).closest(".form-images");
        let inputName = item.find(".input-name").val();
        let parent = $(this).closest(".multi-files");
        var type = "image";
        if (parent.length > 0) {
            var formLang = $(".lang-switch").val();
            type = "file&lang=" + formLang;
        }
        juzawebFileManager(
            {
                type: type,
                prefix: prefix,
                multichoose: true,
                button: button,
            },
            function (files) {
                let temp = document.getElementById(
                    "form-images-template",
                ).innerHTML;
                let str = "";

                $.each(files, function (index, item) {
                    str += replace_template(temp, {
                        name: inputName,
                        url: item.url,
                        path: item.path,
                    });
                });

                item.find(".images-list .image-item:last").before(str);
            },
        );
    });

    bodyElement.on("click", ".form-images .remove-image-item", function () {
        $(this).closest(".image-item").remove();
    });

    // Initialize sortable for images
    function initSortableImages() {
        $(".sortable-images").sortable({
            items: ".image-item:not(.image-item-add)",
            cursor: "move",
            opacity: 0.8,
            placeholder: "image-item-placeholder",
            tolerance: "pointer",
            forcePlaceholderSize: true,
            start: function (e, ui) {
                ui.placeholder.height(ui.item.height());
            },
        });
    }

    // Initialize on page load
    initSortableImages();
});
