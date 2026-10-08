<?php

namespace Juzaweb\CMS\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Juzaweb\Backend\Events\AfterPostSave;
use Juzaweb\Backend\Http\Datatables\PostTypeDataTable;
use Juzaweb\Backend\Http\Datatables\SettingItemDatatable;
use Juzaweb\Backend\Jobs\ProcessImage;
use Juzaweb\Backend\Models\Language;
use Juzaweb\Backend\Models\Permission;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Role;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Abstracts\DataTable;
use Juzaweb\CMS\Facades\HookAction;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;


trait SettingItemTypeController
{
    use ResourceController {
        ResourceController::afterSave as traitAfterSave;
        ResourceController::getDataForIndex as DataForIndex;
        ResourceController::getDataForForm as DataForForm;
    }

    /**
     * @param Request $request
     * @param ...$params
     * @return JsonResponse
     * @throws Exception
     */
    function datatable(Request $request, ...$params): JsonResponse
    {
        $this->checkPermission(
            'index',
            $this->getModel(...$params),
            ...$params
        );

        $table = $this->getDataTable(...$params);
        $table->setCurrentUrl(action([static::class, 'index'], $params, false));
        $columns = $table->columns();

        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'desc');
        if ($sort == "total_posts") {
            unset($sort);
        }
        $offset = $request->get('offset', 0);
        $limit = (int) $request->get('limit', 20);

        //show datatable content based on current language
        $currentLanguage = Lang::locale();
        $query = $table->query($request->all())
            ->where('lang', $currentLanguage);
        $count = $query->count();
        if (isset($sort)) {
            $query->orderBy($sort, $order);
        }
        if ($params[0] == "posts") {
            $query->offset($offset);
            $query->limit($limit);
        }

        $rows = $query->get();
        $results = [];
        foreach ($rows as $index => $row) {
            $main_rel_id = is_null($row->rel_id) ? $row->id : $row->rel_id;
            if ($row->type == "pages" && !$this->hasPermission('view.' . $main_rel_id)) {
                continue;
            }

            $columns['id'] = $row->id;
            foreach ($columns as $col => $column) {
                if (!empty($column['formatter'])) {
                    $results[$index][$col] = $column['formatter'](
                        $row->{$col} ?? null,
                        $row,
                        $index
                    );
                } else {
                    $results[$index][$col] = $row->{$col};
                }
            }
        }
        $totalRowsAfterFilter = $count;

        if ($params[0] == "pages") {
            $totalRowsAfterFilter = count($results);
            $results = array_slice($results, $offset, $limit);
        }

        return response()->json(
            [
                'total' => $totalRowsAfterFilter,
                'rows' => $results,
            ]
        );
    }

    /**
     * @param ...$params
     * @return string
     */
    function getModel(...$params): string
    {
        return Post::class;
    }

    /**
     * @param mixed ...$params
     * @return string
     * @throws Exception
     */
    function getTitle(...$params): string
    {
        return trans('cms::app.setting');
    }

    function validator(array $attributes, ...$params): \Illuminate\Validation\Validator
    {
        $rules = [
            'name' => 'required|string|max:250',
            'description' => 'nullable|max:250',
            'image' => 'nullable|max:250',
            'status' => 'required|boolean',
            'setting_category_id' => 'exist:setting_categories,id'
        ];

        return Validator::make($attributes, $rules);
    }


    /**
     * Get data table resource
     *
     * @param mixed ...$params
     * @return PostTypeDataTable|DataTable
     * @throws Exception
     */
    function getDataTable(...$params): SettingItemDatatable | DataTable
    {
        return new SettingItemDatatable();
    }


    /**
     * Get data for form
     *
     * @param Model $model
     * @param mixed ...$params
     * @return array
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function getDataForForm($model, ...$params): array
    {
        do_action(Action::BLOCKS_INIT);

        $data = $this->DataForForm($model, ...$params);
        $data['date'] = $model['date'];

        //Get website languages
        $data['langs'] = [];
        if (isset($params[1])) { //$param 1 is the page id, Edit page
            //show all langs to switch between
            $langsArray = Language::orderBy('default', 'desc')->get();
        } else { //Add new page
            //Only show primary lang
            $langsArray = Language::where('code', config('app.dashboard_locale'))->get();
        }

        if ($langsArray && $langsArray->count() != 0) {
            $data['langs'] = $langsArray->pluck('name', 'code')->toArray();
        }

        return   $data;
    }

    /**
     * @param ...$params
     * @return array
     * @throws Exception
     */
    function getDataForIndex(...$params): array
    {
        $data = $this->DataForIndex(...$params);
        return $data;
    }

    function parseDataForSave(array $attributes, ...$params)
    {

        $setting = $this->getSetting();
        $attributes['type'] = $setting->get('key');

        $titles = $attributes['repeater_titles'] ?? [];
        $links = $attributes['repeater_links'] ?? [];
        $images = $attributes['repeater_images'] ?? [];
        $descriptions = $attributes['repeater_descriptions'] ?? [];
        $newTab = $attributes['repeater_new_tabs'] ?? [];
        $buttonLink = $attributes['repeater_button_links'] ?? [];
        $date = $attributes['repeater_date'] ?? [];
        $bg_colors = $attributes['bg_colors'] ?? [];
        $text_colors = $attributes['text_colors'] ?? [];

        $repeater = [];
        foreach ($titles as $key => $title) {
            if (!empty($title)) {

                if (isset($images[$key]) && !empty($images[$key])) {
                    $processImageJob = new ProcessImage($images[$key], "scale", ['1000x600'], null);
                    dispatch($processImageJob->onQueue("sliders"));
                }

                $repeater[] = [
                    'title' => $title,
                    'link' => $links[$key] ?? null,
                    'image' => $images[$key] ?? null,
                    'description' => $descriptions[$key] ?? null,
                    'new_tab' => $newTab[$key] ?? 0,
                    'button_link' => $buttonLink[$key] ?? 0,
                    'date' => $date[$key] ?? null,
                    'bg_color' => $bg_colors[$key] ?? 0,
                    'text_color' => $text_colors[$key] ?? 0,
                ];
            }
        }
        $attributes['meta']['repeater'] = $repeater;

        return apply_filters(
            "post_type.{$this->getPostType()}.parseDataForSave",
            $attributes
        );
    }

    function checkPermission($ability, $arguments = [], ...$params): void
    {
        $this->authorize($ability, $arguments);
    }

    function hasPermission($ability, $arguments = [], ...$params): bool
    {
        $response = Gate::inspect($ability, $arguments);
        return $response->allowed();
    }

    function updateSuccessResponse($model, $request, ...$params): JsonResponse | RedirectResponse
    {
        $message = trans('cms::app.updated_successfully')
            . ' <a href="' . $model->getLink() . '" target="_blank">' . trans('cms::app.view_post') . '</a>';

        return $this->success(
            [
                'message' => $message,
                'path' => $model->getLink(),
                'id' => $model->id,
            ]
        );
    }
}
