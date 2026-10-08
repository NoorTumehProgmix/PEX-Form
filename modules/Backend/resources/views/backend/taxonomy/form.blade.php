@extends('cms::layouts.backend')

@section('content')
    @php
        $type = $setting->get('type');
        $postType = $setting->get('post_type');
    @endphp

    @component('cms::components.form_resource', [
        'model' => $model,
    ])
        <div class="row">
            <div class="col-md-8">

                @if (isset($data['related_ids']))
                    @foreach ($data['related_ids'] as $lang => $id)
                        <input type="hidden" class="related_ids" value="{{ $id }}" id="{{ $lang }}">
                    @endforeach
                @endif

                {{ Field::text($model, 'name', [
                    'required' => true,
                    'class' => empty($model->slug) ? 'generate-slug' : '',
                ]) }}
                @if (in_array('slug', $setting->get('supports', [])))
                    {{ Field::slug($model, 'slug') }}
                @endif
                {{ Field::textarea($model, 'description') }}

                @if (in_array('hierarchical', $setting->get('supports', [])))
                    <div class="form-group">
                        <label class="col-form-label" for="parent_id">{{ trans_cms('cms::app.parent') }}</label>
                        <select name="parent_id" id="parent_id" class="form-control load-taxonomies"
                            data-post-type="{{ $setting->get('post_type') }}" data-taxonomy="{{ $setting->get('taxonomy') }}"
                            data-placeholder="{{ trans_cms('cms::app.parent') }}" data-explodes="{{ $model->id }}">
                            @if ($model->parent)
                                <option value="{{ $model->parent->id }}" selected>{{ $model->parent->name }}</option>
                            @endif
                        </select>
                    </div>
                @endif

                @if (isset($setting['supports']) and isset($setting['supports']['parents']))
                    @component('cms::components.form_select_taxonomy_parent', [
                        'name' => 'parent_id',
                        'label' => $setting['supports']['parents']['label'] ?? '',
                        'parents' => $setting['supports']['parents']['name'] ?? '',
                        'value' => $model->parent_id,
                        'lang' => $model->lang,
                    ])
                    @endcomponent
                @endif

                @if (isset($setting['supports']) and isset($setting['supports']['display_order']))
                    <div class="form-group">
                        <label class="col-form-label">{{ trans_cms('cms::app.display_order') }}</label>
                        <input type="number" name="display_order" class="form-control"
                            value="{{ $model->display_order ?? 100 }}" />
                    </div>
                @endif
                @if ($taxonomy != 'tags')
                    {{ Field::select($model, 'lang', [
                        'options' => $data['langs'],
                        'class' => 'lang-switch',
                    ]) }}
                @endif
            </div>

            @if (in_array('thumbnail', $setting->get('supports', [])))
                <div class="col-md-4">
                    @component('cms::components.form_image', [
                        'name' => 'thumbnail',
                        'label' => trans_cms('cms::app.thumbnail'),
                        'value' => $model->thumbnail,
                    ])
                    @endcomponent
                </div>
            @endif



            <input type="hidden" name="post_type" value="{{ $postType }}">
            <input type="hidden" name="taxonomy" value="{{ $taxonomy }}">
        </div>
    @endcomponent
@endsection
