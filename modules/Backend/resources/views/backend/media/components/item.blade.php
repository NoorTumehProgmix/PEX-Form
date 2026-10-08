<li class="media-item" style="height:auto;" title="{{ $item->name }}">
    <a href="{{ $item instanceof \Juzaweb\Backend\Models\MediaFolder ? route('admin.media.folder', [$item->id]) : 'javascript:void(0)' }}" class="media-item-info @if($item instanceof \Juzaweb\Backend\Models\MediaFile) media-file-item @endif" data-id="{{ $item->id }}">
        @php
        $arr = $item->toArray();
        // $arr['url'] = get_full_url(upload_url($item->path), url('/'));
        $arr['url'] = url(upload_url($item->path));

        $arr['thumb'] = url(upload_url($item->path));
        if($item->type == 'url'){
            $arr['thumb'] = "https://i.ytimg.com/vi/$item->mime_type/hqdefault.jpg";
        }
        $arr['updated'] = jw_date_format($item->updated_at);
        $arr['type'] = $item->type;
        $arr['size'] = format_size_units($item->size);
        $arr['is_file'] = $item instanceof \Juzaweb\Backend\Models\MediaFile ? 1 : 0;

            $icons =  [
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
            'ppt' => 'fa-file-powerpoint-o',
            'pptx' => 'fa-file-powerpoint-o',
            'mp4' => 'fa-file-video-o',
            'mp3' => 'fa-file-audio-o',
            'jfif' => 'fa-file-image-o',
            'txt' => 'fa-file-text-o',
            'youtube' => 'fa-youtube-play',
        ];
        $icon = $icons[strtolower($item->extension)] ?? 'fa-file-o';

        @endphp
        <textarea class="d-none item-info">@json($arr)</textarea>
        <div class="attachment-preview">
            <div class="thumbnail">
                <div class="centered">
                    @if($item instanceof \Juzaweb\Backend\Models\MediaFolder)
                        <img src="{{ asset('jw-styles/juzaweb/images/folder.png') }}" alt="{{ $item->name }}">
                    @else
                        @if($item->type == 'image')
                            <img class="lazyload" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="{{ upload_url($item->path, null, '150x150') }}" alt="{{ $item->name }}">
                        @elseif($item->type == 'url')
                        <img class="lazyload" src="https://i.ytimg.com/vi/{{$item->mime_type}}/hqdefault.jpg" data-src="https://i.ytimg.com/vi/{{$item->mime_type}}/hqdefault.jpg" alt="{{ $item->name }}">
                        <i style="color:#ff0101;position: absolute;left: 0;font-size: 50px;transform: translate(-50%,-50%);" class="fa {{$icon}}"></i>
                        @else
                        <i style="position: absolute;left: 0;font-size: 50px;transform: translate(-50%,-50%);" class="fa {{$icon}}"></i>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="media-name">
            <span>{{ $item->name }}</span>
        </div>
    </a>
</li>
