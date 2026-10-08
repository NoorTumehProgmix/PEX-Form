<div class="form-group {{ @$type == 'color' ? 'd-flex justify-content-between align-items-center' : '' }}">

    @php
        if (@$type == 'color') {
            $themeColors = get_config('theme_colors');
            if (isset($themeColors[$value])) {
                $color_label = '(#' . (intval($value) + 1) . ')';
                $value = $themeColors[$value];
            }
        }
    @endphp

    <label class="col-form-label" for="{{ $id ?? $name }}">
        {{ $label ?? $name }} {!! isset($color_label) ? "<small>$color_label</small>" : '' !!} @if ($required ?? false)
            <abbr>*</abbr>
        @endif
    </label>

    @if (isset($type) && $type == 'color')
        <datalist id="theme-colors">
            @if (get_config('theme_colors'))
                @foreach (get_config('theme_colors') as $color)
                    <option>{{ $color }}</option>
                @endforeach
            @endif
        </datalist>
    @endif


    @if (isset($prefix) || isset($suffix))
        <div class="input-group mb-2">
            @if (isset($prefix))
                <div class="input-group-prepend">
                    <div class="input-group-text">{{ $prefix }}</div>
                </div>
            @endif

            <input type="{{ $type ?? 'text' }}" name="{{ $name }}" class="form-control {{ $class ?? '' }}"
                id="{{ $id ?? $name }}" value="{{ $value ?? ($default ?? '') }}" autocomplete="off"
                placeholder="{{ $placeholder ?? '' }}" @if ($type == 'color') list="theme-colors" @endif
                @if ($disabled ?? false) disabled @endif @if ($required ?? false) required @endif
                @if ($readonly ?? false) readonly @endif
                @foreach ($data ?? [] as $key => $val)
                {{ 'data-' . $key . '="' . $val . '"' }} @endforeach />

            @if (isset($suffix))
                <div class="input-group-prepend">
                    <div class="input-group-text">{{ $suffix }}</div>
                </div>
            @endif
        </div>
    @else
        <input type="{{ $type ?? 'text' }}" name="{{ $name }}" class="form-control {{ $class ?? '' }}"
            id="{{ $id ?? $name }}" value="{{ $value ?? ($default ?? '') }}" autocomplete="off"
            placeholder="{{ $placeholder ?? '' }}" @if (@$type == 'color') list="theme-colors" @endif
            @if ($disabled ?? false) disabled @endif @if ($required ?? false) required @endif
            @if ($readonly ?? false) readonly @endif
            @foreach ($data ?? [] as $key => $val)
            {{ 'data-' . $key . '="' . $val . '"' }} @endforeach />
    @endif

    @if ($description ?? false)
        <small class="text-muted">{!! $description !!}</small>
    @endif
</div>

<script>
    $(document).on('change', 'input[type=color]', function() {
        var selectedColorName = $(this).val();
        var themeColors = {!! json_encode(get_config('theme_colors')) !!};
        var colorIndex = Object.keys(themeColors).find(key => themeColors[key] === selectedColorName);
        var colorLabel = $(this).siblings(".col-form-label").find("small");
        if (colorIndex !== undefined) {
            colorLabel.text("(#" + (parseInt(colorIndex) + 1) + ")");
        } else {
            colorLabel.text("");
        }
    });
</script>
