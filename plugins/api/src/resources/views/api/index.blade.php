@extends('cms::layouts.backend')

@section('content')
     <div class="row">
        <div class="col-md-12">
            <div class="btn-group float-right">
                <a href="{{route('api.create')}}" class="btn btn-success"><i class="fa fa-plus-circle"></i> {{ trans_cms('cms::app.add_new') }}</a>
            </div>
        </div>
    </div>
    {{ $dataTable->render() }}
@endsection
