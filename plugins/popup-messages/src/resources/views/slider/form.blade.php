<div class="row">
    @php
        $status = ['1' => trans_cms('cms::app.active'), '0' => trans_cms('cms::app.inactive')];
    @endphp
    <div class="col-md-12">
        <ul id="banners" class="mt-5">
            <datalist id="theme-colors">
                @if (get_config('theme_colors'))
                    @foreach (get_config('theme_colors') as $color)
                        <option>{{ $color }}</option>
                    @endforeach
                @endif
            </datalist>
            @if ($banners = $data['banners'])
                @php
                    $themeColors = get_config('theme_colors');
                @endphp
                @foreach ($banners as $index => $banner)
                    @php
                        $banner = (object) $banner;
                        $bg_color_label = $text_color_label = '';
                        $bg_color = @$banner->bg_color;
                        if (isset($themeColors[$bg_color])) {
                            $bg_color_label = '(#' . intval($bg_color) + 1 . ')';
                            $bg_color = $themeColors[$bg_color];
                        }
                        $text_color = @$banner->text_color;
                        if (isset($themeColors[$text_color])) {
                            $text_color_label = '(#' . intval($text_color) + 1 . ')';
                            $text_color = $themeColors[$text_color];
                        }

                    @endphp
                    <li>
                        <div class="row banner-item">
                            <div class="col-md-3">
                                @component('cms::components.form_image', [
                                    'label' => trans_cms('popmes::content.banner'),
                                    'name' => 'images[]',
                                    'value' => $banner->image ?? '',
                                ])
                                @endcomponent

                                <div class="form-group d-flex justify-content-between">
                                    <label class="form-label">{{ trans_cms('cms::app.bg_color') }}
                                        <small>{{ $bg_color_label }}</small></label>
                                    <input type="color" name="bg_colors[]" list="theme-colors"
                                        value="{{ $bg_color }}">
                                </div>
                                <div class="form-group d-flex justify-content-between">
                                    <label class="form-label">{{ trans_cms('cms::app.text_color') }}
                                        <small>{{ $text_color_label }}</small></label>
                                    <input type="color" name="text_colors[]" list="theme-colors"
                                        value="{{ $text_color }}">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label">{{ trans_cms('cms::app.title') }}</label>
                                    <input type="text" class="form-control" name="titles[]" autocomplete="off"
                                        value="{{ @$banner->title }}">
                                </div>

                                <div class="form-group">
                                    {{ Field::editor(trans('cms::app.description'), 'descriptions[]', [
                                        'value' => @$banner->description,
                                    ]) }}
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="form-label">{{ trans_cms('cms::app.link') }}</label>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" name="links[]"
                                                    autocomplete="off" value="{{ @$banner->link }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2"><input
                                                            type="checkbox" class="link-new-tab" value="1"
                                                            @if (@$banner->new_tab == 1) checked @endif>
                                                        {{ trans_cms('cms::app.open_new_tab') }}</span>
                                                    <input type="hidden" name="new_tabs[]" class="new-tab"
                                                        value="{{ @$banner->new_tab }}">
                                                </div>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon3"><input
                                                            type="checkbox" class="link-new-tab" value="1"
                                                            @if (@$banner->btn_link == 1) checked @endif>
                                                        {{ trans_cms('cms::app.button_link') }}</span>
                                                    <input type="hidden" name="button_links[]" class="new-tab"
                                                        value="{{ @$banner->btn_link }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="col-form-label"
                                                for="btn_dir">{{ trans_cms('cms::app.btn_dir') }}</label>
                                            <select name="btn_dirs[]" id="btn_dir" class="form-control">
                                                @foreach ($btn_directions as $direction)
                                                    <option value="{{ $direction }}" @selected($direction == @$banner->btn_dir)>
                                                        {{ ucwords($direction) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>{{ trans_cms('cms::app.start_date') }}</label>
                                        <input type="datetime-local" name="s_date[]"
                                            value="{{ @$banner->date ? @$banner->date : now()->format('Y-m-d\TH:i') }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{ trans_cms('cms::app.end_date') }}</label>
                                        <input type="datetime-local" name="s_end_date[]"
                                            value="{{ @$banner->end_date ? @$banner->end_date : '' }}">
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-4">
                                        {{ Field::select(trans_cms('cms::app.status'), 'statuses[]', [
                                            'options' => $status,
                                            'value' => isset($banner->status) ? $banner->status : '',
                                        ]) }}
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
            <a href="javascript:void(0)"
                class="btn btn-primary add-banner">{{ trans_cms('cms::app.add_new_banner') }}</a>
        </div>
    </div>
</div>

<template id="banner-template">
    <li>
        <div class="row banner-item">
            <div class="col-md-3">
                @component('cms::components.form_image', [
                    'label' => trans_cms('popmes::content.banner'),
                    'name' => 'images[]',
                ])
                @endcomponent
                <div class="form-group d-flex justify-content-between">
                    <label class="form-label">{{ trans_cms('cms::app.bg_color') }}<small></small></label>
                    <input type="color" name="bg_colors[]" list="theme-colors" value="#ffffff">
                </div>
                <div class="form-group d-flex justify-content-between">
                    <label class="form-label">{{ trans_cms('cms::app.text_color') }}<small></small></label>
                    <input type="color" name="text_colors[]" list="theme-colors" value="#000000">
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label">{{ trans_cms('cms::app.title') }}</label>
                    <input type="text" class="form-control" name="titles[]" autocomplete="off" value="">
                </div>

                <div class="form-group">
                    <textarea class="form-control" name="descriptions[]" id="repeater-editor" rows="5"></textarea>
                </div>


                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label">{{ trans_cms('cms::app.link') }}</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="links[]" autocomplete="off">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2"><input type="checkbox"
                                            class="link-new-tab" value="1">
                                        {{ trans_cms('cms::app.open_new_tab') }}</span>
                                    <input type="hidden" name="new_tabs[]" class="new-tab" value="0">
                                </div>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon3"><input type="checkbox"
                                            class="link-new-tab" value="1">
                                        {{ trans_cms('cms::app.button_link') }}</span>
                                    <input type="hidden" name="button_links[]" class="new-tab" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-form-label" for="btn_dir">{{ trans_cms('cms::app.btn_dir') }}</label>
                            <select name="btn_dirs[]" id="btn_dir" class="form-control">
                                @foreach ($btn_directions as $direction)
                                    <option value="{{ $direction }}"> {{ ucwords($direction) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>{{ trans_cms('cms::app.start_date') }}</label>
                        <input type="datetime-local" name="s_date[]" value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ trans_cms('cms::app.end_date') }}</label>
                        <input type="datetime-local" name="s_end_date[]" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        {{ Field::select(trans_cms('cms::app.status'), 'statuses[]', [
                            'options' => $status,
                        ]) }}
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
    $("#banners").sortable();

    $("#banners").disableSelection();

    var themeColors = @json(get_config('theme_colors'));
    $("body").on('click', '.add-banner', function() {
        let temp = document.getElementById('banner-template').innerHTML;
        let length = $("#banners li").length + 1;
        let newbanner = replace_template(temp, {
            'length': length
        });

        let dynamicID = 'repeater-editor' + length;
        newbanner = newbanner.replace('id="repeater-editor"', 'id="repeater-editor' + length + '"');
        $("#banners").append(newbanner);

        init_editor(dynamicID, themeColors);

        $('.load-media').filemanager('image', {
            prefix:  juzaweb.adminUrl + '/file-manager'
        });
    });

    $("#banners").on('click', '.remove-banner', function() {
        let item = $(this);
        Swal.fire({
            title: '',
            text: '{{ trans_cms('popmes::content.are_you_sure_you_want_to_delete_this_banner') }}',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ trans_cms('cms::app.yes') }} !',
            cancelButtonText: '{{ trans_cms('cms::app.cancel') }} !',
        }).then((result) => {
            if (result.value) {
                item.closest('li').remove();
            }
        });
    });

    $("#banners").on('change', '.link-new-tab', function() {
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
