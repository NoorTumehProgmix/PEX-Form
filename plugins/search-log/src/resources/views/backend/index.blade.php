@extends('cms::layouts.backend')

@section('content')
    <div class="row">
        <div class="col-md-12 mb-2">
            <div class="btn-group float-right">
                <a href="{{ route('search-log.export') }}" id="export-excel-btn" class="btn btn-success">
                    <i class="fa fa-file-excel-o"></i>{{ trans('cms::app.export_excel') }}
                </a>
            </div>
        </div>
    </div>

    {{ $dataTable->render() }}
@endsection
