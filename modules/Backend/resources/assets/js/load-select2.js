function initSelect2(parent = "body") {
    $(parent + " .select2").select2({
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
    $(parent + " .load-features").select2({
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
    $(parent + " .load-values").select2({
        allowClear: true,
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        tags: true,
        placeholder: function (params) {
            return {
                id: null,
                text: params.placeholder,
            };
        },
    });

    $(parent + " .select2-default").select2({
        width: $(this).data("width") || "100%",
        dropdownAutoWidth: !$(this).data("width"),
    });

    //For form submissions export
    $("#search-forms")
        .select2()
        .on("change", function () {
            var selectedOption = $(this).val();
            var currentUrl = window.location.href;
            var baseUrl = currentUrl.substring(0, currentUrl.indexOf("?"));
            var newUrl = baseUrl + "?form=" + selectedOption;
            window.location.href = newUrl;
        });

    var formLang = $(".lang-switch").val();
    formLang = formLang ? formLang : "";

    $(parent + " .load-taxonomies").select2({
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
                    lang: formLang,
                    parent: parent,
                };
            },
        },
    });

    $(parent + " #city-select").select2({
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
            url: juzaweb.adminUrl + "/load-data/getCities",
            dataType: "json",
            data: function (params) {
                var countryId = $("#country-select").val();
                $("#city-select").empty();
                return {
                    countryId: countryId,
                    search: $.trim(params.term),
                    page: params.page,
                    lang: formLang,
                };
            },
        },
    });

    $("[data-taxonomy-child]").on("change", function () {
        let child = $(this).data("taxonomy-child");
        if (child) {
            $("#search-" + child)
                .val(null)
                .trigger("change");
        }
    });

    $(parent + " .load-taxonomies-parent").select2({
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
            url: juzaweb.adminUrl + "/load-data/loadTaxonomiesParent",
            dataType: "json",
            data: function (params) {
                let postType = $(this).data("post-type");
                let taxonomy = $(this).data("taxonomy");
                return {
                    search: $.trim(params.term),
                    post_type: postType,
                    taxonomy: taxonomy,
                    lang: formLang,
                };
            },
        },
    });

    $(parent + " .load-select-multi-tags").select2({
        allowClear: true,
        tags: true,
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        placeholder: function (params) {
            return {
                id: null,
                text: params.placeholder,
            };
        },
    });

    $(parent + " .load-select-multi-new-tags").select2({
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
        placeholder: function (params) {
            return {
                id: null,
                text: params.placeholder,
            };
        },
    });

    $(parent + " .load-select-multi").select2({
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
    $(parent + " .load-stores").select2({
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
                    page: params.page,
                };
            },
        },
    });

    $(parent + " .load-store-products").select2({
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
    $(parent + " .load-manufacturers").select2({
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

    $(parent + " .load-countries").select2({
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
            url: juzaweb.adminUrl + "/load-data/loadCountries",
            dataType: "json",
            data: function (params) {
                return {
                    search: $.trim(params.term),
                };
            },
        },
    });

    $(parent + " .load-cities").select2({
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
            url: juzaweb.adminUrl + "/load-data/loadCities",
            dataType: "json",
            data: function (params) {
                return {
                    search: $.trim(params.term),
                };
            },
        },
    });

    $(".state-parent").select2({
        placeholder: "Select State",
    });

    $(".city-child").select2({
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
            url: juzaweb.adminUrl + "/load-data/loadCities",
            dataType: "json",
            data: function (params) {
                return {
                    search: $.trim(params.term),
                    states: $(".state-parent").val(),
                };
            },
        },
    });

    $(".load-states").select2({
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
            url: juzaweb.adminUrl + "/load-data/loadStates",
            dataType: "json",
            data: function (params) {
                return {
                    country: $(".country-parent").val(),
                    search: $.trim(params.term),
                };
            },
        },
    });

    $(".country-parent").on("change", function () {
        const selectedCountry = $(this).val();
        const stateSelect = $(".state-parent");

        $.ajax({
            url: juzaweb.adminUrl + "/load-data/loadStates",
            method: "GET",
            data: { country: selectedCountry },
            success: function (data) {
                stateSelect.empty();
                if (data.results.length > 0) {
                    stateSelect.append(
                        new Option("All States", "all", true, true)
                    ); // Add "All States" option
                    $.each(data.results, function (index, state) {
                        stateSelect.append(new Option(state.text, state.id));
                    });
                }
                stateSelect.trigger("change"); // Trigger change for cascading update
            },
            error: function () {
                console.error("Failed to load states.");
            },
        });
    });

    $(parent + " .load-users").select2({
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
            url: "/" + juzaweb.adminPrefix + "/load-data/loadUsers",
            dataType: "json",
            data: function (params) {
                let explodes = $(this).data("explodes")
                    ? $(this).data("explodes")
                    : null;
                if (explodes) {
                    explodes = $("." + explodes)
                        .map(function () {
                            return $(this).val();
                        })
                        .get();
                }

                return {
                    search: $.trim(params.term),
                    page: params.page,
                    explodes: explodes,
                };
            },
        },
    });

    $(parent + " .load-menu").select2({
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
            url: "/" + juzaweb.adminPrefix + "/load-data/loadMenu",
            dataType: "json",
            data: function (params) {
                let explodes = $(this).data("explodes")
                    ? $(this).data("explodes")
                    : null;
                if (explodes) {
                    explodes = $("." + explodes)
                        .map(function () {
                            return $(this).val();
                        })
                        .get();
                }

                return {
                    search: $.trim(params.term),
                    page: params.page,
                    explodes: explodes,
                };
            },
        },
    });

    $(parent + " .load-pages").select2({
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
            url: "/" + juzaweb.adminPrefix + "/load-data/loadPages",
            dataType: "json",
            data: function (params) {
                return {
                    search: $.trim(params.term),
                    page: params.page,
                };
            },
        },
    });

    $(parent + " .load-posts").select2({
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
                    lang: formLang,
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

    $(parent + " .load-table").select2({
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

    $(document).ready(function () {
        let select2Element = $(".load-posts-category-tree");

        select2Element.select2({
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
                    let type = $(this).data("type")
                        ? $(this).data("type")
                        : null;
                    return {
                        search: $.trim(params.term),
                        page: params.page,
                        type: type,
                        lang: $(".post_lang").val(),
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

        $(".category-checkbox").on("change", function () {
            let id = $(this).data("id").toString();
            let name = $(this).next("span").text().trim();
            let selectedValues = select2Element.val() || [];
            if ($(this).is(":checked")) {
                if (!select2Element.find("option[value='" + id + "']").length) {
                    let newOption = new Option(name, id, true, true);
                    select2Element.append(newOption);
                }
                if (!selectedValues.includes(id)) {
                    selectedValues.push(id);
                }
            } else {
                selectedValues = selectedValues.filter((value) => value !== id);
                select2Element.find("option[value='" + id + "']").remove();
            }
            select2Element.val(selectedValues).trigger("change");
        });

        select2Element.on("change", function () {
            let selectedValues = select2Element.val() || [];
            $(".category-checkbox").each(function () {
                let id = $(this).data("id").toString();
                $(this).prop("checked", selectedValues.includes(id));
            });
        });

        let selectedValues = select2Element.val() || [];
        $(".category-checkbox").each(function () {
            let id = $(this).data("id").toString();
            $(this).prop("checked", selectedValues.includes(id));
        });
    });

    $(parent + " .load-links").select2({
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
            url: "/" + juzaweb.adminPrefix + "/load-data/loadGeneralLinks",
            dataType: "json",
            data: function (params) {
                return {
                    search: $.trim(params.term),
                    page: params.page,
                };
            },
        },
    });

    $(parent + " .load-locales").select2({
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
            url: "/" + juzaweb.adminPrefix + "/load-data/loadLocales",
            dataType: "json",
            data: function (params) {
                let type = $(this).data("type") ? $(this).data("type") : null;
                let explodes = $(this).data("explodes")
                    ? $(this).data("explodes")
                    : null;
                return {
                    search: $.trim(params.term),
                    page: params.page,
                    type: type,
                    explodes: explodes,
                };
            },
        },
    });

    $(parent + " .load-select2").select2({
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
            url: $(this).data("url") || "",
            dataType: "json",
            data: function (params) {
                return {
                    search: $.trim(params.term),
                    page: params.page,
                };
            },
        },
    });

    $(parent + " .load-resources").select2({
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
            url: juzaweb.adminUrl + "/load-data/loadResource",
            dataType: "json",
            data: function (params) {
                let type = $(this).data("type");
                let explodes = $(this).data("explodes");

                if (explodes) {
                    explodes = $("." + explodes)
                        .map(function () {
                            return $(this).val();
                        })
                        .get();
                }

                return {
                    search: $.trim(params.term),
                    page: params.page,
                    explodes: explodes,
                    type: type,
                    lang: formLang,
                };
            },
        },
    });

    $(parent + " .load-subscription-objects").select2({
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
            url: juzaweb.adminUrl + "/load-data/loadSubscriptionObjects",
            dataType: "json",
            data: function (params) {
                let module = $(this).data("module");

                return {
                    search: $.trim(params.term),
                    page: params.page,
                    module: module,
                };
            },
        },
    });

    $(parent + " .media-size").select2({
        tags: true,
        tokenSeparators: [",", "\t", "\n"],
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        minimumResultsForSearch: -1,
        dropdownParent: $(".tags-only"),
    });
    $(parent + " #hosts").select2({
        tags: true,
        tokenSeparators: [",", "\t", "\n"],
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        minimumResultsForSearch: -1,
        dropdownParent: $(".tags-only"),
    });
    $(parent + " #search_keywords").select2({
        tags: true,
        tokenSeparators: [",", "\t", "\n"],
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        minimumResultsForSearch: -1,
        dropdownParent: $(".tags-only"),
    });
    $(parent + " #meta_keywords").select2({
        tags: true,
        tokenSeparators: [",", "\t", "\n"],
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        minimumResultsForSearch: -1,
        dropdownParent: $(".tags-only"),
    });
    $(parent + " #site_keywords").select2({
        tags: true,
        tokenSeparators: [",", "\t", "\n"],
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        minimumResultsForSearch: -1,
        dropdownParent: $(".tags-only"),
    });
    $(parent + " #meta_title_keywords").select2({
        tags: true,
        tokenSeparators: [",", "\t", "\n"],
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        minimumResultsForSearch: -1,
        dropdownParent: $(".tags-only"),
    });

    $(parent + " #theme_colors").select2({
        tags: true,
        tokenSeparators: [",", " ", "\t", "\n"],
        dropdownAutoWidth: !$(this).data("width"),
        width: $(this).data("width") || "100%",
        minimumResultsForSearch: -1,
        dropdownParent: $(".colors-select"),
        templateSelection: function (selection) {
            if (!selection.id || typeof selection.element === "undefined") {
                return selection.text;
            }
            return $(
                '<span class="color-box" style="background-color:' +
                    selection.id +
                    ';"></span><span>' +
                    selection.text +
                    "</span>"
            );
        },
        templateResult: function (data) {
            if (!data.id) {
                return data.text;
            }
            return $(
                '<span class="color-box" style="background-color:' +
                    data.id +
                    ';"></span><span>' +
                    data.text +
                    "</span>"
            );
        },
    });
}

$(document).ready(function () {
    initSelect2("body");
});
