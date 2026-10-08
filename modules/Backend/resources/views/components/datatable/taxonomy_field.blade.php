<div class="form-group mb-2 mr-1">
    <select name="{{ $name }}" id="search-{{ $name }}" class="form-control load-taxonomies"
        data-post-type="{{ $field['taxonomy']->get('post_type') }}"
        data-taxonomy="{{ $field['taxonomy']->get('taxonomy') }}"
        @if ($field['taxonomy']->has('supports') && isset($field['taxonomy']->get('supports')['parents']['name'])) data-parent="{{ $field['taxonomy']->get('supports')['parents']['name'] }}" @endif
        @if ($child) data-taxonomy-child='{{ $child }}' @endif
        data-placeholder="{{ trans_cms('cms::app.all') }} {{ $field['label'] }}"></select>
</div>
