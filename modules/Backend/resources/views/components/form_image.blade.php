<div class="form-group">
    @php
        $path = $value ?? null;
    @endphp
    <label class="col-form-label">{{ $label ?? $name }}</label>
    <div class="form-image text-center @if ($path) previewing @endif">

        <a href="javascript:void(0)" class="image-clear">
            <i class="fa fa-times-circle fa-2x"></i>
        </a>

        <input type="hidden" name="{{ $name }}" class="input-path {{ @$data['class'] }}"
            value="{{ $path }}" data-name="{{ @$data['name'] }}">

        <div class="dropify-preview image-hidden" @if ($path) style="display: block" @endif>
            <span class="dropify-render">
                @if (pathinfo($path, PATHINFO_EXTENSION) === 'mp4')
                    <video controls>
                        <source src="{{ upload_url($path) }}" type="video/mp4">
                    </video>
                @elseif (strpos($path, 'youtube.com') !== false)
                    <img src="{{ getYoutubeImage($path) }}" alt="">
                @elseif(pathinfo($path, PATHINFO_EXTENSION) === 'txt' || pathinfo($path, PATHINFO_EXTENSION) === 'json')
                    <img src="{{ asset('jw-styles/juzaweb/images/file.png') }}" title="{{ $path }}" alt="file icon">
                @else
                <img src="{{ upload_url($path, $default ?? null) }}" alt="">
                @endif
            </span>
            <div class="dropify-infos">
                <div class="dropify-infos-inner">
                    <p class="dropify-filename">
                        <span class="dropify-filename-inner"></span>
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
