<div class="gallery">
    <div class="row">
        @foreach ($main_post->images as $key => $image)
            <div class="col-md-6 col-6">
                <a href="{{ upload_url($image, null, '1000x800') }}" title="{{ $main_post->title . ' - ' . $key }}"
                    class="gallery-item">
                    @php
                        $attr['alt'] = $main_post->title;
                    @endphp
                    {!! lazyloadImageResize(upload_url($image, null, '1000x800'), 590, 326, $attr) !!}
                </a>
            </div>
        @endforeach
    </div>
</div>
