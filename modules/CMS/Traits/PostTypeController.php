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

trait PostTypeController
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

        if ($params[0] == "pages" or $params[0] == "branches") { //add branches to an already made if statement for pagination
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
     * @param array $data
     * @param Post $model
     * @param mixed ...$params
     * @return void
     * @throws Exception
     */
    function afterStore(Request $request, $model, ...$params): void
    {
        $data = $this->parseDataForSave($request->all(), ...$params);
        $langs = Language::where('code', '!=', config('app.dashboard_locale'))->get();

        foreach ($langs as $lang) {
            // Duplicate with rel_id = the ID of the inserted post
            $duplicateResource = $model->replicate();
            $duplicateResource->slug = $model->slug; // Use the same slug for the duplicate
            $duplicateResource->path = $model->path;
            $duplicateResource->rel_id = $model->id;
            $duplicateResource->display_order = $model->display_order;
            $duplicateResource->show_sitemap = $model->show_sitemap;

            $duplicateResource->lang = $lang->code;
            $new_status = $model['status'] == "preview" ? "preview" : "draft";

            //Reset and empty these values
            if ($new_status != "preview") {
                $duplicateResource->description = '';
                $duplicateResource->text = '';
                $duplicateResource->content = '';
                unset($duplicateResource['meta_title'], $duplicateResource['meta_description'], $duplicateResource['meta_keywords'], $duplicateResource['subtitle'], $duplicateResource['published_at']);
            }
            $duplicateResource->status = $new_status;


            //Save duplicate post
            $duplicateResource->save();

            //Update page metas
            $duplicateResource->path = '/' . $model->slug. '/';
            if (isset($data['meta']['ctemplate'])) {
                $duplicateResource->setMeta('ctemplate', $data['meta']['ctemplate']);
            }

            if (isset($data['meta']['hide_section'])) {
                $duplicateResource->setMeta('hide_section', $data['meta']['hide_section']);
            }
            $duplicateResource->save();


            if (isset($data['meta']['parent'])) {
                $rel_parent = Post::select('id')
                    ->where('rel_id', $data['meta']['parent'])
                    ->where('lang', $lang->code)
                    ->first();

                $rel_parent_id = is_null($rel_parent) ? $data['meta']['parent'] : $rel_parent['id'];
                $duplicateResource->setMeta('parent', $rel_parent_id);
                $path = $duplicateResource['slug'] == "" ? "" : getFullPath($rel_parent_id, $duplicateResource['slug'] . '/');
                $duplicateResource->path = $path;
                $duplicateResource->save();
            }

            if (isset($data['meta']['defaults'])) {
                $rel_def_ids = [];
                foreach ($data['meta']['defaults'] as $def) {
                    $rel_def = Post::select('id')
                        ->where('rel_id', $def)
                        ->where('lang', $lang->code)
                        ->first();

                    if ($rel_def) {
                        $rel_def_ids[] = $rel_def['id'];
                    }
                }
                $duplicateResource->setMeta('defaults', $rel_def_ids);
                $duplicateResource->save();
            }
        }
    }

    function afterUpdate(Request $request, $model, ...$params): void
    {
        $data = $this->parseDataForSave($request->all(), ...$params);

        //Update affected slugs
        if ($model->type == "pages") {
            $new_slug = $model['slug'];
            $old_slug = $model['oldslug'];
            $old_path = $model['path'];
            if ($old_slug != $new_slug && $model['status'] != "preview") {
                $uposts = Post::where('path', 'LIKE', "$old_path%")->get();
                foreach ($uposts as $upost) {
                    $newPath = str_replace("/$old_slug/%", "/$new_slug/%", $upost->path);
                    $pattern = "/\/" . $old_slug . "\//";
                    if ($new_slug != "") {
                        $newPath = preg_replace($pattern, '/' . $new_slug . '/', $upost->path, 1);
                    } else {
                        $newPath = preg_replace($pattern, '/', $upost->path, 1);
                    }
                    $upost->path = $newPath;
                    $upost->save();
                }
            }
        }

        $main_rel_id = is_null($model['rel_id']) ? $model['id'] : $model['rel_id'];
        //Get all posts in all languages based on post to edit
        $related_ids = Post::select('id', 'lang')
            ->where('id', '!=', $model['id'])
            ->where('rel_id', $main_rel_id)
            ->orWhere(function ($query) use ($model, $main_rel_id) {
                $query->whereNull('rel_id')
                    ->where('id', '!=', $model['id'])
                    ->where('id', $main_rel_id);
            })
            ->get();

        if ($related_ids && $related_ids->count() != 0) {
            foreach ($related_ids as $related_post) {
                $tagIds = [];
                $post_to_update = Post::find($related_post->id);

                //Sync all related posts with same slung,thumb,template
                $post_to_update->slug = $model['slug'];
                $post_to_update->thumbnail = $model['thumbnail'];
                $post_to_update->display_order = $model['display_order'];
                $post_to_update->show_sitemap = $model['show_sitemap'];
                if (isset($data['meta']['ctemplate'])) {
                    $post_to_update->setMeta('ctemplate', $data['meta']['ctemplate']);
                }

                if (isset($data['meta']['hide_section'])) {
                    $post_to_update->setMeta('hide_section', $data['meta']['hide_section']);
                }

                //Update page parent
                if (isset($data['meta']['parent'])) {
                    $main_parent_rel = Post::find($data['meta']['parent']);
                    $main_parent_id = is_null($main_parent_rel['rel_id']) ? $main_parent_rel['id'] : $main_parent_rel['rel_id'];
                    $rel_parent = Post::where(function ($query) use ($main_parent_id) {
                        $query->where('rel_id', $main_parent_id)
                            ->orWhere('id', $main_parent_id);
                    })
                        ->where('lang', $related_post->lang)
                        ->first();

                    $rel_parent_id = is_null($rel_parent) ? $data['meta']['parent'] : $rel_parent['id'];
                    $post_to_update->setMeta('parent', $rel_parent_id);
                    $path = $post_to_update['slug'] == "" ? "" : getFullPath($rel_parent_id, $post_to_update['slug'] . '/');
                    $post_to_update->path = $path;
                } else {
                    $post_to_update->setMeta('parent', '');
                }
                $post_to_update->save();

                if (isset($data['meta']['defaults'])) {
                    $rel_def_ids = [];
                    foreach ($data['meta']['defaults'] as $def) {
                        $def_parent_rel = Post::find($def);
                        $def_parent_id = is_null($def_parent_rel['rel_id']) ? $def_parent_rel['id'] : $def_parent_rel['rel_id'];
                        $rel_def = Post::where(function ($query) use ($def_parent_id) {
                            $query->where('rel_id', $def_parent_id)
                                ->orWhere('id', $def_parent_id);
                        })->select('id')->where('lang', $related_post->lang)->first();

                        if ($rel_def) {
                            $rel_def_ids[] = $rel_def['id'];
                        }
                    }
                    $post_to_update->setMeta('defaults', $rel_def_ids);
                } else {
                    $post_to_update->setMeta('defaults', []);
                }
                $post_to_update->save();

                //Update posts tags
                if ($model->type == "posts") {
                    //Update pages
                    $rel_pages_ids = [];
                    $post_pages = [];
                    if (isset($data['meta']['pages']) && !is_null($data['meta']['pages'])) {
                        $post_pages = $data['meta']['pages'];
                    }
                    foreach ($post_pages as $page) {
                        $main_parent_rel = Post::find($page);
                        $main_parent_id = is_null($main_parent_rel['rel_id']) ? $main_parent_rel['id'] : $main_parent_rel['rel_id'];

                        $rel_page = Post::where(function ($query) use ($main_parent_id) {
                            $query->where('rel_id', $main_parent_id)
                                ->orWhere('id', $main_parent_id);
                        })
                            ->where('lang', $related_post->lang)
                            ->first();

                        $rel_page_id = is_null($rel_page) ? $page : $rel_page['id'];
                        $rel_pages_ids[] = "$rel_page_id";
                    }
                    $post_to_update->setMeta('pages', $rel_pages_ids);

                    //Update authors
                    $rel_authors_ids = [];
                    $post_authors = [];
                    if (isset($data['meta']['authors']) && !is_null($data['meta']['authors'])) {
                        $post_authors = $data['meta']['authors'];
                    }
                    foreach ($post_authors as $author) {
                        $main_author_rel = Post::find($author);
                        $main_author_id = is_null($main_author_rel['rel_id']) ? $main_author_rel['id'] : $main_author_rel['rel_id'];

                        $rel_author = Post::where(function ($query) use ($main_author_id) {
                            $query->where('rel_id', $main_author_id)
                                ->orWhere('id', $main_author_id);
                        })
                            ->where('lang', $related_post->lang)
                            ->first();

                        $rel_author_id = is_null($rel_author) ? $author : $rel_author['id'];
                        $rel_authors_ids[] = "$rel_author_id";
                    }
                    $post_to_update->setMeta('authors', $rel_authors_ids);

                    //Update primary page
                    if (isset($data['meta']['primary_page'])) {
                        $main_primary_rel = Post::find($data['meta']['primary_page']);
                        $main_primary_id = is_null($main_primary_rel['rel_id']) ? $main_primary_rel['id'] : $main_primary_rel['rel_id'];
                        $rel_primary = Post::where(function ($query) use ($main_primary_id) {
                            $query->where('rel_id', $main_primary_id)
                                ->orWhere('id', $main_primary_id);
                        })
                            ->where('lang', $related_post->lang)
                            ->first();

                        $rel_primary_id = is_null($rel_primary) ? $data['meta']['primary_page'] : $rel_primary['id'];
                        $post_to_update->setMeta('primary_page', $rel_primary_id);
                    } else {
                        $post_to_update->setMeta('primary_page', '');
                    }

                    //Update tags each for it's own
                    unset($data['tags']);
                    if (isset($post_to_update->json_taxonomies) && $post_to_update->json_taxonomies != null) {
                        $taxonomies_tags = $post_to_update->json_taxonomies;
                        $tagIds = array();
                        foreach ($taxonomies_tags as $taxonomy_tag) {
                            if ($taxonomy_tag['taxonomy'] === 'tags') {
                                $tagIds[] = $taxonomy_tag['id'];
                            }
                        }
                        $data['tags'] = $tagIds;
                    }

                    $meta_page = $post_to_update->getMeta('primary_page');

                    if (isset($meta_page) && $meta_page != "") {
                        $directParent = $meta_page;
                    } else {
                        $meta_pages = @$post_to_update->getMeta('pages')[0];
                        if (isset($meta_pages)) {
                            $directParent = $meta_pages;
                        }
                    }

                    $path = '/' . $post_to_update['slug']. '/';
                    if (isset($directParent)) {
                        $path = $post_to_update['slug'] == "" ? "" : getFullPath($directParent, $post_to_update['slug'] . '/');
                    }
                    $post_to_update->path = $path;
                    $post_to_update->syncTaxonomies($data);
                }
            }
        }
    }

    function afterSave($data, $model, ...$params): void
    {

        $keywords = Arr::get($data, 'search_keywords');
        $keywords = $keywords != "" ? implode(',', $keywords) : "";
        $data['search_keywords'] = $keywords;

        //Update Post Path
        $directParent = 0;
        if ($model['type'] == "posts") {
            if (isset($data['meta']['primary_page'])) {
                $directParent = $data['meta']['primary_page'];
            } else {
                if (isset($data['meta']['pages'])) {
                    $directParent = $data['meta']['pages'][0];
                }
            }
            if (!isset($data['meta']['pages'])) {
                $data['meta']['pages'] = [];
            }
            if (!isset($data['meta']['authors'])) {
                $data['meta']['authors'] = [];
            }
            if (!isset($data['meta']['primary_page'])) {
                $data['meta']['primary_page'] = [];
            }
        }

        if (isset($data['meta']['parent'])) {
            $directParent = $data['meta']['parent'];
        } else {
            $data['meta']['parent'] = "";
        }

        if ($model['status'] == "preview" || $data['status'] == "preview") {
            $model['slug'] = $model['slug'] . "-preview";
        }
        $data['slug'] = $model['slug'];


        $path = $model['slug'] == "" ? "" : getFullPath($directParent, $model['slug'] . '/');

        $data['path'] = $path;

        $model->fill($data);
        $model->save();
        $this->traitAfterSave($data, $model, ...$params);
        $model->syncTaxonomies($data);

        if ($blocks = Arr::get($data, 'blocks', [])) {
            $data['meta']['block_content'] = collect($blocks)
                ->mapWithKeys(
                    function ($item, $key) {
                        return [$key => array_values($item)];
                    }
                )->toArray();
        }

        if (Arr::has($data, 'meta')) {
            $meta = Arr::get($data, 'meta', []);
            $model->syncMetasWithoutDetaching($meta);
        }
        $permissionsArray = [
            "view.$model->id",
            "edit.$model->id",
            "delete.$model->id",
            "add.$model->id",
            "edit.posts.$model->id",
            "delete.posts.$model->id",
        ];
        foreach ($permissionsArray as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission],
                ['description' => 'page permissions']
            );
        }

        $user = jw_current_user();
        foreach ($user['roles'] as $role) {
            $roleModel = Role::find($role->id);
            $roleModel->givePermissionTo($permissionsArray);
        }

        do_action('post_types.after_save', $model, $data);
        do_action("post_type.{$this->getPostType()}.after_save", $model, $data);

        event(new AfterPostSave($model, $data));
    }

    /**
     * @param mixed ...$params
     * @return string
     * @throws Exception
     */
    function getTitle(...$params): string
    {
        return $this->getSetting()->get('label');
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

    function getSetting(): Collection
    {
        $postType = $this->getPostType();
        $setting = HookAction::getPostTypes($postType);
        if (empty($setting)) {
            throw new Exception('Post type does not exists.');
        }

        return $setting;
    }

    /**
     * Get data table resource
     *
     * @param mixed ...$params
     * @return PostTypeDataTable|DataTable
     * @throws Exception
     */
    function getDataTable(...$params): PostTypeDataTable|DataTable
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
            $langsArray = Language::where('code', config('app.dashboard_locale'))->get();
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
        $subtitles = $attributes['repeater_subtitles'] ?? [];
        $links = $attributes['repeater_links'] ?? [];
        $images = $attributes['repeater_images'] ?? [];
        $secondary_images = $attributes['repeater_secondary_images'] ?? [];
        $descriptions = $attributes['repeater_descriptions'] ?? [];
        $newTab = $attributes['repeater_new_tabs'] ?? [];
        $buttonLink = $attributes['repeater_button_links'] ?? [];
        $date = $attributes['repeater_date'] ?? [];
        $bg_colors = $attributes['bg_colors'] ?? [];
        $text_colors = $attributes['text_colors'] ?? [];
        $theme_colors = get_config('theme_colors');

        $repeater = [];
        foreach ($titles as $key => $title) {
            if (!empty($title)) {
                $bg_color = null;
                if (isset($bg_colors[$key])) {
                    $bg_color = $bg_colors[$key];
                    $color_key = array_search($bg_color, $theme_colors);
                    if ($color_key !== false) {
                        $bg_color = $color_key;
                    }
                }
                $text_color = null;
                if (isset($text_colors[$key])) {
                    $text_color = $text_colors[$key];
                    $text_color_key = array_search($text_color, $theme_colors);
                    if ($text_color_key !== false) {
                        $text_color = $text_color_key;
                    }
                }

                $parsed_desc = null;
                if (isset($descriptions[$key])) {
                    $desc = $descriptions[$key];
                    foreach ($theme_colors as $ckey => $color) {
                        $desc = str_replace('color: ' . $color, 'color: ' . $ckey, $desc);
                        $desc = str_replace('background-color: ' . $color, 'background-color: ' . $ckey, $desc);
                    }
                    $parsed_desc = $desc;
                }
                $parsed_desc_html = processEditorContent($parsed_desc);

                if (isset($images[$key]) && !empty($images[$key])) {
                    $processImageJob = new ProcessImage($images[$key], "scale", ['1000x600'], null);
                    dispatch($processImageJob->onQueue("sliders"));
                }

                if (isset($secondary_images[$key]) && !empty($secondary_images[$key])) {
                    $processImageJob = new ProcessImage($secondary_images[$key], "scale", ['1000x600'], null);
                    dispatch($processImageJob->onQueue("sliders"));
                }

                $repeater[] = [
                    'title' => $title,
                    'subtitle' => $subtitles[$key] ?? null,
                    'link' => $links[$key] ?? null,
                    'image' => $images[$key] ?? null,
                    'secondary_image' => $secondary_images[$key] ?? null,
                    'description' => $parsed_desc_html,
                    'new_tab' => $newTab[$key] ?? 0,
                    'button_link' => $buttonLink[$key] ?? 0,
                    'date' => $date[$key] ?? null,
                    'bg_color' => $bg_color,
                    'text_color' => $text_color,
                ];
            }
        }
        $attributes['meta']['repeater'] = $repeater;

        //attributes taxonomy for compare
        $attrs_ids = $attributes['attributes_ids'] ?? [];
        $attrs_vals = $attributes['attributes_values'] ?? [];
        $attrs = [];
        foreach ($attrs_ids as $key => $taxonomy_id) {
            $attrs[] = [
                'taxonomy_id' => $taxonomy_id,
                'value' => $attrs_vals[$key] ?? null,
            ];
        }
        $attributes['meta']['attributes'] = $attrs;

        return apply_filters(
            "post_type.{$this->getPostType()}.parseDataForSave",
            $attributes
        );
    }

    function checkPermission($ability, $arguments = [], ...$params): void
    {

        $this->authorize($ability, [$arguments, $this->getPostType()]);
    }

    function hasPermission($ability, $arguments = [], ...$params): bool
    {
        $response = Gate::inspect($ability, [$arguments, $this->getPostType()]);
        return $response->allowed();
    }

    function updateSuccessResponse($model, $request, ...$params): JsonResponse|RedirectResponse
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

    /**
     * @param Model|Post $model
     * @return array|Collection
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function getTemplateData(Model|Post $model): array|Collection
    {
        $template = $this->getTemplate($model);

        if (empty($template)) {
            return [];
        }

        return HookAction::getThemeTemplates($template);
    }

    /**
     * @param Model|Post $model
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function getTemplate(Model|Post $model): ?string
    {
        $template = request()->get('template');
        if (empty($template)) {
            $template = $model->getMeta('template');
        }

        return $template;
    }

    /**
     * Get post type name
     *
     * @return string|null
     */
    function getPostType(): ?string
    {
        if (empty($this->postType)) {
            return request()->segment(3);
        }

        return $this->postType;
    }
}

function getFullPath($postId, $path = "")
{
    $post = Post::find($postId);
    if ($post) {
        if (substr($path, 0, 1) !== '/') {
            if ($post->slug != "") {
                $path = '/' . $post->slug . '/' . $path;
            } else {
                $path = '/' . $path;
            }
        } else {
            $path = '/' . $post->slug . $path;
        }

        $metas = $post->json_metas;
        if (isset($metas['parent'])) {
            $path = getFullPath($metas['parent'], $path);
        }
    } else {
        $path = '/' . $path;
    }
    $path = str_replace('//', '/', $path);
    return $path;
}
