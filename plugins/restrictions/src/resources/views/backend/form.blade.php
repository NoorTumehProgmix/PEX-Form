@extends('cms::layouts.backend')

@section('content')
@component('cms::components.form_resource', [
'model' => $model,
])
<div class="row">
    <div class="col-md-8">

            {{ Field::text($model, 'ip', [
                    'label' => trans_cms('restrictions::content.ip'),
                    'required' => true,
                ]) }}
            {{ Field::text($model, 'notes', [
                    'label' => trans_cms('restrictions::content.notes'),
            ]) }}

    </div>
</div>
@endcomponent
@endsection
