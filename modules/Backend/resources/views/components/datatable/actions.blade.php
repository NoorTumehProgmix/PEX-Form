<div class="dropdown mb-2 mr-2">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        {{ trans_cms('cms::app.actions') }}
    </button>
    <div class="dropdown-menu" role="menu">
        @if (isset($resources))
            @foreach ($resourses as $key => $resourse)
                <a class="dropdown-item"
                    href="{{ route('admin.post_resource.index', [$key, $row->id]) }}">{{ $resourse->get('label_action') }}</a>
            @endforeach
        @endif

        @if (isset($actions))
            @foreach ($actions as $key => $action)
                @php

                    $hasAction = !empty($action['action']);
                @endphp
                @if (isset($action['label']))
                    <a class="dropdown-item {{ $action['class'] ?? '' }} {{ $hasAction ? 'action-item' : '' }}"
                        data-id="{{ $row->id ?? '' }}"
                        @if ($hasAction) data-action="{{ $action['action'] }}" @endif
                        @if (isset($action['target'])) target="{{ $action['target'] }}" @endif
                        @foreach ($action['data'] ?? [] as $dataKey => $item)
                        data-{{ $dataKey }}="{{ (string) $item }}" @endforeach
                        href="{{ $action['url'] ?? 'javascript:void(0)' }}">{{ $action['label'] }}</a>
                @endif
            @endforeach
        @endif
    </div>
</div>
