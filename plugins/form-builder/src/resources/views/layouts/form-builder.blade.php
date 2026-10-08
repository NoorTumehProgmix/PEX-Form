@extends('cms::layouts.backend')
@section('header')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.form.io/js/formio.full.min.css">
    {{ Vite::useBuildDirectory('plugins/form-builder') }}
    @vite('plugins/form-builder/src/resources/assets/app.js')
    @vite('plugins/form-builder/src/resources/assets/app.css')
@endsection

@section('content')
    @yield('form-builder-content')
@endsection
