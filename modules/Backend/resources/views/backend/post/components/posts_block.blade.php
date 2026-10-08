<div class="mb-3">
    @foreach ($templates as $key => $template)
        <a href="{{ route('admin.posts.create', ['type' => 'landing_pages', 'parent' => $model->id, 'ctemplate' => $key]) }}"
            title="{{ $template['label'] }}"
            class="btn btn-light rounded mb-1">{{ __('cms::app.' . $template['label']) }}</a>
    @endforeach
</div>

{{-- {{ $dataTable->render() }} --}}
@if (is_iterable($model->page_components) and count($model->page_components))
    <table class="table jw-table table-bordered table-hover">
        <tbody>
            @foreach ($model->page_components as $component)
                <tr>
                    <td>{{ $component->title }}</td>
                    <td style="text-align: center; width: 10%; ">
                        <a href="{{ route('admin.posts.edit', ['landing_pages', $component->id]) }}"
                            title="Edit {{ $component->title }}" class="btn btn-primary rounded">
                            <i class="fa fa-edit m-0"></i>
                        </a>
                    </td>
                    <td>{{ strstr(__('cms::app.' . $component->getMeta('ctemplate')), " ") }}</td>
                    <td style="text-align: center; width: 10%; ">{{ $component->display_order }}</td>
                    <td style="text-align: center; width: 10%; ">
                        {{ \Carbon\Carbon::parse($component->date)->format('Y-m-d H:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
