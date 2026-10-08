<div class="row">
    <div class="col-md-12">
        <ul id="repeater_items" class="mt-5">
            @if ($repeater_items = $data['repeaters'])

                <datalist id="theme-colors">
                    @if (get_config('theme_colors'))
                        @foreach (get_config('theme_colors') as $color)
                            <option>{{ $color }}</option>
                        @endforeach
                    @endif
                </datalist>
                @foreach ($repeater_items as $index => $row_item)
                    @php
                        $row_item = (object) $row_item;
                    @endphp
                    <li>
                        <div class="row repeater-item">
                            <div class="col-md-3">
                                @component('cms::components.form_image', [
                                    'label' => trans_cms('cms::app.thumbnail'),
                                    'name' => 'repeater_images[]',
                                    'value' => $row_item->image ?? '',
                                ])
                                @endcomponent
                                @component('cms::components.form_image', [
                                    'label' => trans_cms('cms::app.secondary_image'),
                                    'name' => 'repeater_secondary_images[]',
                                    'value' => $row_item->secondary_image ?? '',
                                ])
                                @endcomponent

                                @php
                                    $themeColors = get_config('theme_colors');
                                    $bg_color = @$row_item->bg_color;
                                    $bg_color_label = $text_color_label = '';
                                    if (isset($themeColors[$bg_color])) {
                                        $bg_color_label = '(#' . intval($bg_color) + 1 . ')';
                                        $bg_color = $themeColors[$bg_color];
                                    }
                                    $text_color = @$row_item->text_color;
                                    if (isset($themeColors[$text_color])) {
                                        $text_color_label = '(#' . intval($text_color) + 1 . ')';
                                        $text_color = $themeColors[$text_color];
                                    }
                                @endphp
                                <div class="form-group d-flex justify-content-between">
                                    <label class="form-label"
                                        style="font-size: 14px">{{ trans_cms('cms::app.bg_color') }}<small>
                                            {{ $bg_color_label }}</small></label>
                                    <input type="color" name="bg_colors[]" list="theme-colors"
                                        value="{{ $bg_color ?? '#ffffff' }}">
                                </div>
                                <div class="form-group d-flex justify-content-between">
                                    <label class="form-label"
                                        style="font-size: 14px">{{ trans_cms('cms::app.text_color') }}
                                        <small>{{ $text_color_label }}</small></label>
                                    <input type="color" name="text_colors[]" list="theme-colors"
                                        value="{{ $text_color ?? '#000000' }}">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label">{{ trans_cms('cms::app.title') }}</label>
                                    <input type="text" class="form-control" name="repeater_titles[]"
                                        autocomplete="off" value="{{ @$row_item->title }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ trans_cms('cms::app.subtitle') }}</label>
                                    <input type="text" class="form-control" name="repeater_subtitles[]"
                                        autocomplete="off" value="{{ @$row_item->subtitle }}">
                                </div>

                                <div class="form-group">
                                    {{ Field::editor(trans('cms::app.description'), 'repeater_descriptions[]', [
                                        'value' => @$row_item->description,
                                    ]) }}
                                </div>

                                <div class="form-group">
                                    <label class="form-label">{{ trans_cms('cms::app.link') }}</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="repeater_links[]"
                                            autocomplete="off" value="{{ @$row_item->link }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2"><input type="checkbox"
                                                    class="link-new-tab" value="1"
                                                    @if (@$row_item->new_tab == 1) checked @endif>
                                                {{ trans_cms('cms::app.open_new_tab') }}</span>
                                            <input type="hidden" name="repeater_new_tabs[]" class="new-tab"
                                                value="{{ @$row_item->new_tab }}">
                                        </div>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon3"><input type="checkbox"
                                                    class="link-new-tab" value="1"
                                                    @if (@$row_item->button_link == 1) checked @endif>
                                                {{ trans_cms('cms::app.button_link') }}</span>
                                            <input type="hidden" name="repeater_button_links[]" class="new-tab"
                                                value="{{ @$row_item->button_link }}">
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>{{ trans_cms('cms::app.start_date') }}</label>
                                        <input type="datetime-local" name="repeater_date[]"
                                            value="{{ @$row_item->date ? @$row_item->date : now()->format('Y-m-d\TH:i') }}">
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-1">
                                <a href="javascript:void(0)" class="text-danger remove-banner"><i
                                        class="fa fa-times-circle"></i></a>
                            </div>
                        </div>
                    </li>
                @endforeach
            @endif
        </ul>

        <div class="text-right mt-5">
            <a href="javascript:void(0)" class="add-item">{{ trans_cms('cms::app.add_new_item') }}</a>
        </div>
    </div>
</div>

<template id="repeater-template">
    <li>
        <div class="row repeater-item">
            <div class="col-md-3">
                @component('cms::components.form_image', [
                    'label' => trans_cms('cms::app.thumbnail'),
                    'name' => 'repeater_images[]',
                ])
                @endcomponent

                @component('cms::components.form_image', [
                    'label' => trans_cms('cms::app.secondary_image'),
                    'name' => 'repeater_secondary_images[]',
                ])
                @endcomponent
                <div class="form-group d-flex justify-content-between">
                    <label class="form-label">{{ trans_cms('cms::app.bg_color') }} <small></small></label>
                    <input type="color" name="bg_colors[]" list="theme-colors" value="#ffffff">
                </div>
                <div class="form-group d-flex justify-content-between">
                    <label class="form-label">{{ trans_cms('cms::app.text_color') }} <small></small></label>
                    <input type="color" name="text_colors[]" list="theme-colors" value="#000000">
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label">{{ trans_cms('cms::app.title') }}</label>
                    <input type="text" class="form-control" name="repeater_titles[]" autocomplete="off"
                        value="">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ trans_cms('cms::app.subtitle') }}</label>
                    <input type="text" class="form-control" name="repeater_subtitles[]" autocomplete="off"
                        value="">
                </div>

                <div class="form-group">
                    <textarea class="form-control" name="repeater_descriptions[]" id="repeater-editor" rows="5"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ trans_cms('cms::app.link') }}</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="repeater_links[]" autocomplete="off">
                        <div class="input-group-append">
                            <span class="input-group-text" id="basic-addon2"><input type="checkbox"
                                    class="link-new-tab" value="1">
                                {{ trans_cms('cms::app.open_new_tab') }}</span>
                            <input type="hidden" name="repeater_new_tabs[]" class="new-tab" value="0">
                        </div>
                        <div class="input-group-append">
                            <span class="input-group-text" id="basic-addon3"><input type="checkbox"
                                    class="link-new-tab" value="1">
                                {{ trans_cms('cms::app.button_link') }}</span>
                            <input type="hidden" name="repeater_button_links[]" class="new-tab" value="0">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>{{ trans_cms('cms::app.date') }}</label>
                        <input type="datetime-local" name="repeater_date[]"
                            value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>

                </div>
            </div>

            <div class="col-md-1">
                <a href="javascript:void(0)" class="text-danger remove-banner">
                    <i class="fa fa-times-circle fa-2x"></i>
                </a>
            </div>
        </div>
    </li>
</template>

<script type="text/javascript">
    $("#repeater_items").sortable();

    $("#repeater_items").disableSelection();
    var themeColors = @json(get_config('theme_colors'));

    $("body").on('click', '.add-item', function() {
        let temp = document.getElementById('repeater-template').innerHTML;
        let length = $("#repeater_items li").length + 1;
        let newbanner = replace_template(temp, {
            'length': length
        });
        let dynamicID = 'repeater-editor' + length;
        newbanner = newbanner.replace('id="repeater-editor"', 'id="repeater-editor' + length + '"');
        $("#repeater_items").append(newbanner);
        init_editor(dynamicID, themeColors);

        $('.load-media').filemanager('image', {
            prefix: juzaweb.adminUrl + '/file-manager'
        });
    });

    $("#repeater_items").on('click', '.remove-banner', function() {
        let item = $(this);
        Swal.fire({
            title: '',
            text: '{{ trans_cms('cms::app.are_you_sure_you_want_to_delete_this_item') }}',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ trans_cms('cms::app.yes') }}',
            cancelButtonText: '{{ trans_cms('cms::app.cancel') }}',
        }).then((result) => {
            if (result.value) {
                item.closest('li').remove();
            }
        });
    });

    $("#repeater_items").on('change', '.link-new-tab', function() {
        if ($(this).is(':checked')) {
            $(this).closest('.input-group-append').find('.new-tab').val(1);
        } else {
            $(this).closest('.input-group-append').find('.new-tab').val(0);
        }
    });
    $(document).on('change', 'input[type=color]', function() {
        var selectedColorName = $(this).val();
        var themeColors = {!! json_encode(get_config('theme_colors')) !!};
        var colorIndex = Object.keys(themeColors).find(key => themeColors[key] === selectedColorName);
        var colorLabel = $(this).siblings(".form-label").find("small");
        if (colorIndex !== undefined) {
            colorLabel.text("(#" + (parseInt(colorIndex) + 1) + ")");
        } else {
            colorLabel.text("");
        }
    });
</script>
