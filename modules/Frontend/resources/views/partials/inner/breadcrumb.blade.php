<ul class="breadcrumbs list-plain">
    <li>
        <a href="{{ route('home') }}" class="item">
            {{ __('messages.home') }}
        </a>
    </li>
    @if (!empty($breadcrumbs))
        @foreach ($breadcrumbs as $key => $breadcrumb)
            <li>
                <a href="{{ $breadcrumb['path'] }}" title="{{ $breadcrumb['title'] }}" class="item">
                    {{ $breadcrumb['title'] }}
                </a>
            </li>
        @endforeach
    @endif
    @if (isset($main_post))
        <li>
            <span class="item active">{{ $main_post->title }}</span>
        </li>
    @endif
</ul>
