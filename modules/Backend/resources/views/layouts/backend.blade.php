<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? '' }}</title>
    <link rel="icon" href="{{ asset('jw-styles/juzaweb/images/favicon.ico') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Mukta:300,400,400i,700&display=swap" />
    @include('cms::components.juzaweb_langs')


    {{-- <script src="{{ asset('assets/js/backend.js') }}"></script> --}}

    @do_action('juzaweb_header')

    @yield('header')

</head>
{{-- {{ dd(request()->path()) }} --}}
{{-- {{ dd(Route::currentRouteName()) }} --}}

<body class="juzaweb__menuLeft--unfixed">
    <div id="admin-overlay">
        <div class="cv-spinner">
            <span class="spinner"></span>
        </div>
    </div>

    <div class="juzaweb__layout juzaweb__layout--hasSider">

        <div class="juzaweb__menuLeft">
            <div class="juzaweb__menuLeft__mobileTrigger"><span></span></div>

            <div class="juzaweb__menuLeft__outer">
                <div class="juzaweb__menuLeft__logo__container">
                    <a href="/{{ config('juzaweb.admin_prefix') }}" class="juzaweb__menuLeft__logo">
                        <img src="https://progmix.dev/progmix_logo_w.svg" alt="Progmix">
                    </a>

                </div>

                <div class="juzaweb__menuLeft__scroll jw__customScroll">
                    @include('cms::backend.menu_left')
                </div>
            </div>
        </div>
        <div class="juzaweb__menuLeft__backdrop"></div>

        <div class="juzaweb__layout">
            <div class="juzaweb__layout__header">
                @include('cms::backend.menu_top')
            </div>

            <div class="juzaweb__layout__content">
                @if (!request()->is(config('juzaweb.admin_prefix')))
                    {{ jw_breadcrumb('admin', [
                        [
                            'title' => $title,
                        ],
                    ]) }}
                @else
                    <div class="mb-3"></div>
                @endif


                {{--                <h4 class="font-weight-bold ml-3 text-capitalize">{{ $title }}</h4> --}}

                <div class="juzaweb__utils__content">

                    @do_action('backend_message')

                    @php
                        $messages = get_backend_message();
                    @endphp

                    @foreach ($messages as $message)
                        <div
                            class="alert alert-{{ $message['status'] == 'error' ? 'danger' : $message['status'] }} jw-message">
                            <button type="button" class="close close-message" data-dismiss="alert" aria-label="Close"
                                data-id="{{ $message['id'] }}">
                                <span aria-hidden="true">×</span>
                            </button>
                            {!! e_html($message['message']) !!}
                        </div>
                    @endforeach

                    @if (session()->has('message'))
                        <div
                            class="alert alert-{{ session()->get('status') == 'error' ? 'danger' : 'success' }} jw-message">
                            {{ session()->get('message') }}</div>
                    @endif

                    <div id="jquery-message"></div>

                    @yield('content')
                </div>
            </div>

            <div class="juzaweb__layout__footer">
                <div class="juzaweb__footer">
                    <div class="juzaweb__footer__inner">
                        <!-- <a href="https://progmix.dev" target="_blank" rel="noopener noreferrer" class="juzaweb__footer__logo">
                        ProgmiX - Building Bigger Ideas Together
                        <span></span>
                    </a>
                    <br />
                    <p class="mb-0">
                        Copyright © 2020 {{ get_config('title') }} - Provided by Progmix
                    </p> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="form-images-template">
        @component('cms::components.image-item', [
            'name' => '{name}',
            'path' => '{path}',
            'url' => '{url}',
            'icon' => '{icon}',
        ])
        @endcomponent
    </template>

    <div id="show-modal"></div>

    <form action="{{ route('logout') }}" method="post" style="display: none" class="form-logout">
        @csrf
    </form>

    <script type="text/javascript">
        $.extend($.validator.messages, {
            required: "{{ trans_cms('cms::app.this_field_is_required') }}",
        });

        $(".form-ajax").validate();

        $(".auth-logout").on('click', function() {
            $('.form-logout').submit();
        });
    </script>

    <script>
        var lang = @json(trans_cms('cms::filemanager'));
        var actions = [];

        var multi_selection_enabled = $("#juzawebFileManagerModal").data("multi");
    </script>
    <script src="{{ asset('jw-styles/juzaweb/js/filemanager.min.js') }}?v=0.0.3"></script>


    @do_action('juzaweb_footer')

    @yield('footer')
    <div class="filemanager-wrapper"></div>

</body>

</html>
