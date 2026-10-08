@extends('cms::installer.layouts.master-update')

@section('title', trans_cms('cms::installer.updater.welcome.title'))
@section('container')
    <p class="paragraph text-center">
    	{{ trans_cms('cms::installer.updater.welcome.message') }}
    </p>
    <div class="buttons">
        <a href="{{ route('LaravelUpdater::overview') }}" class="button">{{ trans_cms('cms::installer.next') }}</a>
    </div>
@stop
