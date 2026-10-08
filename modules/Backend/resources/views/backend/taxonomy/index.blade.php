@extends('cms::layouts.backend')

@section('content')

    <div class="row">
        @if ($canCreate)
            <div class="col-md-4">
                <h5>{{ trans_cms('cms::app.add_new') }}</h5>
                @php
                    $type = $setting->get('type');
                    $postType = $setting->get('post_type');

                @endphp

                <form method="post" action="{{ route('admin.taxonomies.store', [$postType, $taxonomy]) }}" class="form-ajax"
                    data-success="reload_data" id="form-add">

                    @component('cms::components.form_input', [
                        'name' => 'name',
                        'label' => trans_cms('cms::app.name'),
                        'value' => '',
                        'required' => true,
                        'class' => in_array('slug', $setting->get('supports', [])) ? 'generate-slug' : '',
                    ])
                    @endcomponent
                    @if (in_array('slug', $setting->get('supports', [])))
                        <label>{{ trans_cms('cms::app.slug') }}</label>
                        {{ Field::slug('', 'slug') }}
                    @endif
                    @component('cms::components.form_textarea', [
                        'name' => 'description',
                        'rows' => '3',
                        'label' => trans_cms('cms::app.description'),
                        'value' => '',
                    ])
                    @endcomponent

                    @if (in_array('hierarchical', $setting->get('supports', [])))
                        <div class="form-group">
                            <label class="col-form-label" for="parent_id">{{ trans_cms('cms::app.parent') }}</label>
                            <select name="parent_id" id="parent_id" class="form-control load-taxonomies"
                                data-post-type="{{ $setting->get('post_type') }}"
                                data-taxonomy="{{ $setting->get('taxonomy') }}"
                                data-placeholder="{{ trans_cms('cms::app.parent') }}">
                            </select>
                        </div>
                    @endif
                    @if (
                        !in_array($taxonomy, [
                            'tags',
                            'branch_type',
                            'filter',
                            'status_group',
                            'city',
                            'area',
                            'region',
                            'years',
                            'quarters',
                            'types',
                            'categories',
                        ]))
                        <div class="form-group">
                            <label class="col-form-label" for="lang">{{ trans_cms('cms::app.lang') }}</label>
                            <select class="form-control lang-switch" id="lang" name="lang">
                                @foreach ($langs as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="lang" value="{{ Lang::getLocale() }}">
                    @endif
                    <input type="hidden" name="post_type" value="{{ $postType }}">
                    <input type="hidden" name="taxonomy" value="{{ $taxonomy }}">

                    @if (in_array('thumbnail', $setting->get('supports', [])))
                        @component('cms::components.form_image', [
                            'name' => 'thumbnail',
                            'label' => trans_cms('cms::app.thumbnail'),
                        ])
                        @endcomponent
                    @endif

                    @if (isset($setting['supports']) and isset($setting['supports']['parents']))
                        @component('cms::components.form_select_taxonomy_parent', [
                            'name' => 'parent_id',
                            'label' => $setting['supports']['parents']['label'],
                            'parents' => $setting['supports']['parents']['name'],
                            'lang' => \Juzaweb\Backend\Models\Language::where('code', config('app.dashboard_locale'))?->get()?->first()?->code,
                        ])
                        @endcomponent
                    @endif

                    @if (isset($setting['supports']) and isset($setting['supports']['display_order']))
                        <div class="form-group">
                            <label class="col-form-label">{{ trans_cms('cms::app.display_order') }}</label>
                            <input type="number" name="display_order" class="form-control" value="100" />
                        </div>
                    @endif
                    <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i>
                        {{ trans_cms('cms::app.add') }} {{ $setting->get('label') }}
                    </button>
                </form>
            </div>
        @endif

        <div class="@if ($canCreate) col-md-8 @else col-md-12 @endif">
            {{ $dataTable->render() }}
        </div>
    </div>

    <script type="text/javascript">
        function reload_data(form) {
            $('#form-add input[type="text"], #form-add textarea').val(null);
            $('#form-add #parent_id').val(null).trigger('change.select2');
            table.refresh();
        }
    </script>
    <style>
        .col-form-label:empty {
            display: none;
        }
    </style>
@endsection
