@extends('cms::layouts.backend')

@section('content')
    <div class="row mt-4 mb-3">
        <div class="col-md-8">
            <form action="{{ route('admin.setting.media.fetch-resize') }}" method="POST" class="mb-4">
                @csrf
                <button type="submit" class="btn btn-primary">Fetch Posts</button>
            </form>

            @if (session()->has('posts1Count'))
                <p>Posts for Queue 1: {{ session('posts1Count') }} posts</p>
            @endif

            @if (session()->has('posts2Count'))
                <p>Posts for Queue 2: {{ session('posts2Count') }} posts</p>
            @endif

            <!-- Form to dispatch jobs -->
            @if (session()->has('posts1Count'))
                <form action="{{ route('admin.setting.media.dispatch-jobs') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Dispatch Jobs</button>
                </form>
            @endif

            <form action="{{ route('admin.theme.setting') }}" method="post" class="form-ajax">
                <h4>{{ trans_cms('cms::app.media_setting.thumbnail_settings') }}</h4>

                <input type="hidden" name="config[webp_extension]" value="0">
                {{ Field::checkbox(trans_cms('cms::app.media_setting.image_extension'), 'config[webp_extension]', [
                    'checked' => get_config('webp_extension') ?? false,
                ]) }}

                @foreach ($postTypes as $key => $postType)
                    <h5>{{ $postType->get('label') }}</h5>
                    <label>{{ trans_cms('cms::app.media_setting.thumbnail_size') }}</label>
                    @php
                        $thumbnailSize = get_thumbnail_size($key, $thumbnailSizes ?? []);
                    @endphp
                    <div class="row">
                        <div class="col-md-6 tags-only">
                            <select name="theme[thumbnail_sizes][{{ $key }}][size][]"
                                class="form-control media-size" multiple="multiple">
                                @foreach ($thumbnailSize as $size)
                                    <option value="{{ $size }}" selected>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    {{ Field::text(trans_cms('cms::app.media_setting.quality'), "config[thumbnail_quality][{$key}]", [
                        'value' => get_config('thumbnail_quality', [])[$key] ?? '',
                    ]) }}
                    {{ Field::checkbox(
                        trans_cms('cms::app.media_setting.auto_resize_thumbnail'),
                        "config[auto_resize_thumbnail][{$key}]",
                        [
                            'checked' => get_config('auto_resize_thumbnail', [])[$key] ?? false,
                        ],
                    ) }}

                    {{ Field::image(trans_cms('cms::app.media_setting.thumbnail_default'), "config[thumbnail_defaults][{$key}]", [
                        'value' => $thumbnailDefaults[$key] ?? null,
                    ]) }}
                @endforeach

                <h5>{{ trans_cms('cms::app.media_setting.gallery') }}</h5>
                <label>{{ trans_cms('cms::app.media_setting.thumbnail_size') }}</label>
                @php
                    $galleryThumbnailSize = get_thumbnail_size('gallery', $thumbnailSizes ?? []);
                @endphp
                <div class="row">
                    <div class="col-md-6 tags-only">
                        <select name="theme[thumbnail_sizes][gallery][size][]" class="form-control media-size"
                            multiple="multiple">
                            @foreach ($galleryThumbnailSize as $size)
                                <option value="{{ $size }}" selected>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{ Field::text(trans_cms('cms::app.media_setting.quality'), 'config[thumbnail_quality][gallery]', [
                    'value' => get_config('thumbnail_quality', [])['gallery'] ?? '',
                ]) }}
                {{ Field::checkbox(
                    trans_cms('cms::app.media_setting.auto_resize_thumbnail'),
                    'config[auto_resize_thumbnail][gallery]',
                    [
                        'checked' => get_config('auto_resize_thumbnail', [])['gallery'] ?? false,
                    ],
                ) }}

                {{ Field::image(trans_cms('cms::app.media_setting.thumbnail_default'), 'config[thumbnail_defaults][gallery]', [
                    'value' => $thumbnailDefaults['gallery'] ?? null,
                ]) }}

                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i>
                    {{ trans_cms('cms::app.save_change') }}
                </button>
            </form>
        </div>
    </div>
@endsection
