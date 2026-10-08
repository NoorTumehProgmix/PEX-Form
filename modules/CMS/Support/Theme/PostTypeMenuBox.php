<?php



namespace Juzaweb\CMS\Support\Theme;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Abstracts\MenuBox;
use Juzaweb\CMS\Facades\HookAction;

class PostTypeMenuBox extends MenuBox
{
    protected $key;
    protected $postType;

    public function __construct($key, $postType)
    {
        $this->key = $key;
        $this->postType = $postType;
    }

    public function mapData($data)
    {
        $result = [];
        if (isset($data['items'])) {
            $items = $data['items'];
            $query = app($this->postType->get('model'))->query();
            $items = $query->whereIn('id', $items)->get();

            foreach ($items as $item) {
                $result[] = $this->getData([
                    'label' => $item->getTitle(),
                    'model_id' => $item->id,
                ]);
            }
        }
        return $result;
    }

    public function getData($item)
    {
        return [
            'label' => $item['label'],
            'model_class' => $this->postType->get('model'),
            'model_id' => $item['model_id'],
        ];
    }

    public function addView()
    {
        $items = app($this->postType->get('model'))
            ->where('type', $this->postType->get('key'))
            ->where('lang', Lang::locale())
            ->where('status', Post::STATUS_PUBLISH)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        $parentIds = collect($items)
            ->map(fn ($item) => is_array($item->json_metas ?? null) ? ($item->json_metas['parent'] ?? null) : null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $parents = $parentIds !== []
            ? Post::whereIn('id', $parentIds)->get(['id', 'title'])->keyBy('id')
            : collect();

        foreach ($items as $item) {
            $parentId = is_array($item->json_metas ?? null) ? ($item->json_metas['parent'] ?? null) : null;
            $item->parent_title = $parentId ? ($parents->get($parentId)?->title ?? '') : '';
        }

        return view('cms::backend.menu.boxs.post_type_add', [
            'key' => $this->key,
            'postType' => $this->postType,
            'items' => $items,
        ]);
    }

    public function editView($item)
    {
        return view('cms::backend.menu.boxs.post_type_edit', [
            'item' => $item,
            'postType' => $this->postType,
        ]);
    }

    public function getLinks($menuItems)
    {

        $permalink = HookAction::getPermalinks($this->postType->get('key'));
        $lang = app()->getLocale();


        if (empty($permalink)) {
            $base = '';
        } else {
            $base = $permalink->get('base');
        }

        $base = $base == "" ? $lang  : $lang . '/' . $base;
        $query = app($this->postType->get('model'))->query();
        $cacheKey = "menu-items-$lang-" . md5(json_encode($menuItems->pluck('model_id')->toArray()));
        $items = Cache::remember($cacheKey, get_config('cache_duration', 0), function () use ($query, $menuItems) {
            return $query->whereIn('id', $menuItems->pluck('model_id')->toArray())
                ->get(['id', 'path'])->keyBy('id');
        });
        return $menuItems->map(function ($item) use ($base, $items) {
            if (!empty($items[$item->model_id])) {
                $item->link = url()->to($base . $items[$item->model_id]->path);
            }
            return $item;
        });
    }
}
