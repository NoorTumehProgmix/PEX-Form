@php
    $show_attributes_taxonomy = true;
    $attr_group_taxonomy = $model->taxonomies->where('taxonomy', '=', 'attributes_groups')->pluck('id')->first();
    if (isset($attr_group_taxonomy)) {
        $show_attributes_taxonomy = false;
    }

    if ($show_attributes_taxonomy && isset($model->json_metas['parent'])) {
        $parent_post = \Juzaweb\Backend\Models\Post::find($model->json_metas['parent']);
        if (isset($parent_post)) {
            $parent_taxonomy = $parent_post->taxonomies
                ->where('taxonomy', '=', 'attributes_groups')
                ->pluck('id')
                ->first();
            $show_attributes_taxonomy = isset($parent_taxonomy) ? true : false;
        }
    }
    if (!isset($model->json_metas['parent']) || $model->json_metas['parent'] == '') {
        $show_attributes_taxonomy = false;
    }
@endphp

@if ($show_attributes_taxonomy)
    <div class="form-group form-taxonomy">
        <label class="col-form-label w-100">
            {{ $taxonomy->get('label') }}
        </label>
        <a href="javascript:void(0)" class="btn btn-success mx-2" data-toggle="modal"
            data-target="#modal-add">{{ trans('cms::app.add_attributes') }}</a>

    </div>
    <div class="modal fade" id="modal-add" role="dialog" aria-labelledby="modal-add-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-add-title">{{ trans('cms::app.add_attributes') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="{{ trans('cms::app.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @php
                        $parent_id = isset($_GET['parent']) ? $_GET['parent'] : @$model->json_metas['parent'];
                        $parent_post = \Juzaweb\Backend\Models\Post::find($parent_id);
                        if (isset($parent_post)) {
                            $parent_taxonomy = $parent_post->taxonomies
                                ->where('taxonomy', '=', 'attributes_groups')
                                ->pluck('id')
                                ->first();

                            $items = \Juzaweb\Backend\Models\Taxonomy::where(
                                'taxonomy',
                                '=',
                                $taxonomy->get('taxonomy'),
                            )
                                ->where('post_type', '=', $taxonomy->get('post_type'))
                                ->where('parent_id', $parent_taxonomy)
                                ->get();
                        }
                    @endphp
                    @if (isset($items))
                        @foreach ($items as $item)
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    {{ $item->name }}
                                </div>
                                <div class="col-md-8">
                                    <input type="hidden" name="{{ $taxonomy->get('taxonomy') . '_ids' }}[]"
                                        class="form-control" value="{{ $item->id }}">
                                    <input type="text" name="{{ $taxonomy->get('taxonomy') . '_values' }}[]"
                                        class="form-control"
                                        value="{{ @collect($model->json_metas[$taxonomy->get('taxonomy')])->where('taxonomy_id', $item->id)->first()['value'] }}">
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"
                        data-dismiss="modal">{{ trans('cms::app.save') }}</button>
                </div>
            </div>
        </div>
    </div>

@endif
