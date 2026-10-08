<?php

use Carbon\Carbon;
use Google\Service\AnalyticsReporting\OrderBy;
use Illuminate\Support\Arr;
use Juzaweb\Backend\Http\Resources\PostResource;
use Juzaweb\Backend\Http\Resources\PostResourceCollection;
use Juzaweb\Backend\Http\Resources\ResourceResource;
use Juzaweb\Backend\Http\Resources\TaxonomyResource;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Resource;
use Juzaweb\Backend\Models\Taxonomy;
use Juzaweb\CMS\Facades\JWQuery;
use Illuminate\Container\Container;


/**
 * Get the available container instance.
 *
 * @param  string|null  $abstract
 * @param  array  $parameters
 * @return mixed|\Illuminate\Contracts\Foundation\Application
 */
function app_cms($abstract = null, array $parameters = [])
{
    if (is_null($abstract)) {
        return Container::getInstance();
    }

    return Container::getInstance()->make($abstract, $parameters);
}


/**
 * Translate the given message.
 *
 * @param  string|null  $key
 * @param  array  $replace
 * @param  string|null  $locale
 * @return \Illuminate\Contracts\Translation\Translator|string|array|null
 */
function trans_cms($key = null, $replace = [], $locale = null)
{

    if (is_null($key)) {
        return app_cms('translator');
    }

    return app_cms('translator')->get($key, $replace, $locale);
}
function get_posts(?string $type = null, array $options = []): array
{
    return JWQuery::posts($type, $options);
}

function get_posts_by_filter(?array $options): ?array
{
    if ($sortBy = Arr::get($options, 'sort_by')) {
        $options['order_by'] = [$sortBy => Arr::get($options, 'sort_order', 'asc')];
    }

    return JWQuery::posts($options['type'] ?? 'posts', $options);
}

function get_post_taxonomy($post, $taxonomy = null, $params = []): ?array
{
    return JWQuery::postTaxonomy($post, $taxonomy, $params);
}

function get_post_taxonomies($post, $taxonomy = null, $params = [])
{
    return JWQuery::postTaxonomies($post, $taxonomy, $params);
}

function get_related_posts($post, $limit = 5, $taxonomy = null): ?array
{
    return JWQuery::relatedPosts($post, $limit, $taxonomy);
}


/**
 * Get the most popular posts.
 *
 *
 * @param null $type The type of post to retrieve.
 * @param array|null $post An optional post array to exclude from the results.
 * @param int $limit The maximum number of posts to return. Defaults to 5.
 * @param array $options Optional options for retrieving posts.
 *
 * @return array An array of the most popular posts.
 */
function get_popular_posts($type = null, $post = null, $limit = 5, $options = []): array
{
    if ($limit > 20) {
        $limit = 20;
    }

    $query = Post::selectFrontendBuilder();

    if ($post) {
        $query->where('id', '!=', Arr::get($post, 'id'));
    }

    if ($type) {
        $query->where('type', '=', $type);
    }

    $query->orderBy('views', 'DESC');

    $posts = $query->take($limit)->get();

    return PostResourceCollection::make($posts)->toArray(request());
}


/**
 * Retrieve post resources from database.
 *
 * @param string $resource Resource type to query.
 * @param array $options Options for sorting, limiting, and paginating the query.
 * - id: Resource id.
 * - post_id: Post id.
 * - parent_id: Parent resource id.
 * - order_by: Array containing column name and direction to sort by.
 * - paginate: Number of records to paginate.
 * - limit: Limit records returned in the query.
 *
 * @return array Collection of post resources.
 */
function get_post_resources(string $resource, array $options = []): array
{
    $query = Resource::selectFrontendBuilder()->where('type', '=', $resource);

    if ($id = Arr::get($options, 'id')) {
        $query->where('id', '=', $id);
    }

    if ($postId = Arr::get($options, 'post_id')) {
        $query->where('post_id', '=', $postId);
    }

    if ($parentId = Arr::get($options, 'parent_id')) {
        $query->where('parent_id', '=', $parentId);
    }

    if ($orderBys = Arr::get($options, 'order_by')) {
        foreach ($orderBys as $column => $direction) {
            $query->orderBy($column, $direction);
        }
    }

    if ($paginate = Arr::get($options, 'paginate')) {
        if ($paginate > 100) {
            $paginate = 10;
        }

        $data = $query->paginate($paginate);
    } else {
        $limit = Arr::get($options, 'limit', 10);
        if ($limit > 100) {
            $limit = 10;
        }

        $data = $query->limit($limit)->get();
    }
    return ResourceResource::collection($data)->toArray(request());
}

/**
 * Get a single post resource.
 *
 * @param string $resource The name of the resource to get.
 * @param int $id The id of the post to get.
 *
 * @return array|null An array of the resource data, or null if not found.
 */
function get_post_resource(string $resource, int $id): ?array
{
    $query = Resource::selectFrontendBuilder()
        ->where('type', '=', $resource)
        ->where('id', '=', $id);
    $data = $query->first();
    return $data ? (new ResourceResource($data))->toArray(request()) : null;
}

function get_resource($id, $type = "sliders"): ?array
{
    $data = Resource::selectFrontendBuilder()
        ->where('type', '=', "$type")
        ->where('id', '=', $id)
        ->where('lang', app()->getLocale())
        ->where(function ($query) {
            $query->where('date', '<=', Carbon::now())
                ->orWhereNull('date');
        })
        ->where(function ($query) {
            $query->where('end_date', '>=', Carbon::now())
                ->orWhereNull('end_date');
        })
        ->orderBy('id', 'desc')
        ->first();

    return $data ? (new ResourceResource($data))->toArray(request()) : null;
}

function get_fixed_messages(): ?array
{
    $data = Resource::selectFrontendBuilder()
        ->where('type', '=', "popups-messages")
        ->where('lang', app()->getLocale())
        ->whereJsonContains('json_metas->in_all_pages', "1")
        ->where(function ($query) {
            $query->where('date', '<=', Carbon::now())
                ->orWhereNull('date');
        })
        ->where(function ($query) {
            $query->where('end_date', '>=', Carbon::now())
                ->orWhereNull('end_date');
        })
        ->orderBy('id', 'desc')
        ->first();

    return $data ? (new ResourceResource($data))->toArray(request()) : null;
}

/**
 * Return the next resource of a given type.
 *
 * @param string $type The type of resource to get next.
 * @param array|null $resource The current resource which may be used to get the next one.
 *
 * @return array|null The next resource or null if none exists.
 */
function get_next_resource(string $type, ?array $resource): ?array
{
    $query = Resource::selectFrontendBuilder()
        ->where('type', '=', $type)
        ->where('id', '>', Arr::get($resource, 'id'));
    $data = $query->first();
    return $data ? (new ResourceResource($data))->toArray(request()) : null;
}

/**
 * Retrieves the previous post from the database in relation to the current post.
 *
 * @param array|null $currentPost The current Post array for comparison.
 *
 * @return array|null The previous Post as an array or null if not found.
 */
function get_previous_post(?array $currentPost): ?array
{
    $post = Post::selectFrontendBuilder()
        ->where('id', '<', Arr::get($currentPost, 'id'))
        ->orderBy('id', 'DESC')
        ->first();

    return $post ? (new PostResource($post))->toArray(request()) : null;
}

/**
 * Get the next post from the database by ID.
 *
 * @param array $post The post object.
 *
 * @return array|null An array containing the details of the next post, if one exists. Otherwise, null.
 */
function get_next_post($post): ?array
{
    $post = Post::selectFrontendBuilder()
        ->where('id', '>', Arr::get($post, 'id', 0))
        ->orderBy('id', 'ASC')
        ->first();

    if (empty($post)) {
        return null;
    }

    return (new PostResource($post))->toArray(request());
}

function get_taxonomy($taxonomy, $args = []): array
{
    if (empty($taxonomy)) {
        return [];
    }

    $tax = Taxonomy::find($taxonomy);
    return (new TaxonomyResource($tax))
        ->toArray(request());
}

function get_taxonomies($args = []): array
{
    $query = Taxonomy::selectFrontendBuilder();
    $type = Arr::get($args, 'type');
    $taxonomy = Arr::get($args, 'taxonomy');
    $inIds = Arr::get($args, 'id_in');
    $limit = Arr::get($args, 'limit', 10);

    if ($limit > 100) {
        $limit = 10;
    }

    if ($type) {
        $query->where('post_type', '=', $type);
    }

    if ($taxonomy) {
        $query->where('taxonomy', '=', $taxonomy);
    }

    if ($parentId = Arr::get($args, 'parent_id')) {
        $query->where('parent_id', $parentId);
    }

    if ($inIds) {
        if (!is_array($inIds)) {
            $inIds = [$inIds];
        }

        $query->whereIn('id', $inIds);
    }

    $data = $query->limit($limit)->get();

    return TaxonomyResource::collection($data)
        ->toArray(request());
}

function get_total_resource($resource, $args = []): int
{
    $query = Resource::selectFrontendBuilder()->where('type', '=', $resource);

    if ($postId = Arr::get($args, 'post_id')) {
        $query->where('post_id', $postId);
    }

    return $query->count();
}

function get_page_url(string | int | Post | null $page): null | string
{
    if (empty($page)) {
        return null;
    }

    if ($page instanceof Post) {
        return $page->getLink();
    }

    if (is_numeric($page)) {
        $data = Post::cacheFor(3600)->find($page, ['id', 'slug', 'type']);

        if ($data) {
            return $data->getLink();
        }
    }

    $data = Post::cacheFor(3600)
        ->where('slug', '=', $page)
        ->first(['id', 'slug', 'type']);

    return $data?->getLink();
}


if (!function_exists('get_primary_page')) {
    function get_primary_page($post_metas): array
    {
        $main_page = "";
        $pagesIds  = $post_metas['pages'] ?? '';
        if (!empty($pagesIds) || isset($post_metas['primary_page'])) {
            $main_page     = isset($post_metas['primary_page']) && !empty($post_metas['primary_page']) ? $post_metas['primary_page'] : $pagesIds[0];
            $currentLocale = app()->getLocale();
            $cacheTime     = get_config('cache_duration', 0);
            $primary_post  = Cache::remember("primaryPage-$main_page-$currentLocale", $cacheTime, function () use ($main_page) {
                return Post::find($main_page);
            });

            return [
                "id"    => $primary_post->id,
                "title" => $primary_post->subtitle ?? $primary_post->title,
                "path"  => $primary_post->path,
                "slug"  => $primary_post->slug,
                "post"  => $primary_post,
            ];
        }
        return [];
    }
}
