<?php

/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/juzacms
 * @author     The Anh Dang
 * @link       https://github.com/juzaweb/juzacms
 * @license    GNU V2
 */

namespace Juzaweb\CMS\Traits;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Juzaweb\Backend\Models\Config;
use Juzaweb\Backend\Models\Language;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Resource;
use Juzaweb\Backend\Models\Taxonomy;
use Juzaweb\CMS\Abstracts\DataTable;
use Juzaweb\CMS\Facades\Plugin;
use Progmix\Forms\Models\Form;
use stdClass;

/**
 * @method void getBreadcrumbPrefix(...$params)
 */
trait ResourceController
{
    public function index(...$params): View
    {
        //Get primary language
        $langsArray = Language::where('code', config('app.dashboard_locale'))->get();
        $langs      = $langsArray->pluck('name', 'code')->toArray();

        $this->checkPermission(
            'index',
            $this->getModel(...$params),
            ...$params
        );

        if (method_exists($this, 'getBreadcrumbPrefix')) {
            $this->getBreadcrumbPrefix(...$params);
        }

        return view(
            "{$this->viewPrefix}.index",
            array_merge(
                [
                    'langs' => $langs,
                ],
                $this->getDataForIndex(...$params)
            )
        );
    }

    public function create(...$params): View
    {

        $this->checkPermission('create', $this->getModel(...$params), ...$params);

        $indexRoute = str_replace(
            '.create',
            '.index',
            Route::currentRouteName()
        );

        if (method_exists($this, 'getBreadcrumbPrefix')) {
            $this->getBreadcrumbPrefix(...$params);
        }

        $this->addBreadcrumb(
            [
                'title' => $this->getTitle(...$params),
                'url'   => route($indexRoute, $params),
            ]
        );

        //back link parameters
        $backLinkParams = "";
        $backLinkPath   = action([static::class, 'index'], $params);

        $model = $this->makeModel(...$params);
        $data  = [];

        //Set page to be selected if sent in the URL
        if (isset($_GET['page'])) {
            $jsonMetas          = $model->json_metas;
            $jsonMetas['pages'] = $_GET['page'];
            $model->json_metas  = $jsonMetas;

            $get_page_defaults = Post::PublishedOrDraft()
                ->where("id", $_GET['page'])
                ->first();
            if (isset($get_page_defaults->json_metas['defaults'])) {
                $data['defaults']   = $get_page_defaults->json_metas['defaults'];
                $data['defaults'][] = $_GET['page'];
            }
            $backLinkParams = "?page=" . $_GET['page'];
        }
        if (isset($_GET['pages'])) {
            $jsonMetas                 = $model->json_metas;
            $jsonMetas['primary_page'] = $_GET['pages'];
            $model->json_metas         = $jsonMetas;
            $get_page_defaults         = Post::PublishedOrDraft()
                ->where("id", $_GET['pages'])
                ->first();
            if (isset($get_page_defaults->json_metas['defaults'])) {
                $data['defaults'] = $get_page_defaults->json_metas['defaults'];
            }
            $backLinkParams = "?pages=" . $_GET['pages'];
        }
        //Set page to be selected if sent in the URL
        if (isset($_GET['parent'])) {
            $jsonMetas           = $model->json_metas;
            $jsonMetas['parent'] = $_GET['parent'];
            $model->json_metas   = $jsonMetas;
            $backLinkParams      = "?parent=" . $_GET['parent'];
        }

        //Set ctemplate to be selected if sent in the URL
        if (isset($_GET['ctemplate'])) {
            $jsonMetas              = $model->json_metas;
            $jsonMetas['ctemplate'] = $_GET['ctemplate'];
            $model->json_metas      = $jsonMetas;
        }

        $seo_meta                      = new stdClass();
        $seo_meta->json_metas['metas'] = $this->setMetas();

        $plugins = Plugin::all();
        if (isset($plugins['progmix/forms']) and $plugins['progmix/forms']->isEnabled()) {
            $forms         = Form::orderByDesc('id')->select('name->' . Lang::locale() . ' as label', 'id')->pluck('label', 'id')->toArray();
            $data['forms'] = ['' => trans_cms('cms::app.select_form')] + $forms;
        }

        $plugins = Plugin::all();
        if (isset($plugins['progmix/popup-messages']) and $plugins['progmix/popup-messages']->isEnabled()) {
            $forms                  = Resource::orderByDesc('id')->where('type', 'popups-messages')->pluck('name', 'id')->toArray();
            $data['popup_messages'] = ['' => trans_cms('cms::app.select_popup_messages')] + $forms;
        }

        if (method_exists($model, 'getMeta')) {
            $data['repeaters'] = $model->getMeta('repeater');
            $data['files'] = $model->getMeta('files');
        }
        if (@$params[0] == "landing_pages" && isset($_GET['parent'])) {
            $indexParams       = $params;
            $indexParams       = collect($indexParams)->values()->toArray();
            $indexParams['id'] = $_GET['parent'];
            $backLinkPath      = action([static::class, 'edit'], $indexParams);
        }

        return view(
            "{$this->viewPrefix}.form",
            array_merge(
                [
                    'title'     => trans_cms('cms::app.add_new'),
                    'linkIndex' => $backLinkPath . $backLinkParams,
                    'seo_meta'  => $seo_meta,
                    'data'      => $data,
                ],
                $this->getDataForForm(
                    $model,
                    ...$params
                )
            )
        );
    }

    public function edit(...$params): View
    {
        //Get all languages
        $langsArray = Language::orderBy('default', 'desc')->get();

        $data          = [];
        $data['langs'] = $langsArray->pluck('name', 'code')->toArray();

        $plugins = Plugin::all();
        if (isset($plugins['progmix/forms']) and $plugins['progmix/forms']->isEnabled()) {
            $forms         = Form::orderByDesc('id')->select('name->' . Lang::locale() . ' as label', 'id')->pluck('label', 'id')->toArray();

            $data['forms'] = ['' => trans_cms('cms::app.select_form')] + $forms;
        }

        $plugins = Plugin::all();
        if (isset($plugins['progmix/popup-messages']) and $plugins['progmix/popup-messages']->isEnabled()) {
            $forms                  = Resource::orderByDesc('id')->where('type', 'popups-messages')->pluck('name', 'id')->toArray();
            $data['popup_messages'] = ['' => trans_cms('cms::app.select_popup_messages')] + $forms;
        }

        $indexRoute = str_replace(
            '.edit',
            '.index',
            Route::currentRouteName()
        );

        $indexParams = $params;
        unset($indexParams[$this->getPathIdIndex($indexParams)]);
        $indexParams = collect($indexParams)->values()->toArray();

        //back link parameters
        $backLinkParams = "";
        $backLinkPath   = action([static::class, 'index'], $indexParams);

        if (method_exists($this, 'getBreadcrumbPrefix')) {
            $this->getBreadcrumbPrefix(...$params);
        }

        $model = $this->getDetailModel($this->makeModel(...$indexParams), ...$params);

        $this->addBreadcrumb(
            [
                'title' => $this->getTitle(...$params),
                'url'   => route($indexRoute, $indexParams),
            ]
        );

        if (isset($model['json_metas']['parent']) && $model['json_metas']['parent'] != '') {
            $parent = Post::find($model['json_metas']['parent']);
            $this->addBreadcrumb(
                [
                    'title' => $parent->{$parent->getFieldName()},
                    'url'   => route($indexRoute, $indexParams) . '?parent=' . $model['json_metas']['parent'],
                ]
            );
        }


        //get sub pages,posts count
        if (isset($model['json_metas']['parent'])) {
            $pageID                   = $model['id'];
            $model['sub_pages_count'] = Post::PublishedOrDraft()
                ->where(function ($query) use ($pageID) {
                    $query->orWhereJsonContains('json_metas->parent', "$pageID")
                        ->orWhereJsonContains('json_metas->parent', intval($pageID))
                        ->orWhereJsonContains('json_metas->defaults', intval($pageID))
                        ->orWhereJsonContains('json_metas->defaults', "$pageID");
                })
                ->where('lang', Lang::locale())
                ->where('type', $model->type)->count();
            $model['posts_count'] = Post::PublishedOrDraft()
                ->where(function ($query) use ($pageID) {
                    $query->orWhereJsonContains('json_metas->parent', "$pageID")
                        ->orWhereJsonContains('json_metas->parent', intval($pageID))
                        ->orWhereJsonContains('json_metas->defaults', intval($pageID))
                        ->orWhereJsonContains('json_metas->defaults', "$pageID");
                })
                ->where('lang', Lang::locale())
                ->where('type', 'posts')->count();

            $primary_page = @$model['json_metas']['parent'];
            if (isset($primary_page)) {
                $backLinkParams = "?parent=$primary_page";
            }
        } //get landing pages sub pages
        else if ($model->type === "landing_pages") {
            $pageID                   = $model['id'];
            $model['page_components'] = Post::PublishedOrDraft()->where('json_metas->parent', $pageID)
                ->where('type', 'landing_pages')
                ->orderBy('display_order', 'asc')
                ->orderBy('date', 'desc')
                ->orderBy('title', 'asc')
                ->get();

            $primary_page = @$model['json_metas']['parent'];
            if (isset($primary_page)) {
                $indexParams['id'] = $primary_page;
                $backLinkPath      = action([static::class, 'edit'], $indexParams);
            }
        } else if ($model->type == "posts") {
            $primary_page = @get_primary_page($model['json_metas'])['id'];
            if (isset($primary_page)) {
                $backLinkParams = "?pages=$primary_page";
            }
        }

        //Get related taxonomies based on lang,rel_id
        $main_rel_id = is_null($model['rel_id']) ? $model['id'] : $model['rel_id'];

        $this->checkPermission('edit', $model, ...$params);
        if ($model->type == "pages") {
            $this->checkPermission("edit.$main_rel_id", $model, ...$params);
        } else if ($model->type == "posts") {
            if (! $this->hasPermissionMultiPages("edit.posts", $model->json_metas['pages'], $model, ...$params)) {
                abort(403);
            }
        }

        $related_ids = Taxonomy::select('id', 'lang')
            ->where('rel_id', $main_rel_id)
            ->orWhere(function ($query) use ($model, $main_rel_id) {
                $query->whereNull('rel_id')
                    ->where('id', $main_rel_id);
            })
            ->get();
        if ($related_ids && $related_ids->count() != 0) {
            $data['related_ids'] = $related_ids->pluck('id', 'lang')->toArray();
        }

        if (! ($model instanceof Taxonomy) and $model->json_metas !== null) {
            $data['repeaters'] = $model->getMeta('repeater');
            $data['files'] = $model->getMeta('files');
        }
        //Content Colors
        $theme_colors = get_config('theme_colors');
        if (isset($theme_colors)) {
            foreach ($theme_colors as $key => $color) {
                if (isset($model['content'])) {
                    $model['content'] = str_replace('color: ' . $key, 'color: ' . $color, $model['content']);
                    $model['content'] = str_replace('background-color: ' . $key, 'background-color: ' . $color, $model['content']);
                    $model['content'] = processEditorContent($model['content']);
                }
                if (isset($model['text'])) {
                    $model['text'] = str_replace('color: ' . $key, 'color: ' . $color, $model['text']);
                    $model['text'] = str_replace('background-color: ' . $key, 'background-color: ' . $color, $model['text']);
                    $model['text'] = processEditorContent($model['text']);
                }
            }
            if ($model instanceof Post) {
                $repeater_items = $model?->getMeta('repeater');
            }

            if (isset($repeater_items)) {
                foreach ($repeater_items as &$item) {
                    $item = (object) $item;
                    if (isset($item->description)) {
                        foreach ($theme_colors as $key => $color) {
                            $item->description = str_replace('color: ' . $key, 'color: ' . $color, $item->description);
                            $item->description = str_replace('background-color: ' . $key, 'background-color: ' . $color, $item->description);
                        }
                    }
                }
                $data['repeaters'] = $repeater_items;
            }
        }

        return view(
            $this->viewPrefix . '.form',
            array_merge(
                [
                    'title'     => $model->{$model->getFieldName()},
                    'linkIndex' => $backLinkPath . $backLinkParams,
                    'data'      => $data,
                    //                    'dataTable' => $dataTable,
                ],
                $this->getDataForForm(
                    $model,
                    ...$params
                )
            )
        );
    }

    public function store(Request $request, ...$params): JsonResponse | RedirectResponse
    {
        $this->checkPermission('create', $this->getModel(...$params), ...$params);

        $validator = $this->validator($request->all(), ...$params);
        if (is_array($validator)) {
            $validator = Validator::make($request->all(), $validator);
        }

        $validator->validate();

        $data = $this->parseDataForSave($request->all(), ...$params);

        $data['json_metas']['metas'] = $this->setMetas($data);

        if (isset($data['meta']["primary_page"])) {
            $data['json_metas']["primary_page"] = $data['meta']["primary_page"];
        }
        if (isset($data['meta']["parent"])) {
            $data['json_metas']["parent"] = $data['meta']["parent"];
        }

        if (isset($data['type']) and $data['type'] === "landing_pages" and count(Language::all()) > 1) {
            foreach ($data['meta'] as $key => $meta) {
                if (! empty($data['meta'][$key]) and $key !== 'parent') {
                    $data['json_metas'][$key] = $data['meta'][$key];
                }
            }

            if (! empty($data['meta']['parent'])) {
                $pageID      = $data['meta']['parent'];
                $relatedPage = Post::PublishedOrDraft()
                    ->where(function ($query) use ($pageID) {
                        $query->where("id", $pageID)
                            ->orWhere("rel_id", $pageID);
                    })
                    ->where("lang", "!=", $data['lang'])
                    ->first();

                $data['json_metas']['parent'] = $relatedPage->id == $pageID ? $relatedPage->rel_id : $relatedPage->id;
            }
        }

        //Save variable colors
        $theme_colors = get_config('theme_colors');

        if (isset($theme_colors)) {
            foreach ($theme_colors as $key => $color) {
                if (isset($data['content'])) {
                    $data['content'] = str_replace('color: ' . $color, 'color: ' . $key, $data['content']);
                    $data['content'] = str_replace('background-color: ' . $color, 'background-color: ' . $key, $data['content']);
                    $data['content'] = processEditorContent($data['content']);
                }
                if (isset($data['text'])) {
                    $data['text'] = str_replace('color: ' . $color, 'color: ' . $key, $data['text']);
                    $data['text'] = str_replace('background-color: ' . $color, 'background-color: ' . $key, $data['text']);
                    $data['text'] = processEditorContent($data['text']);
                }
            }

            if (isset($data['meta']['background_color'])) {
                $background_color = $data['meta']['background_color'];
                $key              = array_search($background_color, $theme_colors);
                if ($key) {
                    $data['meta']['background_color'] = $key;
                }
            }
            if (isset($data['meta']['text_color'])) {
                $text_color = $data['meta']['text_color'];
                $key        = array_search($text_color, $theme_colors);
                if ($key) {
                    $data['meta']['text_color'] = $key;
                }
            }
        }

        unset($data['meta_title'], $data['meta_description'], $data['meta_keywords']);
        DB::beginTransaction();

        try {
            $this->beforeStore($request, ...$params);
            $model = $this->makeModel(...$params);

            if ($request['taxonomy'] == "tags" && is_null($request['lang'])) {
                exit();
            }

            if (($model instanceof Taxonomy) && $request['taxonomy'] != "tags") {
                $data['lang'] = Language::where('code', config('app.dashboard_locale'))->get()?->first()?->code;
            }

            if (isset($data['meta']['pages']) && ! $this->hasPermissionMultiPages("add", $data['meta']['pages'], $model, ...$params)) {
                $this->authorize(null);
            }

            $this->beforeSave($data, $model, ...$params);
            $model->fill($data);
            $slug = $request->input('slug');
            if ($slug && method_exists($model, 'generateSlug')) {
                $data['slug'] = $model->generateSlug($slug);
            }
            $model->save();


            $this->afterStore($request, $model, ...$params);

            $this->afterSave($data, $model, ...$params);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if (method_exists($this, 'storeSuccess')) {
            $this->storeSuccess($request, $model, ...$params);
        }

        if (method_exists($this, 'saveSuccess')) {
            $this->saveSuccess($request, $model, ...$params);
        }

        return $this->storeSuccessResponse(
            $model,
            $request,
            ...$params
        );
    }

    public function update(Request $request, ...$params): JsonResponse | RedirectResponse
    {
        $validator = $this->validator($request->all(), ...$params);
        if (is_array($validator)) {
            $validator = Validator::make($request->all(), $validator);
        }
        $validator->validate();

        if (is_null($request['images'])) {
            $request['images'] = [];
        }
        if (is_null($request['files'])) {
            $request['files'] = [];
        }

        $data = $this->parseDataForSave($request->all(), ...$params);


        $data['json_metas']['metas'] = $this->setMetas($data);
        $model                       = $this->getDetailModel($this->makeModel(...$params), ...$params);
        $this->checkPermission('edit', $model, ...$params);
        $main_rel_id = is_null($model['rel_id']) ? $model['id'] : $model['rel_id'];

        if ($model->type == 'branches') {
            $data['lang'] = $model->lang;
        }
        if ($model->type == "pages") {
            $this->checkPermission("edit.$main_rel_id", $model, ...$params);
        } else if ($model->type == "posts" && isset($data['meta']['pages']) && ! $this->hasPermissionMultiPages("edit.posts", $model->json_metas['pages'], $model, ...$params)) {
            $this->authorize(null);
        }

        //Save variable colors
        $theme_colors = get_config('theme_colors');
        if ($theme_colors != null) {
            foreach ($theme_colors as $key => $color) {
                if (isset($data['content'])) {
                    $data['content'] = str_replace('color: ' . $color, 'color: ' . $key, $data['content']);
                    $data['content'] = str_replace('background-color: ' . $color, 'background-color: ' . $key, $data['content']);
                    $data['content'] = processEditorContent($data['content']);
                }
                if (isset($data['text'])) {
                    $data['text'] = str_replace('color: ' . $color, 'color: ' . $key, $data['text']);
                    $data['text'] = str_replace('background-color: ' . $color, 'background-color: ' . $key, $data['text']);
                    $data['text'] = processEditorContent($data['text']);
                }
            }
        }

        if (isset($theme_colors)) {
            if (isset($data['meta']['background_color'])) {

                $background_color = $data['meta']['background_color'];
                $key              = array_search($background_color, $theme_colors);
                if ($key) {
                    $data['meta']['background_color'] = $key;
                }
            }

            if (isset($data['meta']['text_color'])) {
                $text_color = $data['meta']['text_color'];
                $key        = array_search($text_color, $theme_colors);
                if ($key) {
                    $data['meta']['text_color'] = $key;
                }
            }
        }

        DB::beginTransaction();
        try {
            $this->beforeUpdate($request, $model, ...$params);
            $slug     = $request->input('slug');
            $old_slug = $model['slug'];

            if ($slug && method_exists($model, 'generateSlug')) {
                $data['slug'] = $model->generateSlug($slug);
            }
            if ($old_slug != $model['slug']) {
                $data['oldslug'] = $old_slug;
            }

            if ($model->getTable() == "posts") {
                if (is_null($slug)) {
                    $data['slug'] = "";
                }
            }

            $model->fill($data);
            $model->save();

            $this->beforeSave($data, $model, ...$params);

            $this->afterUpdate($request, $model, ...$params);

            $this->afterSave($data, $model, ...$params);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if (method_exists($this, 'updateSuccess')) {
            $this->updateSuccess($request, $model, ...$params);
        }

        if (method_exists($this, 'saveSuccess')) {
            $this->saveSuccess($request, $model, ...$params);
        }

        return $this->updateSuccessResponse(
            $model,
            $request,
            ...$params
        );
    }

    public function datatable(Request $request, ...$params): JsonResponse
    {
        $this->checkPermission(
            'index',
            $this->getModel(...$params),
            ...$params
        );

        $table = $this->getDataTable(...$params);
        $table->setCurrentUrl(action([static::class, 'index'], $params, false));

        //Show table result based on admin language
        if (@$params[1] == "categories" || @$params[1] == "tags" || @$params[0] == "sliders") {
            $request['lang'] = Lang::locale();
        }

        list($count, $rows) = $table->getData($request);

        $results = [];
        $columns = $table->columns();

        foreach ($rows as $index => $row) {
            $columns['id'] = $row->id;
            foreach ($columns as $col => $column) {
                if (! empty($column['formatter'])) {
                    $formatter = $column['formatter'](
                        $row->{$col} ?? null,
                        $row,
                        $index
                    );

                    if ($formatter instanceof Renderable) {
                        $formatter = $formatter->render();
                    }

                    $results[$index][$col] = $formatter;
                } else {
                    $results[$index][$col] = $row->{$col};
                }

                if (! empty($column['detailFormater'])) {
                    $results[$index]['detailFormater'] = $column['detailFormater'](
                        $index,
                        $row
                    );
                }
            }
        }

        return response()->json(
            [
                'total' => $count,
                'rows'  => $results,
            ]
        );
    }

    public function bulkActions(Request $request, ...$params): JsonResponse | RedirectResponse
    {
        $request->validate(
            [
                'ids'    => 'required|array',
                'action' => 'required',
            ]
        );

        $action = $request->post('action');
        $ids    = $request->post('ids');

        $table   = $this->getDataTable(...$params);
        $results = [];

        foreach ($ids as $id) {
            $model      = $this->makeModel(...$params)->find($id);
            $permission = $action != 'delete' ? 'edit' : 'delete';

            if (! $this->hasPermission($permission, $model, ...$params)) {
                continue;
            }

            $main_rel_id = is_null($model['rel_id']) ? $model['id'] : $model['rel_id'];
            if ($model->type == "pages" && ! $this->hasPermission("delete.$main_rel_id", $model, ...$params)) {
                continue;
            } else if ($model->type == "posts" && ! $this->hasPermissionMultiPages("delete.posts", $model->json_metas['pages'], $model, ...$params)) {
                continue;
            }

            $results[] = $id;
        }

        $bulkResponse = $table->bulkActions($action, $results);

        if ($bulkResponse !== null && isset($bulkResponse['status']) && ! $bulkResponse['status']) {
            return $this->error([
                'status'  => false,
                'message' => $bulkResponse['message'],
            ]);
        } else {
            return $this->success(
                [
                    'message' => trans_cms('cms::app.successfully'),
                ]
            );
        }
    }

    public function getDataForSelect(Request $request, ...$params): JsonResponse
    {
        $queries   = $request->query();
        $exceptIds = $request->get('except_ids');
        $model     = $this->makeModel(...$params);
        $limit     = $request->get('limit', 10);

        if ($limit > 100) {
            $limit = 100;
        }

        $query = $model::query();
        $query->select(
            [
                'id',
                $model->getFieldName() . ' AS text',
            ]
        );

        $query->whereFilter($queries);

        if ($exceptIds) {
            $query->whereNotIn('id', $exceptIds);
        }

        $paginate        = $query->paginate($limit);
        $data['results'] = $query->get();
        if ($paginate->nextPageUrl()) {
            $data['pagination'] = ['more' => true];
        }

        return response()->json($data);
    }

    protected function getDetailModel(Model $model, ...$params): Model
    {
        return $model
            ->where($this->editKey ?? 'id', $this->getPathId($params))
            ->firstOrFail();
    }

    protected function beforeStore(Request $request, ...$params)
    {
        //
    }

    protected function afterStore(Request $request, $model, ...$params)
    {
        //LOG ACTION TO TABLE
        $data    = $this->resourceModel($model);
        $path    = $data['path'] != "" ? url($model->lang . $data['route'] . $data['path']) : "";
        $content = [
            'method'    => "POST",
            'table'     => $data['table'],
            'id'        => $model->id,
            'type'      => $model->type,
            'label'     => "added a new " . Str::singular($data['table_name']),
            'title'     => $data['title'],
            'path'      => $path,
            'form_data' => $request->all(),
        ];
        log_action($content);
    }

    protected function beforeUpdate(Request $request, $model, ...$params)
    {
        //
    }

    protected function afterUpdate(Request $request, $model, ...$params)
    {
        //LOG ACTION TO TABLE
        $data    = $this->resourceModel($model);
        $path    = $data['path'] != "" ? url($model->lang . $data['route'] . $data['path']) : "";
        $content = [
            'method'    => "PUT",
            'table'     => $data['table'],
            'id'        => $model->id,
            'type'      => $model->type,
            'label'     => "updated " . Str::singular($data['table_name']),
            'title'     => $data['title'],
            'path'      => $path,
            'form_data' => $request->all(),
        ];
        log_action($content);
    }

    protected function resourceModel($model)
    {
        $data = [];
        if ($model->type == "posts" || $model->type == "pages") {
            $data['route'] = "";
        } elseif ($model->type == "authors") {
            $data['route'] = "/authors";
        } elseif ($model->type == "videos" || $model->type == "photos") {
            $data['route'] = "/album";
        } else {
            $data['route'] = "/$model->type";
        }
        $data['path']  = $model->path;
        $data['table'] = $model->getTable();
        if ($model->type) {
            $data['table_name'] = $model->type;
        } else {
            $data['table_name'] = $data['table'];
        }

        if ($data['table'] == "taxonomies") {
            $data['table_name'] = $model->taxonomy;
            $data['title']      = $model->name;
            $data['route']      = "/$model->taxonomy";
            $data['path']       = "/$model->slug";
        } elseif ($data['table'] == "posts") {
            $data['title'] = $model->title;
        } elseif ($data['table'] == "email_templates") {
            $data['title']      = $model->code;
            $data['path']       = "";
            $data['table_name'] = "email template";
        } else {
            $data['title'] = $model->name;
            $data['path']  = "";
        }
        return $data;
    }

    protected function beforeSave(&$data, &$model, ...$params)
    {
        //
    }

    /**
     * After Save model
     *
     * @param array $data
     * @param \Juzaweb\CMS\Models\Model $model
     * @param mixed $params
     */
    protected function afterSave($data, $model, ...$params) {}

    /**
     * @param $params
     * @return \Juzaweb\CMS\Models\ResourceModel
     */
    protected function makeModel(...$params)
    {
        return app($this->getModel(...$params));
    }

    protected function parseDataForSave(array $attributes, ...$params)
    {
        return $attributes;
    }

    /**
     * Get data for form
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    protected function getDataForForm($model, ...$params)
    {
        $data = [
            'model' => $model,
        ];

        if (method_exists($this, 'getSetting')) {
            $data['setting'] = $this->getSetting(...$params);
        }

        return $data;
    }

    /**
     * @throws \Exception
     */
    protected function getDataForIndex(...$params)
    {
        $dataTable = $this->getDataTable(...$params);
        $dataTable->setDataUrl(action([static::class, 'datatable'], $params));
        $dataTable->setActionUrl(action([static::class, 'bulkActions'], $params));
        $dataTable->setCurrentUrl(action([static::class, 'index'], $params, false));

        $canCreate = $this->hasPermission(
            'create',
            $this->getModel(...$params),
            ...$params
        );

        $data = [
            'title'      => $this->getTitle(...$params),
            'dataTable'  => $dataTable,
            'canCreate'  => $canCreate,
            'linkCreate' => action([static::class, 'create'], $params),
        ];

        if (method_exists($this, 'getSetting')) {
            $data['setting'] = $this->getSetting(...$params);
        }

        return $data;
    }

    protected function getPathIdIndex($params)
    {
        return count($params) - 1;
    }

    protected function getPathId($params)
    {
        return $params[$this->getPathIdIndex($params)];
    }

    protected function storeSuccessResponse($model, $request, ...$params)
    {
        $indexRoute = str_replace(
            '.store',
            '.index',
            Route::currentRouteName()
        );
        $redirectUrl = route($indexRoute, $params);
        if ($model->type != "landing_pages") {
            if (isset($model->json_metas['parent'])) {
                $redirectUrl .= '?parent=' . $model->json_metas['parent'];
            } elseif (isset($model->json_metas['pages']) && ! empty($model->json_metas['pages'])) {
                $redirectUrl .= '?pages=' . $model->json_metas['pages'][0];
            }
        } else {
            if (isset($model->json_metas['parent'])) {
                $parent = $model->json_metas['parent'];
                $redirectUrl .= "/$parent/edit";
            }
        }

        return $this->success(
            [
                'message'  => trans_cms('cms::app.created_successfully'),
                'redirect' => $redirectUrl,
                'path'     => method_exists($model, 'getLink') ? $model->getLink() : null,
                'id'       => $model->id,
            ]
        );
    }

    protected function updateSuccessResponse($model, $request, ...$params)
    {
        return $this->success(
            [
                'message' => trans_cms('cms::app.updated_successfully'),
            ]
        );
    }

    protected function isUpdateRoute()
    {
        return Route::getCurrentRoute()->getName() == 'admin.resource.update';
    }

    protected function checkPermission($ability, $arguments = [], ...$params)
    {
        //fix later
        if ($params && $params[0] == "sliders") {
            $ability = "resource_sliders.$ability";
        }
        if ($params && $params[0] == "popups") {
            $ability = "resource_popups.$ability";
        }
        $this->authorize($ability, $arguments);
    }

    protected function hasPermissionMultiPages($method, $abilities = [], $arguments = [], ...$params)
    {
        $allowed = true;
        foreach ($abilities as $ability) {
            $m_ability = Post::find($ability)->rel_id;
            if (isset($m_ability)) {
                $response = Gate::inspect($method . "." . $m_ability, $arguments);
            } else {
                $response = Gate::inspect($method . "." . $ability, $arguments);
            }

            if (! $response->allowed()) {
                $allowed = false;
            }
        }
        return $allowed;
    }

    protected function hasPermission($ability, $arguments = [], ...$params)
    {
        //fix later
        if ($params && $params[0] == "sliders") {
            $ability = "resource_sliders.$ability";
        }
        if ($params && $params[0] == "popups") {
            $ability = "resource_popups.$ability";
        }
        if ($params && $params[0] == "popups-messages") {
            $ability = "resource_popups-messages.$ability";
        }
        $response = Gate::inspect($ability, $arguments);
        return $response->allowed();
    }

    private function setMetas($data = null)
    {
        $siteTitle       = get_config('title_' . Lang::locale());
        $siteName        = get_config('sitename_' . Lang::locale());
        $siteDescription = mb_substr(get_config('description_' . Lang::locale()), 0, 160);
        $siteKeywords    = gettype(get_config('site_keywords_' . Lang::locale())) === 'array' ? (array) get_config('site_keywords_' . Lang::locale()) : (array) json_decode(get_config('site_keywords_' . Lang::locale()));
        $siteTitle       = isset($data['title']) ? $data['title'] : $siteTitle;

        $pageContent    = isset($data['content']) ? seo_string($data['content'], 160) : $siteDescription;
        $ogImageDefault = get_og_default();

        return [
            //google
            "meta_title"               => $data['meta_title'] ?? $siteTitle,
            "meta_heading"             => $data['meta_heading'] ?? $siteTitle,
            "meta_author"              => $data['meta_author'] ?? $siteName,
            "meta_canonical"           => $data['meta_canonical'] ?? config('app.metas.meta_canonical') . '/' . app()->getLocale(),
            "meta_showRobots"          => $data['meta_showRobots'] ?? config('app.metas.meta_showRobots'),
            "meta_robots"              => $data['meta_robots'] ?? config('app.metas.meta_robots'),
            "meta_keywords"            => isset($data['meta_keywords']) ? (array) $data['meta_keywords'] : $siteKeywords,
            "meta_title_keywords"      => isset($data['meta_title_keywords']) ? (array) $data['meta_title_keywords'] : [],
            "meta_description"         => isset($data['meta_description']) ? $data['meta_description'] : $pageContent,
            "meta_copyright"           => config('app.metas.meta_copyright'),
            //facebook
            "meta_og_site_name"        => $data['meta_og_site_name'] ?? $siteName,
            "meta_og_title"            => $data['meta_og_title'] ?? $siteTitle,
            "meta_og_type"             => $data['meta_og_type'] ?? config('app.metas.meta_og_type'),
            "meta_og_url"              => $data['meta_og_url'] ?? config('app.metas.meta_canonical') . '/' . app()->getLocale(),
            "meta_og_image"            => $data['meta_og_image'] ?? $ogImageDefault,
            "meta_og_description"      => isset($data['meta_og_description']) ? $data['meta_og_description'] : $pageContent,
            //twitter
            "meta_twitter_card"        => $data['meta_twitter_card'] ?? config('app.metas.meta_twitter_card'),
            "meta_twitter_title"       => $data['meta_twitter_title'] ?? $siteTitle,
            "meta_twitter_description" => isset($data['meta_twitter_description']) ? $data['meta_twitter_description'] : $pageContent,
            "meta_twitter_image_alt"   => $data['meta_twitter_image_alt'] ?? $siteTitle,
            "meta_twitter_image"       => $data['meta_twitter_image'] ?? $ogImageDefault,
            /*
             to add more metas
             modules\CMS\Traits\ResourceController.php private function setMetas (cruid operation) ,
             config/app.php metas (default hard coded values),
             modules\Backend\resources\views\backend\items\seo_form.blade.php  (cruid form),
             modules\Frontend\resources\views\components\meta\meta.blade.php (front end page),
            */
        ];
    }

    /**
     * Get data table resource
     *
     * @return DataTable
     */
    abstract protected function getDataTable(...$params);

    /**
     * Validator for store and update
     *
     * @param array $attributes
     * @param mixed ...$params
     * @return Validator|array
     */
    abstract protected function validator(array $attributes, ...$params);

    /**
     * Get model resource
     *
     * @param array $params
     * @return string // namespace model
     */
    abstract protected function getModel(...$params);

    /**
     * Get title resource
     *
     * @param array $params
     * @return string
     */
    abstract protected function getTitle(...$params);
}
