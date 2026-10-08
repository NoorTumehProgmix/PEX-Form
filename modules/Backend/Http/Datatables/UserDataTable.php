<?php



namespace Juzaweb\Backend\Http\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Juzaweb\CMS\Abstracts\DataTable;
use Juzaweb\CMS\Models\User;

class UserDataTable extends DataTable
{
    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns(): array
    {
        return [
            'avatar' => [
                'label' => trans_cms('cms::app.avatar'),
                'width' => '5%',
                'formatter' => function ($value, $row, $index) {
                    return '<img src="' . $row->getAvatar('150x150') . '" class="w-100"/>';
                },
            ],
            'name' => [
                'label' => trans_cms('cms::app.name'),
                'formatter' => [$this, 'rowActionsFormatter'],
            ],
            'email' => [
                'label' => trans_cms('cms::app.email'),
                'width' => '15%',
                'align' => 'center',
            ],
            'is_admin' => [
                'label' => trans_cms('cms::app.roles'),
                'formatter' => function ($value, $row, $index) {
                    $user_roles = "";
                    if ($row->is_admin == 1) {
                        $user_roles .= "Admin,";
                    }
                    foreach ($row['roles'] as $role) {
                        $user_roles .= $role->name . ',';
                    }
                    return rtrim($user_roles, ',');
                },
            ],
            'created_at' => [
                'label' => trans_cms('cms::app.created_at'),
                'width' => '15%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return jw_date_format($row->created_at);
                },
            ],
        ];
    }

    public function rowAction($row)
    {
        $data = parent::rowAction($row);

        $data['edit'] = [
            'label' => trans_cms('cms::app.edit'),
            'url' => route('admin.users.edit', [$row->id]),
        ];

        return $data;
    }

    /**
     * Query data datatable
     *
     * @param array $data
     * @return Builder
     */
    public function query($data)
    {
        $query = User::query();

        if ($keyword = Arr::get($data, 'keyword')) {
            $query->where(
                function (Builder $q) use ($keyword) {
                    $q->where('name', JW_SQL_LIKE, '%' . $keyword . '%');
                    $q->orWhere('email', JW_SQL_LIKE, '%' . $keyword . '%');
                }
            );
        }

        if ($status = Arr::get($data, 'status')) {
            $query->where('status', '=', $status);
        }
        $query->with('roles:name');

        return $query;
    }

    public function bulkActions($action, $ids)
    {
        // /* Only update are not master admin  */
        // $ids = User::whereIn('id', $ids)
        //     ->whereIsAdmin(0)
        //     ->pluck('id')
        //     ->toArray();

        // switch ($action) {
        //     case 'delete':
        //         User::destroy($ids);

        //         break;
        // }

        foreach ($ids as $id) {
            DB::beginTransaction();
            try {
                switch ($action) {
                    case 'delete':
                        $model = User::find($id);
                        if ($model && !$model->isAdmin()) {
                            $model->delete();
                            // Log the action
                            $content = [
                                'method' => $action,
                                'table' => $model->getTable(),
                                'id' => $id,
                                'type' => "user",
                                'label' => "deleted a user",
                                'title' => $model->name,
                                'path' => "",
                            ];
                            log_action($content);
                        }
                        break;
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }
}
