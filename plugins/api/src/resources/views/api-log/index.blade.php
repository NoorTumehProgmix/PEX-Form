@extends('cms::layouts.backend')

@section('content')
    <div class="d-flex justify-content-end align-items-center w-100">
    </div>
    {{ $dataTable->render() }}
@endsection
