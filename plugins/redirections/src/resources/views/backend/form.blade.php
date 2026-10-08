@extends('cms::layouts.backend')

@section('content')
    @component('cms::components.form_resource', [
        'model' => $model,
    ])
        <div class="row">
            <div class="col-md-8">

                {{ Field::text($model, 'name', [
                    'label' => trans_cms('redirections::content.title'),
                ]) }}
                {{ Field::text($model, 'old_link', [
                    'required' => true,
                    'label' => trans_cms('redirections::content.old_link'),
                ]) }}
                {{ Field::text($model, 'new_link', [
                    'required' => true,
                    'label' => trans_cms('redirections::content.new_link'),
                ]) }}
            </div>
        </div>
    @endcomponent
@endsection
