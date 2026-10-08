<div class="form-group">
    <label class="col-form-label" for="{{ $id }}">{{ $label ?? $name }}</label>
    {{-- <ul class="nav nav-tabs" id="content-{{ $name }}-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link @if (request()->query('e', 'editor') == 'editor') active @endif" id="editor-tab" href="?e=editor">Editor</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if (request()->query('e', 'editor') == 'html') active @endif" id="html-tab" href="?e=html">Html</a>
        </li>
    </ul> --}}
    <textarea class="form-control" name="{{ $name }}" id="{{ $id }}" rows="5">{{ $value ?? '' }}</textarea>

</div>
<div id="formio6" style="display: none"></div>
<div id="formio7" style="display: none"></div>
<div id="formio8" style="display: none"></div>
<div id="formio9" style="display: none"></div>
<div id="formio10" style="display: none"></div>
<script type="text/javascript">
    var themeColors = @json(get_config('theme_colors'));

    var colorMap = [];
    for (var color in themeColors) {
        var colorCode = themeColors[color].substring(1);
        colorMap.push(colorCode, themeColors[color]);
    }

    var transformedArray = @json($forms);
    var forms = transformedArray.map(item => {
        return {
            title: item.name,
            description: item.name,
            content: 'formBuilder2Yy#' + item.id + '!!'
        };
    });

    var tabeditor = '{{ request()->query('e', 'editor') }}';
    var storage_url = '{{ config('filesystems.disks.storage_server.images_url') }}';
    loadEditorContent(tabeditor);

    function loadEditorContent(tab) {
        if (tab === 'editor') {
            tinymce.init({
                selector: '#{{ $id }}',
                convert_urls: true,
                document_base_url: storage_url,
                urlconverter_callback: function(url, node, on_save, name) {
                    url = url.replace(storage_url, '');
                    return url;
                },
                height: 400,
                plugins: [
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table directionality",
                    "emoticons template paste textpattern advlist"
                ],
                extended_valid_elements: "iframe[src|frameborder|style|scrolling|class|width|height|name|align]",

                link_class_list: [{
                        title: 'None',
                        value: ''
                    },
                    {
                        title: 'Button',
                        value: 'btn btn-default'
                    },
                    {
                        title: 'Secondary Button',
                        value: 'btn btn-secondary'
                    },
                    {
                        title: 'Primary Outline Button',
                        value: 'btn btn-primary-outline'
                    },
                    {
                        title: 'Link',
                        value: 'read-more'
                    }
                ],
                templates: forms,
                menu: {
                    file: {
                        title: 'File',
                        items: 'newdocument restoredraft | preview | print '
                    },
                    edit: {
                        title: 'Edit',
                        items: 'undo redo | cut copy paste | selectall | searchreplace'
                    },
                    view: {
                        title: 'View',
                        items: 'code | visualaid visualchars visualblocks | spellchecker | preview fullscreen'
                    },
                    insert: {
                        title: 'Insert',
                        items: 'image link media template codesample inserttable | charmap emoticons hr | pagebreak nonbreaking anchor toc | insertdatetime'
                    },
                    format: {
                        title: 'Format',
                        items: 'bold italic underline strikethrough superscript subscript codeformat | formats blockformats fontformats fontsizes align lineheight | forecolor backcolor | removeformat'
                    },
                    tools: {
                        title: 'Tools',
                        items: 'spellchecker spellcheckerlanguage | code wordcount'
                    },
                    table: {
                        title: 'Table',
                        items: 'inserttable | cell row column | tableprops deletetable'
                    },
                },
                toolbar: [{
                        name: 'new',
                        items: ['newdocument']
                    },
                    {
                        name: 'history',
                        items: ['undo', 'redo']
                    },
                    {
                        name: 'styles',
                        items: ['styleselect']
                    },
                    {
                        name: 'formatting',
                        items: ['bold', 'italic']
                    },
                    {
                        name: 'alignment',
                        items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']
                    },
                    {
                        name: 'color',
                        items: ['forecolor', 'backcolor']

                    },

                    {
                        name: 'indentation',
                        items: ['outdent', 'indent', 'bullist', 'numlist']
                    },
                    {
                        name: 'media',
                        items: ['link', 'image', 'media']
                    },
                    {
                        name: 'view',
                        items: ['code', 'preview', 'fullscreen']
                    }
                ],
                color_map: colorMap,
                file_picker_callback: function(callback, value, meta) {
                    let x = window.innerWidth || document.documentElement.clientWidth || document
                        .getElementsByTagName('body')[0].clientWidth;
                    let y = window.innerHeight || document.documentElement.clientHeight || document
                        .getElementsByTagName('body')[0].clientHeight;
                    let cmsURL = '/' + juzaweb.adminPrefix + '/file-manager?editor=' + meta.fieldname;

                    if (meta.filetype == 'image') {
                        cmsURL = cmsURL + "&type=image";
                    } else {
                        cmsURL = cmsURL + "&type=file";
                    }

                    tinyMCE.activeEditor.windowManager.openUrl({
                        url: cmsURL,
                        title: 'Filemanager',
                        width: x * 0.8,
                        height: y * 0.8,
                        resizable: "yes",
                        close_previous: "no",
                        onMessage: (api, message) => {
                            callback(message.content);
                        }
                    });
                },
                setup: function(ed) {

                    ed.on('change', function(e) {
                        let title = $('input[name=title]').val();
                        let description = tinyMCE.activeEditor.getContent();
                        if (!$('.review-title').length) {
                            return false;
                        }

                        $.ajax({
                            type: 'POST',
                            url: juzaweb.adminUrl + '/ajax/seo-content',
                            dataType: 'json',
                            data: {
                                'title': title,
                                'description': description,
                            }
                        }).done(function(data) {
                            if (data.status === false) {
                                show_message(data);
                                return false;
                            }

                            if (!$("#meta_title").val()) {
                                $(".review-title").text(data.title);
                            }

                            if (!$("#meta_description").val()) {
                                $(".review-description").text(data.description);
                            }
                            return false;
                        }).fail(function(data) {
                            show_message(data);
                            return false;
                        });
                    })
                }
            });
        } else {
            var mixedMode = {
                name: "htmlmixed",
                scriptTypes: [{
                        matches: /\/x-handlebars-template|\/x-mustache/i,
                        mode: null
                    },
                    {
                        matches: /(text|application)\/(x-)?vb(a|script)/i,
                        mode: "vbscript"
                    }
                ]
            };

            var editor = CodeMirror.fromTextArea(document.getElementById("{{ $id }}"), {
                mode: mixedMode,
                lineNumbers: true,
                styleActiveLine: true,
                matchBrackets: true,
                lineWrapping: true,
                extraKeys: {
                    "Ctrl-Space": "autocomplete"
                },
            });

            editor.setOption("theme", 'default');
        }
    }
</script>
