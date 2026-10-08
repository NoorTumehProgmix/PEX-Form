@extends('cms::layouts.editor')

@section('buttons')
    <div class="btn-group">
        <button type="submit" class="btn btn-success px-5">
            <i class="fa fa-save"></i> {{ trans_cms('cms::app.save') }}
        </button>
        <button type="submit" data-type="new" data-create="true" class="btn btn-primary px-5">
            <i class="fa fa-save"></i> {{ trans_cms('cms::app.save_and_create') }}
        </button>
        <a href="" id="preview-post" data-id="0" class="btn btn-info px-5">
            <i class="fa fa-eye"></i> {{ trans_cms('cms::app.preview') }}
        </a>
        <button type="button" class="btn btn-warning cancel-button px-3">
            <i class="fa fa-refresh"></i> {{ trans_cms('cms::app.reset') }}
        </button>
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-9">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab1">General</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div id="tab1" class="tab-pane active">

                </div>
            </div>
        </div>
@endsection
