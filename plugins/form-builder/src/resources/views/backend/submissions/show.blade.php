<div class="w-100" style="direction: ltr; text-align: start">
    <div class="d-flex">
        <div>{{ trans_cms('cms::app.form') }}</div>:
        {{ $title }}
        <br>
    </div>
    @forelse ($data as $key=>$value)
        <div class="d-flex">
            <div>{{ DBTrans("formBuilder.$key") }}</div>:
            @if (is_array($value))
                {{ json_encode($value) }}
            @else
                {{ $value }}
            @endif
            <br>
        </div>
    @empty
        <div class="text-danger text-center">No data</div>
    @endforelse
</div>
