<div class="form-group">
    @php
        $path = $value ?? null;
    @endphp
    <label class="col-form-label">{{ $label ?? $name }}</label>
    <div class="form-image text-center @if ($path) previewing @endif" data-file='true'>

        <a href="javascript:void(0)" class="image-clear">
            <i class="fa fa-times-circle fa-2x"></i>
        </a>

        <input type="hidden" name="{{ $name }}" class="input-path {{ @$data['class'] }}"
            value="{{ $path }}" data-name="{{ @$data['name'] }}">

        <div class="dropify-preview image-hidden" @if ($path) style="display: block" @endif>
            <div class="dropify-infos opacity-1">
                <div class="dropify-infos-inner">
                    <p class="dropify-filename">
                        <span class="dropify-filename-inner">{{ get_file_name($path) }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="icon-choose">
            <i class="fa fa-cloud-upload fa-5x"></i>
            <p>{{ trans_cms('cms::app.click_here_to_select_file') }}</p>
        </div>
    </div>
</div>
