@php
    $registerPage = get_page_by_template('RegisterHome', ['pages']);
@endphp
<section class="register" id="register" aria-labelledby="reg-title">
    <div class="container">
        <div class="reveal section-header section-header--register">
            <span class="badge badge--highlight">{!! $registerPage?->subtitle !!}</span>
            <h2 class="section-title" id="reg-title">{!! $registerPage?->title !!}</h2>
            <div class="gold-line" aria-hidden="true"></div>
            <p class="section-sub register-cta-text">{!! $registerPage?->content !!}</p>
        </div>

        <div class="register-cta reveal">
            <a href="{{ route('forum-registration.show') }}" class="btn-primary">
                <i class="icon-user-plus-solid-full icon"></i>
                {{ __('messages.register_now') }}
            </a>
        </div>
    </div>
</section>