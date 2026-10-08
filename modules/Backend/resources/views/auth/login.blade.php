@extends('cms::layouts.auth')

@section('content')
    <div class="juzaweb-progmix">
        <div class="juzaweb-progmix--logo">
            <img src="https://progmix.dev/progmix_logo.svg" alt="Progmix">
        </div>
        <div class="juzaweb-progmix--box">
            <div class="juzaweb__auth__boxContainer1">
                <div class="text-dark font-size-24 mb-4">
                    <strong>{{ trans_cms('cms::message.login_form.header') }}</strong>
                </div>
                @if (isset($messages))
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
                @endif

                @if (session()->has('message'))
                    <div class="alert alert-{{ session()->get('status') == 'error' ? 'danger' : 'success' }} jw-message">
                        {{ session()->get('message') }}</div>
                @endif
                <form action="" method="post" class="mb-4 demo-form" id="demo-form">
                    @csrf
                    @do_action('login_form')

                    <div class="form-group mb-4">
                        <input type="email" name="email" class="form-control"
                            placeholder="{{ trans('cms::app.email_address') }}" required />

                    </div>

                    <div class="form-group mb-4">
                        <input type="password" name="password" class="form-control"
                            placeholder="{{ trans('cms::app.password') }}" required />
                    </div>

                    @error('email')
                        <span class="text-danger">{{ trans("cms::$message") }}</span>
                    @enderror

                    <button data-sitekey='{{ get_config('google_captcha.site_key') }}' data-callback='onSubmit'
                        data-action='Submit' type="submit" class="g-recaptcha btn btn-primary text-center w-100 mb-2"
                        data-loading-text="{{ trans('cms::app.please_wait') }}"><i class="fa fa-sign-in"></i>
                        {{ trans('cms::app.login') }}</button>

                    <input type="checkbox" name="remember" value="1" checked>
                    {{ trans('cms::app.remember_me') }}
                </form>
            </div>

            @if (get_config('user_registration'))
                <div class="text-center pt-2 mb-auto">
                    <span class="mr-2">{{ trans('cms::message.login_form.dont_have_an_account') }}</span>
                    <a href="{{ route('admin.register') }}" class="jw__utils__link font-size-16" data-turbolinks="false">
                        {{ __('Sign Up') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
    @push('scripts')
        <script nonce="{{ csp_nonce() }}">
            function onSubmit(token) {
                document.getElementById("demo-form").submit();
            }
        </script>
    @endpush
@endsection
