@extends('cms::layouts.auth')

@section('content')
    <div class="juzaweb-progmix">
        <div class="juzaweb-progmix--logo">
            <img src="https://progmix.dev/progmix_logo.svg" alt="Progmix">
        </div>
        <div class="juzaweb-progmix--box">
            <div class="juzaweb__auth__boxContainer1">
                <div class="text-dark font-size-24 mb-4">
                    <strong>{{ trans_cms('cms::message.2fa.header') }}</strong>
                    <div><small>{{ trans_cms('cms::message.2fa.description') }}</small></div>
                </div>
                <form action="{{ route('two-factor.login') }}" method="post" class="mb-4">
                    @csrf
                    <div class="form-group mb-4">

                        <input type="text" name="code" class="form-control"
                            placeholder="{{ trans('cms::app.2fa_code') }}" required />
                        @error('code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary text-center w-100 mb-2">
                        {{ trans('cms::app.send') }}</button>

                </form>
            </div>


        </div>
    </div>
@endsection
