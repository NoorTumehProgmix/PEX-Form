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
    @php
        if (is_string($postType)) {
            $postTypeArr = \Juzaweb\CMS\Facades\HookAction::getPostTypes($postType)->toArray();
        }

        $type = $setting['key'];
        if ($type === 'landing_pages') {
            $isRoot = !isset($model->json_metas['parent']);
            $editMood = !empty($model->id);
            $templates = Juzaweb\CMS\Facades\ThemeLoader::getRegister(jw_current_theme(), 'landing_pages');
            $templateFields = [];
            if ($model->getMeta('ctemplate')) {
                $templateFields = $templates[$model->getMeta('ctemplate')]['fields'];
            }
        } else {
            $isRoot = false;
            $templateFields = [];
        }
    @endphp
    <div class="row">
        <div class="col-md-9">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab1">General</a>
                </li>

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('editor', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']) or $isRoot)
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab"
                            href="#editor-tab">{{ trans_cms('cms::app.main_content') }}</a>
                    </li>
                @endif

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('repeater', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']) or $isRoot)
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab"
                            href="#repeater-tab">{{ trans_cms('cms::app.items_repeater') }}</a>
                    </li>
                @endif

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('images', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']) ||
                        in_array('images', $templateFields))
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#images-tab">{{ trans_cms('cms::app.images') }}</a>
                    </li>
                @endif

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('inner_page_design', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']) ||
                        in_array('inner_page_design', $templateFields))
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab"
                            href="#inner-page-design-tab">{{ trans_cms('cms::app.inner_page_design') }}</a>
                    </li>
                @endif

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('seo', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']) or $isRoot)
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab2">SEO</a>
                    </li>
                @endif
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div id="tab1" class="tab-pane active">
                    @component('cms::components.card', [
                        'label' => trans_cms('cms::app.information'),
                    ])
                        <input type="hidden" class="post_lang" value="{{ $model->lang }}">

                        <div class="row mb-2">
                            <div class="col-md-12">
                                {{ Field::text($model, 'title', [
                                    'required' => true,
                                    'class' => empty($model->slug) ? 'generate-slug' : '',
                                ]) }}
                            </div>
                        </div>

                        @if (
                            (isset($postTypeArr['custom_fields']) && in_array('subtitle', $postTypeArr['custom_fields'])) ||
                                !isset($postTypeArr['custom_fields']) ||
                                ($type == 'landing_pages' && !$isRoot))
                            @php
                                $subtitleLabel = trans_cms('cms::app.subtitle');
                                if ($type === 'landing_pages') {
                                    $subtitleLabel = trans_cms('cms::app.menu_title');
                                }
                            @endphp
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    {{ Field::text($model, 'subtitle', ['label' => $subtitleLabel]) }}
                                </div>
                            </div>
                        @endif
                    @endcomponent

                    @if ($type === 'landing_pages' and $isRoot and $editMood)
                        @component('cms::components.card', [
                            'label' => trans_cms('cms::app.blocks'),
                        ])
                            @include($blocks)
                        @endcomponent
                    @endif

                    @php
                        $metas = collect_metas($setting->get('metas'))
                            ->where('sidebar', false)
                            ->where('visible', true)
                            ->toArray();
                    @endphp
                    @if (!empty($metas))
                        @component('cms::components.card')
                            @foreach ($metas as $name => $meta)
                                @if (
                                    $type !== 'landing_pages' or
                                        isset($meta['show_in_root']) and $meta['show_in_root'] and $isRoot or
                                        isset($meta['show_in_child']) and $meta['show_in_child'] and !$isRoot)
                                    @php
                                        if ($name == 'background_color') {
                                            $meta['data']['default'] = '#ffffff';
                                        } elseif ($name == 'text_color') {
                                            $meta['data']['default'] = '#000000';
                                        }
                                        $meta['name'] = "meta[{$name}]";
                                        if (isset($data['defaults']) && $name == 'pages') {
                                            $meta['data']['value'] = $data['defaults'];
                                        } else {
                                            $meta['data']['value'] = $model->getMeta($name);
                                        }
                                        if ($meta['type'] === 'checkbox') {
                                            $meta['data']['checked'] = $model->getMeta($name) ? 1 : 0;
                                        }
                                    @endphp
                                    @if (
                                        $type !== 'landing_pages' or
                                            !isset($meta['show_if_visible']) or
                                            $meta['show_if_visible'] and in_array($name, $templateFields))
                                        {{ Field::fieldByType($meta) }}
                                    @endif
                                @endif
                            @endforeach

                            {{ Field::render($setting->get('fields', []), $model) }}
                        @endcomponent
                    @endif

                    @do_action('post_type.' . $postType . '.form.left', $model)
                </div>

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('seo', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']) ||
                        $isRoot)
                    <div id="tab2" class="tab-pane fade">
                        @if (!isset($seo_meta))
                            @do_action('post_types.form.left', $model)
                        @else
                            @php
                                $seo_meta->model = $model;
                            @endphp
                            @do_action('post_types.form.seo_form_create', $seo_meta)
                        @endif
                    </div>
                @endif

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('editor', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']) ||
                        $isRoot)
                    <div id="editor-tab" class="tab-pane fade">
                        <div class="p-3">
                            @if (
                                (isset($postTypeArr['custom_fields']) && in_array('editor', $postTypeArr['custom_fields'])) ||
                                    !isset($postTypeArr['custom_fields']) ||
                                    in_array('editor', $templateFields))
                                @if (
                                    (isset($postTypeArr['custom_fields']) && in_array('latlng', $postTypeArr['custom_fields'])) ||
                                        !isset($postTypeArr['custom_fields']) ||
                                        in_array('latlng', $templateFields))
                                    <div class="row mb-2">
                                        <div class="col-md-12">
                                            <div class="custom-tooltip">
                                                {{ Field::text($model, 'latlng') }}
                                                <span class="custom-tooltip-text">يظهر على الزر في الصفحة (مثال: اقرأ
                                                    المزيد)</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (
                                    (isset($postTypeArr['custom_fields']) && in_array('external_link', $postTypeArr['custom_fields'])) ||
                                        !isset($postTypeArr['custom_fields']) ||
                                        in_array('external_link', $templateFields))
                                    <div class="row mb-2">
                                        <div class="col-md-12">
                                            {{ Field::text($model, 'external_link') }}
                                        </div>
                                    </div>
                                @endif

                                @include($editor)

                                @if (
                                    (isset($postTypeArr['custom_fields']) && in_array('text', $postTypeArr['custom_fields'])) ||
                                        !isset($postTypeArr['custom_fields']) ||
                                        in_array('text', $templateFields))
                                    {{ Field::editor($model, 'text') }}
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('repeater', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']) ||
                        in_array('items_repeater', $templateFields))
                    <div id="repeater-tab" class="tab-pane fade">
                        <div class="p-3">
                            @component('cms::components.card', [
                                'label' => trans_cms('cms::app.items_repeater'),
                            ])
                                @include($repeater)
                            @endcomponent
                        </div>
                    </div>
                @endif

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('inner_page_design', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']) ||
                        in_array('inner_page_design', $templateFields))
                    <div id="inner-page-design-tab" class="tab-pane fade">
                        <div class="p-3">
                            @component('cms::components.card', ['label' => trans_cms('cms::app.inner_page_design')])
                                @php
                                    $designMetas = [
                                        'background_image' => [
                                            'type' => 'image',
                                            'label' => trans_cms('cms::app.background_image'),
                                            'sidebar' => true,
                                        ],
                                        'side_design' => [
                                            'type' => 'checkbox',
                                            'label' => trans_cms('cms::app.side_design'),
                                            'sidebar' => true,
                                        ],
                                        'color' => [
                                            'type' => 'text',
                                            'label' => trans_cms('cms::app.color'),
                                            'sidebar' => true,
                                            'data' => [
                                                'type' => 'color',
                                            ],
                                        ],
                                    ];
                                @endphp
                                @foreach ($designMetas as $name => $meta)
                                    @php
                                        $meta['name'] = "meta[{$name}]";
                                        $meta['data']['value'] = $model->getMeta($name);
                                        if ($name == 'background_color') {
                                            $meta['data']['default'] = '#ffffff';
                                        } elseif ($name == 'text_color') {
                                            $meta['data']['default'] = '#000000';
                                        }

                                        if ($meta['type'] === 'checkbox') {
                                            $meta['data']['checked'] = $model->getMeta($name) ? 1 : 0;
                                        }
                                    @endphp
                                    {{ Field::fieldByType($meta) }}
                                @endforeach
                            @endcomponent
                        </div>
                    </div>
                @endif

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('images', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']) ||
                        in_array('images', $templateFields))
                    <div id="images-tab" class="tab-pane fade">
                        <div class="p-3">
                            {{ Field::images($model, 'images', ['label' => trans_cms('cms::app.images_list')]) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-3">
            @if (isset($model->sub_pages_count))
                <div class="row">
                    <div class="col-md-6">
                        @component('cms::components.card')
                            <a href="{{ route('admin.posts.index', ['pages', 'parent' => $model->id]) }}"
                                class="font-size-18 font-weight-bold text-center text-primary">
                                <div>{{ trans_cms('cms::app.sub_pages') }}</div>
                                <div>{{ $model->sub_pages_count }}</div>
                            </a>
                        @endcomponent
                    </div>
                    <div class="col-md-6">
                        @component('cms::components.card')
                            <a href="{{ route('admin.posts.index', ['posts', 'pages' => $model->id]) }}"
                                class="font-size-18 font-weight-bold text-center text-primary">
                                <div>{{ trans_cms('cms::app.posts') }}</div>
                                <div>{{ $model->posts_count }}</div>
                            </a>
                        @endcomponent
                    </div>
                </div>
            @endif

            <div class="card">
                <a href="" class="card-header navbar-toggler collapsed" data-toggle="collapse"
                    data-target="#advanced-options">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ trans_cms('cms::app.advanced_options') }}</h5>
                        <i class="fa fa-sort-down"></i>
                    </div>
                </a>
                <div class="card-body collapse navbar-collapse" id="advanced-options">
                    {{ Field::slug($model, 'slug') }}
                    @if ($model->oldslug != null && $model->oldslug != $model->slug)
                        <span>{{ trans_cms('cms::app.oldslug') }} {{ $model->oldslug }}</span>
                    @endif
                </div>
            </div>

            @component('cms::components.card', [
                'label' => trans_cms('cms::app.side_bar'),
            ])
                {{ Field::select($model, 'lang', [
                    'options' => $langs,
                    'class' => 'lang-switch',
                ]) }}

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('show_sitemap', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']))
                    <input type="hidden" name="show_sitemap" value="0">
                    {{ Field::checkbox(trans_cms('cms::app.show_sitemap'), 'show_sitemap', ['value' => 1, 'checked' => (isset($model['show_sitemap']) && $model['show_sitemap'] == 1) || !isset($model['show_sitemap']) ? true : false]) }}
                @else
                    <input type="hidden" name="show_sitemap" value="0">
                @endif

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('hide_section', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']))
                    <input type="hidden" name="meta[hide_section]" value="0">

                    {{ Field::checkbox(trans_cms('cms::app.hide_section'), 'meta[hide_section]', [
                        'value' => '1',
                        'checked' => $model->getMeta('hide_section') ? 1 : 0,
                    ]) }}
                @elseif($type === 'posts')
                    <input type="hidden" name="meta[hide_section]" value="0">
                @else
                    <input type="hidden" name="meta[hide_section]" value="1">
                @endif

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('pin', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']))
                    @php
                        $faqLabel = trans_cms('cms::app.pin');
                        if (in_array($type, ['faqs', 'posts'])) {
                            $faqLabel = trans_cms('cms::app.show_homepage');
                        }
                    @endphp
                    <input type="hidden" name="pin" value="0">

                    {{ Field::checkbox($faqLabel, 'pin', [
                        'value' => 1,
                        'checked' => isset($model['pin']) && $model['pin'] == 1 ? true : false,
                    ]) }}
                @endif

                @if (isset($related_ids))
                    @foreach ($related_ids as $lang => $id)
                        <input type="hidden" class="related_ids" value="{{ $id }}" id="{{ $lang }}">
                    @endforeach
                @endif

                {{ Field::select($model, 'status', ['options' => $model->getStatuses()]) }}

                <div class="form-group" @if ($type === 'landing_pages' and $isRoot) style="display: none" @endif>
                    <label class="col-form-label">{{ trans_cms('cms::app.display_order') }}</label>
                    <input type="number" name="display_order" class="form-control"
                        value="{{ $model->display_order ?? 100 }}" />
                </div>
                <div class="form-group">
                    <label class="col-form-label">{{ trans_cms('cms::app.start_date') }}</label>
                    <input type="datetime-local" name="date" class="form-control"
                        value="{{ $date ? $date : now()->format('Y-m-d\TH:i') }}">
                </div>
                <div class="form-group">
                    <label class="col-form-label">{{ trans_cms('cms::app.end_date') }}</label>
                    <input type="datetime-local" name="end_date" class="form-control"
                        value="{{ $model->end_date ? $model->end_date : '' }}">
                </div>

                @if (
                    (isset($postTypeArr['custom_fields']) && in_array('thumbnail', $postTypeArr['custom_fields'])) ||
                        !isset($postTypeArr['custom_fields']) ||
                        in_array('thumbnail', $templateFields))
                    {{ Field::image($model, 'thumbnail') }}
                @endif

                @php
                    $sideMetas = collect_metas($setting->get('metas'))
                        ->where('sidebar', true)
                        ->where('visible', true)
                        ->toArray();
                @endphp

                @if (!empty($sideMetas))
                    @foreach ($sideMetas as $name => $meta)
                        @if (
                            $type !== 'landing_pages' or
                                isset($meta['show_in_root']) and $meta['show_in_root'] and $isRoot or
                                isset($meta['show_in_child']) and $meta['show_in_child'] and !$isRoot)
                            @php
                                $meta['name'] = "meta[{$name}]";
                                $meta['data']['value'] = $model->getMeta($name);
                                if ($name == 'background_color') {
                                    $meta['data']['default'] = '#ffffff';
                                } elseif ($name == 'text_color') {
                                    $meta['data']['default'] = '#000000';
                                }

                                if ($meta['type'] === 'checkbox') {
                                    $meta['data']['checked'] = $model->getMeta($name) ? 1 : 0;
                                }
                            @endphp
                            @if (isset($meta['show_in_child']) and $meta['show_in_child'] and !$isRoot)
                                <div style="display: none">
                            @endif
                            {{ Field::fieldByType($meta) }}
                            @if (isset($meta['show_in_child']) and $meta['show_in_child'] and !$isRoot)
            </div>
            @endif
            @endif
            @endforeach
            @endif

            @if (
                (isset($postTypeArr['custom_fields']) && in_array('form', $postTypeArr['custom_fields'])) ||
                    !isset($postTypeArr['custom_fields']) ||
                    in_array('form_block', $templateFields))
                @if (isset($data['forms']))
                    {{ Field::select($model, 'meta[form]', [
                        'label' => trans_cms('cms::app.insert_form'),
                        'options' => $data['forms'],
                        'value' => $model->getMeta('form'),
                    ]) }}
                @endif
            @endif

            @if (
                (isset($postTypeArr['custom_fields']) && in_array('popup_messages', $postTypeArr['custom_fields'])) ||
                    !isset($postTypeArr['custom_fields']) ||
                    in_array('form_block', $templateFields))
                @if (isset($data['popup_messages']))
                    {{ Field::select($model, 'meta[popup_messages]', [
                        'label' => trans_cms('cms::app.insert_popup_messages'),
                        'options' => $data['popup_messages'],
                        'value' => $model->getMeta('popup_messages'),
                    ]) }}
                @endif
            @endif
        @endcomponent

        @do_action('post_types.form.right', $model)

        @do_action('post_type.' . $postType . '.form.right', $model)
    </div>
    </div>
@endsection
