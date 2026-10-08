<?php

namespace Juzaweb\Backend\Http\Controllers\Backend;

use Arr;
use Barryvdh\Reflection\DocBlock\Type\Collection;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Juzaweb\Backend\Http\Datatables\PostTypeDataTable;
use Juzaweb\Backend\Jobs\ProcessImage;
use Juzaweb\Backend\Models\Language;
use Juzaweb\Backend\Models\SettingItem;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Abstracts\DataTable;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Http\Controllers\BackendController;

class SettingItemController extends BackendController
{
    use ResourceController {
        ResourceController::afterSave as traitAfterSave;
        ResourceController::getDataForIndex as DataForIndex;
        ResourceController::getDataForForm as DataForForm;
    }

    protected string $viewPrefix = 'cms::backend.post';


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
        return SettingItem::class;
    }

    function validator(array $attributes, ...$params): \Illuminate\Validation\Validator
    {
        $taxonomies = HookAction::getTaxonomies($this->getPostType());
        $keys = $taxonomies->keys()->toArray();

        $rules = [
            'title' => 'required|string|max:250',
            'subtitle' => 'nullable|max:250',
            'description' => 'nullable|max:250',
            'slug' => 'nullable|max:250',
            'status' => 'required|in:draft,publish,trash,private,preview',
            'thumbnail' => 'nullable|string|max:150',
            'display_order' => 'integer|min:0|max:100',
            'end_date' => 'nullable|date|after:date',
            'meta.location' => 'nullable|string|max:250',
            'meta.address' => 'nullable|string|max:250',
        ];

        foreach ($keys as $key) {
            $rules[$key] = 'nullable|array|max:10';
        }

        return Validator::make($attributes, $rules);
    }

    /**
     * Get data table resource
     *
     * @param mixed ...$params
     * @return PostTypeDataTable|DataTable
     * @throws Exception
     */
    function getDataTable(...$params): PostTypeDataTable | DataTable
    {
        $dataTable = new PostTypeDataTable();
        $dataTable->mountData($this->getSetting()->toArray());
        return $dataTable;
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
        $setting = $this->getSetting();
        $templateData = $this->getTemplateData($model);
        $editor = 'cms::backend.post.components.editor';
        if (Arr::get($templateData, 'blocks', [])) {
            $editor = 'cms::backend.page-block.block';
        }
        $data['editor'] = $editor;

        $repeater = 'cms::backend.post.components.repeater';
        $data['repeater'] = $repeater;

        $postBlocks = 'cms::backend.post.components.posts_block';
        $data['blocks'] = $postBlocks;

        $data['date'] = $model['date'];

        //Get website languages
        $data['langs'] = [];
        if (isset($params[1])) { //$param 1 is the page id, Edit page
            //show all langs to switch between
            $langsArray = Language::orderBy('default', 'desc')->get();

            //Get related pages based on lang,rel_id
            $main_rel_id = is_null($model['rel_id']) ? $model['id'] : $model['rel_id'];
            $related_ids = Post::select('id', 'lang')
                ->where('rel_id', $main_rel_id)
                ->orWhere(function ($query) use ($model, $main_rel_id) {
                    $query->whereNull('rel_id')
                        ->where('id', $main_rel_id);
                })
                ->get();
            if ($related_ids && $related_ids->count() != 0) {
                $data['related_ids'] = $related_ids->pluck('id', 'lang')->toArray();
            }
        } else { //Add new page
            //Only show primary lang
            $langsArray = Language::where('default', 1)->get();
        }

        if ($langsArray && $langsArray->count() != 0) {
            $data['langs'] = $langsArray->pluck('name', 'code')->toArray();
        }

        return apply_filters(
            "post_type.{$this->getPostType()}.getDataForForm",
            array_merge(
                $data,
                [
                    'postType' => $setting->get('key'),
                    'model' => $model,
                    'setting' => $setting,
                    'templateData' => $templateData,
                ]
            )
        );
    }

    /**
     * @param ...$params
     * @return array
     * @throws Exception
     */
    function getDataForIndex(...$params): array
    {
        $data = $this->DataForIndex(...$params);
        $data['setting'] = $this->getSetting();
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
}
