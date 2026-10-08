@extends('cms::layouts.backend')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="btn-group float-right">
                <div class="mr-3">
                    <div class="dropdown mr-4 d-none d-sm-block">
                        <a href="javascript:void(0)" class="dropdown-toggle text-nowrap btn btn-success"
                            data-toggle="dropdown">
                            <i class="fa fa-plus-circle"></i>
                            <span class="dropdown-toggle-text"> {{ trans_cms('cms::app.add_new') }}</span>
                        </a>
                        @error('form_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="dropdown-menu" role="menu">
                            @foreach (\Progmix\FormBuilder\Models\Form::TYPES as $typeKey => $typeValue)
                                <a class="dropdown-item"
                                    href="{{ route('create.form', $typeValue) }}">{{ trans_cms("formBuilder::content.add_new_$typeKey") }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $dataTable->render() }}
@endsection
