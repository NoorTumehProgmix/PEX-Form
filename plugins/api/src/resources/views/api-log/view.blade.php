@extends('cms::layouts.backend')
@section('header')
    {{ Vite::useBuildDirectory('plugins/api') }}
    @vite('plugins/api/src/resources/assets/app.css')
@endsection
@section('content')
    <div class="row mt-4 mb-2">
        <div class="col-md-12">
            <h4>{{ $apiLog->api?->name }}</h4>

        </div>
    </div>
    <table class="table table-striped">
        <tr>
            <th class="field">Field</th>
            <th>Value</th>
        </tr>
        <tbody>
            @forelse ($apiLog->getAttributes() as $key=>$value)
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ $value }}</td>
                </tr>
            @empty
                <div class="text-danger text-center">No data</div>
            @endforelse
        </tbody>

    </table>
@endsection
