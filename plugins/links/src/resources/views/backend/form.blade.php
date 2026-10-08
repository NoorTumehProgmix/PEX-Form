@extends('cms::layouts.backend')

@section('content')
    @component('cms::components.form_resource', [
        'model' => $model,
    ])
        <div class="row">
            <div class="col-md-8">

                {{ Field::text($model, 'name', [
                    'label' => trans_cms('links::content.title'),
                    'class' => !isset($model->id) ? 'generate-slug' : '',
                ]) }}
                {{ Field::text($model, 'slug', [
                    'required' => true,
                    'label' => trans_cms('links::content.slug'),
                ]) }}
                {{ Field::text($model, 'link', [
                    'required' => true,
                    'label' => trans_cms('links::content.name'),
                ]) }}

            </div>
        </div>
    @endcomponent
@endsection
