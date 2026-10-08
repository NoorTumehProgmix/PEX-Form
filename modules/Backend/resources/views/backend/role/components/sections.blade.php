<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <input id="check-all-pages" value="1" type="checkbox" /> {{ trans_cms('cms::app.check_all') }}
        </div>
    </div>
    <div class="col-md-12">

        <div class="card">
            <div class="card-body">
                <input type="hidden" name='current_permissions' value="{{ $cur_pages_permissions }}">

                @foreach ($sections as $section)
                    <h3>
                        {{ $section['title'] }}
                    </h3>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                    <th>{{ trans_cms('cms::app.permissions') }}</th>
                                    <th style="width: 10%">
                                        <input class="check-all-permissions" value="1" type="checkbox" />
                                        {{ trans_cms('cms::app.check_all') }}
                                    </th>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>Access</td>
                                        <td>
                                            <input class="perm-check-item" value="{{ 'view.' . $section['id'] }}"
                                                type="checkbox" name="permissions[]"
                                                @if ($model->hasPermissionTo('view.' . $section['id'])) checked @endif>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Edit Page</td>
                                        <td>
                                            <input class="perm-check-item" value="{{ 'edit.' . $section['id'] }}"
                                                type="checkbox" name="permissions[]"
                                                @if ($model->hasPermissionTo('edit.' . $section['id'])) checked @endif>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Delete Page</td>
                                        <td>
                                            <input class="perm-check-item" value="{{ 'delete.' . $section['id'] }}"
                                                type="checkbox" name="permissions[]"
                                                @if ($model->hasPermissionTo('delete.' . $section['id'])) checked @endif>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Add Posts</td>
                                        <td>
                                            <input class="perm-check-item" value="{{ 'add.' . $section['id'] }}"
                                                type="checkbox" name="permissions[]"
                                                @if ($model->hasPermissionTo('add.' . $section['id'])) checked @endif>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Edit Posts</td>
                                        <td>
                                            <input class="perm-check-item" value="{{ 'edit.posts.' . $section['id'] }}"
                                                type="checkbox" name="permissions[]"
                                                @if ($model->hasPermissionTo('edit.posts.' . $section['id'])) checked @endif>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Delete Posts</td>
                                        <td>
                                            <input class="perm-check-item"
                                                value="{{ 'delete.posts.' . $section['id'] }}" type="checkbox"
                                                name="permissions[]" @if ($model->hasPermissionTo('delete.posts.' . $section['id'])) checked @endif>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{ $sections->links() }}

<script>
    $(document).ready(function() {
        $('#check-all-pages').change(function() {
            if (this.checked) {
                $(this).closest('.tab-pane').find('.check-all-permissions,.perm-check-item').prop(
                    'checked', true);
            } else {
                $(this).closest('.tab-pane').find('.check-all-permissions,.perm-check-item').prop(
                    'checked', false);
            }
        });
    });
</script>
