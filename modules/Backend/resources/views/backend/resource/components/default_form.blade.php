<div class="row">
    <div class="col-md-8">
        {{ Field::text($model, 'name', [
            'required' => true,
        ]) }}

        @if ($setting->get('has_description', true))
            {{ Field::textarea($model, 'description') }}
        @endif

        @php
            $metas = collect_metas($setting->get('metas'))->where('sidebar', false)->toArray();
        @endphp

        @foreach ($metas as $name => $meta)
            @php
                $meta['name'] = "meta[{$name}]";
                $meta['data']['value'] = $model->getMeta($name);
                if ($meta['type'] === 'checkbox') {
                    $meta['data']['checked'] = $model->getMeta($name) ? 1 : 0;
                }
            @endphp

            {{ Field::fieldByType($meta) }}
        @endforeach

    </div>

    <div class="col-md-4">
        @if (method_exists($model, 'getStatuses'))
            {{ Field::select($model, 'status', [
                'options' => $model->getStatuses(),
            ]) }}
        @endif

        @if ($setting->get('has_display_order', true))
            {{ Field::text($model, 'display_order', [
                'required' => true,
                'default' => 100,
            ]) }}
        @endif
        <div class="row">
            <div class="form-group col-md-6">
                <label>{{ trans_cms('cms::app.start_date') }}</label>
                <input type="datetime-local" name="date" class="form-control"
                    value="{{ $model->date ? $model->date : now()->format('Y-m-d\TH:i') }}">
            </div>
            <div class="form-group col-md-6">
                <label>{{ trans_cms('cms::app.end_date') }}</label>
                <input type="datetime-local" name="end_date" class="form-control"
                    value="{{ $model->end_date ? $model->end_date : '' }}">
            </div>
        </div>


        @php
            $resource_lang = $model->lang ? $model->lang : Lang::Locale();
            $metas = collect_metas($setting->get('metas'))->where('sidebar', true)->toArray();
        @endphp

        <input type="hidden" name="lang" value="{{ $resource_lang }}" />


        @foreach ($metas as $name => $meta)
            @php
                $meta['name'] = "meta[{$name}]";
                $meta['data']['value'] = $model->getMeta($name);
            @endphp

            {{ Field::fieldByType($meta) }}
        @endforeach

        @do_action("resource.{$setting->get('key')}.form_right", $model)
    </div>
</div>
<div class="row">
    <div class="col-md-12"> @do_action("resource.{$setting->get('key')}.form_left", $model)
    </div>
</div>
