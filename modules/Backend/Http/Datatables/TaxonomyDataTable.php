<?php



namespace Juzaweb\Backend\Http\Datatables;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Juzaweb\CMS\Abstracts\DataTable;
use Juzaweb\Backend\Models\Taxonomy;
use Illuminate\Support\Facades\Lang;

class TaxonomyDataTable extends DataTable
{
    protected $taxonomy;

    public function mount($taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        $columns = [
            'name' => [
                'label' => trans_cms('cms::app.name'),
                'formatter' => [$this, 'rowActionsFormatter'],
            ]
        ];

        if (in_array('hierarchical', Arr::get($this->taxonomy, 'supports', []))) {
            $columns['parent'] = [
                'label' => trans_cms('cms::app.parent'),
                'width' => '20%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return $row->parent->name ?? '__';
                }
            ];
        }

        $columns['total_post'] = [
            'label' => trans_cms('cms::app.total_posts'),
            'width' => '15%',
            'align' => 'center',
        ];

        $columns['created_at'] = [
            'label' => trans_cms('cms::app.created_at'),
            'width' => '15%',
            'align' => 'center',
            'formatter' => function ($value, $row, $index) {
                return jw_date_format($row->created_at);
            },
        ];

        return $columns;
    }

    /**
     * Query data datatable
     *
     * @param array $data
     * @return Builder
     */
    public function query($data)
    {
        /**
         * @var Builder $query
         */
        $query = $this->makeModel()->query()->with('parent');
        $data['post_type'] = $this->taxonomy['post_type'];
        $data['taxonomy'] = $this->taxonomy['taxonomy'];
        $query->where('lang', Lang::locale());
        $query->whereFilter($data);
        return $query;
    }

    public function rowAction($row)
    {
        $data = parent::rowAction($row);
        $data['view'] = [
            'label' => trans_cms('cms::app.view'),
            'url' => $row->getLink(),
            'target' => '_blank',
        ];

        return $data;
    }

    public function bulkActions($action, $ids)
    {
        foreach ($ids as $id) {
            DB::beginTransaction();
            try {
                switch ($action) {
                    case 'delete':

                        $model = $this->makeModel()->find($id);
                        if ($model->is_default) {
                            $data = [
                                "status" => false,
                                "message" => trans_cms('cms::app.taxonomy_failed_to_delete_default')
                            ];
                            return $data;
                        }

                        if ($model->total_post == 0) {
                            $model->delete($id);
                        } else {
                            $data = [
                                "status" => false,
                                "message" => "$model->taxonomy has $model->total_post related posts please delete them first."
                            ];
                            return $data;
                        }

                        break;
                }

                DB::commit();

                $content = [
                    'method' => $action,
                    'table' => $model->table,
                    'id' => $id,
                    'type' => $model->taxonomy,
                    'label' => "$action a " . Str::singular($model->taxonomy),
                    'title' => $model->name,
                    'path' => "",
                ];
                log_action($content);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }

    protected function makeModel()
    {
        return app(Taxonomy::class);
    }
}
