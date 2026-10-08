@extends('cms::layouts.auth')

@section('content')
    <div class="juzaweb-progmix">
        <div class="juzaweb-progmix--logo">
            <img src="https://progmix.dev/progmix_logo.svg" alt="Progmix">
        </div>
        <div class="juzaweb-progmix--box">
            <div class="juzaweb__auth__boxContainer1">
                <div class="text-dark font-size-24 mb-4">
                    <strong>{{ trans_cms('cms::app.confirm_password') }}</strong>
                </div>
                <form action="{{ route('password.confirm') }}" method="post" class="mb-4">
                    <div class="form-group mb-4">
                        <input type="password" name="password" class="form-control"
                            placeholder="{{ trans_cms('cms::app.password') }}" autocomplete="off" required />
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary text-center w-100">
                        {{ trans_cms('cms::app.confirm_password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
