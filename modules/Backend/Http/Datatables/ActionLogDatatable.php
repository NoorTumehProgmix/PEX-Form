<?php



namespace Juzaweb\Backend\Http\Datatables;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Juzaweb\Backend\Models\ActionLog;
use Juzaweb\CMS\Abstracts\DataTable;
use Juzaweb\CMS\Models\User;
use Juzaweb\Backend\Models\Post;


class ActionLogDatatable extends DataTable
{
    protected string $sortName = 'created_at';

    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        return [
            'created_at' => [
                'label' => trans_cms('cms::app.date'),
                'width' => '15%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return jw_date_format($row->created_at);
                },
            ],
            'description' => [
                'label' => trans_cms('cms::app.description'),
                'formatter' => function ($value, $row, $index) {
                    $data = json_decode($row->description, true);
                    $user = User::find($row->user_id);
                    return "$user->name " . $data['label'];
                },
            ],
            'method' => [
                'label' => trans_cms('cms::app.method'),
                'formatter' => function ($value, $row, $index) {
                    $data = json_decode($row->description, true);
                    return $data['method'];
                },
            ],
            'url' => [
                'label' => trans_cms('cms::app.title'),
                'formatter' => function ($value, $row, $index) {
                    $data = json_decode($row->description, true);

                    if ($data['path'] != "") {
                        $title = "<a href='" . $data['path'] . "' target='_blank'>" . $data['title'] . "</a>";
                    } else {
                        $title = $data['title'];
                    }
                    return $title;
                },
            ],
            'user_id' => [
                'label' => trans_cms('cms::app.user'),
                'formatter' => function ($value, $row, $index) {
                    $user = User::find($row->user_id);
                    return $user->name;
                },
            ],
            'ip_address' => [
                'label' => trans_cms('cms::app.ip_address'),
                'formatter' => function ($value, $row, $index) {
                    $method = @json_decode($row->description, true)['ip_address'];
                    return $method;
                },
            ],
            'user_agent' => [
                'label' => trans_cms('cms::app.user_agent'),
                'formatter' => function ($value, $row, $index) {
                    return @json_decode($row->description, true)['user_agent'];
                },
            ],
            'form_data' => [
                'label' => trans_cms('cms::app.form_data'),
                'formatter' => function ($value, $row, $index) {
                    $formData = @json_decode($row->description, true)['form_data'];
                    $more = "";
                    if (strlen(json_encode($formData, JSON_PRETTY_PRINT)) > 150) {
                        $more = "<a href='#' class='expand-more'>more</a>";
                    }
                    return "<div class='short-text'>" . json_encode($formData, JSON_PRETTY_PRINT) . "</div>" . $more;
                },
            ],

        ];
    }

    /**
     * Query data datatable
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder|Builder
     */
    public function query($data)
    {
        $query = ActionLog::query();

        if ($search = Arr::get($data, 'keyword')) {
            $query->where(
                function (Builder $q) use ($search) {
                    $q->where('description', JW_SQL_LIKE, '%' . $search . '%');
                }
            );
        }
        return $query;
    }
    public function actions(): array
    {
        $actions = [];
        return $actions;
    }
}
