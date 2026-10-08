@component('cms::components.card', [
    'label' => $label ?? $name,
])
    @php
        $value = is_array($value) ? $value : json_decode($value);
        $paths = $value ?? [];
    @endphp

    <div class="form-images">
        <input type="hidden" class="input-name" value="{{ $name }}[]">
        <div class="images-list sortable-images">
            @foreach ($paths as $path)
                @php
                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                    if ($extension == '') {
                        $extension = 'youtube';
                    }
                    $icons = [
                        'pdf' => 'fa-file-pdf-o',
                        'doc' => 'fa-file-word-o',
                        'docx' => 'fa-file-word-o',
                        'xls' => 'fa-file-excel-o',
                        'xlsx' => 'fa-file-excel-o',
                        'rar' => 'fa-file-archive-o',
                        'zip' => 'fa-file-archive-o',
                        'gif' => 'fa-file-image-o',
                        'jpg' => 'fa-file-image-o',
                        'jpeg' => 'fa-file-image-o',
                        'png' => 'fa-file-image-o',
                        'webp' => 'fa-file-image-o',
                        'ppt' => 'fa-file-powerpoint-o',
                        'pptx' => 'fa-file-powerpoint-o',
                        'mp4' => 'fa-file-video-o',
                        'mp3' => 'fa-file-audio-o',
                        'jfif' => 'fa-file-image-o',
                        'txt' => 'fa-file-text-o',
                        'youtube' => 'fa-youtube-play',
                    ];
                    $icon = $icons[strtolower($extension)] ?? 'fa-file-o';

                @endphp

                @component('cms::components.image-item', [
                    'name' => "{$name}[]",
                    'path' => $path,
                    'url' => upload_url($path),
                    'icon' => $icon,
                ])
                @endcomponent
            @endforeach

            <div class="image-item border image-item-add">
                <a href="javascript:void(0)" class="text-secondary add-image-images">
                    <i class="fa fa-plus fa-5x"></i>
                </a>
            </div>
        </div>
    </div>
@endcomponent
