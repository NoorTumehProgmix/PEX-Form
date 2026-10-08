<div class="form-group">
    <label class="col-form-label">{{ trans_cms('cms::app.url') }}</label>
    <input type="text" class="form-control menu-data" data-name="link" placeholder="http://" autocomplete="off"
        value="{{ $item->link }}">
</div>

<div class="form-group">
    <label class="col-form-label">{{ trans_cms('cms::app.link_text') }}</label>
    <input type="text" class="form-control change-label menu-data" data-name="label" autocomplete="off" required
        value="{{ $item->label }}">
</div>

<div class="form-group">
    {{ Field::image($item, 'icon', [
        'value' => $item->icon,
        'data' => [
            'name' => 'icon',
        ],
    ]) }}
</div>
