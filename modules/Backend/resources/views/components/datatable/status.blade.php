@switch($row->status)
    @case('publish')
    <span class="text-success">{{ trans_cms('cms::app.publish') }}</span>
    @break
    @case('scheduled')
    <span class="text-warning">{{ trans_cms('cms::app.scheduled') }}</span>
    <div><small>{{ $timeElapsed }}</small></div>
    @break
    @case('approved')
    <span class="text-success">{{ trans_cms('cms::app.approved') }}</span>
    @break

    @case('private')
    <span class="text-warning">{{ trans_cms('cms::app.private') }}</span>
    @break

    @case('pending')
    <span class="text-warning">{{ trans_cms('cms::app.pending') }}</span>
    @break

    @case('draft')
    <span class="text-secondary">{{ trans_cms('cms::app.draft') }}</span>
    @break

    @case('trash')
    <span class="text-danger">{{ trans_cms('cms::app.trash') }}</span>
    @break

    @case('deny')
    <span class="text-danger">{{ trans_cms('cms::app.deny') }}</span>
    @break

    @default
    <span class="text-secondary">{{ trans_cms('cms::app.draft') }}</span>
    @break
@endswitch