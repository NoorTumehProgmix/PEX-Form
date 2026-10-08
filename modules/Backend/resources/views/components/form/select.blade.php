@php
    $show_attributes_groups_taxonomy = true;
    if ($taxonomy->get('taxonomy') == 'attributes_groups' && isset($model->json_metas['parent'])) {
        $parent_post = \Juzaweb\Backend\Models\Post::find($model->json_metas['parent']);
        if (isset($parent_post)) {
            $parent_taxonomy = $parent_post->taxonomies
                ->where('taxonomy', '=', 'attributes_groups')
                ->pluck('id')
                ->first();
            $show_attributes_groups_taxonomy = isset($parent_taxonomy) ? false : true;
        }
    }
@endphp

@if ($show_attributes_groups_taxonomy)
    <div class="form-group form-taxonomy">
        <label class="col-form-label w-100">
            {{ $taxonomy->get('label') }}
        </label>

        <select
            class="form-control load-taxonomies {{ isset($taxonomy['multiple']) && $taxonomy['multiple'] ? 'select-tags' : 'select-single-tags' }}"
            data-placeholder="--- {{ $taxonomy->get('label') }} ---" data-post-type="{{ $taxonomy->get('post_type') }}"
            data-type="{{ $taxonomy->get('type') }}" data-taxonomy="{{ $taxonomy->get('taxonomy') }}"
            data-explodes="{{ $taxonomy->get('taxonomy') }}-explode">
        </select>

        <div
            class="{{ isset($taxonomy['multiple']) && $taxonomy['multiple'] ? 'show-tags' : 'show-single-tags' }}  mt-2">
            @php
                $items = $model->taxonomies()->where('taxonomy', '=', $taxonomy->get('taxonomy'))->get();
            @endphp

            @foreach ($items as $item)
                @component('cms::components.tag-item', [
                    'name' => $taxonomy->get('taxonomy'),
                    'item' => $item,
                ])
                @endcomponent
            @endforeach
        </div>

        <div class="form-add mt-2 form-add-taxonomy box-hidden">
            <input type="hidden" class="tag-lang" name="lang"
                value="{{ $model->lang ?? config('app.dashboard_locale') }}" />

            <div class="form-group mb-1">
                <label class="col-form-label">{{ trans_cms('cms::app.name') }}</label>
                <input type="text" class="form-control taxonomy-name" autocomplete="off">
            </div>

            @if (in_array('hierarchical', $taxonomy->get('supports', [])))
                <div class="form-group mb-1">
                    <label class="col-form-label">{{ trans_cms('cms::app.parent') }}</label>
                    <select type="text" class="form-control taxonomy-parent load-taxonomies" autocomplete="off"
                        data-post-type="{{ $taxonomy->get('post_type') }}"
                        data-taxonomy="{{ $taxonomy->get('taxonomy') }}">
                    </select>
                </div>
            @endif

            <button type="button" class="btn btn-primary mt-2" data-type="{{ $taxonomy->get('type') }}"
                data-post_type="{{ $taxonomy->get('post_type') }}" data-taxonomy="{{ $taxonomy->get('taxonomy') }}"><i
                    class="fa fa-plus-circle"></i> {{ trans_cms('cms::app.add') }}</button>
        </div>
    </div>
@endif
