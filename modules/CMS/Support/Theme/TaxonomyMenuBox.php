<?php


namespace Juzaweb\CMS\Support\Theme;

use Juzaweb\CMS\Abstracts\MenuBox;
use Juzaweb\CMS\Facades\HookAction;
use Illuminate\Support\Facades\Cache;

class TaxonomyMenuBox extends MenuBox
{
    protected $key;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $taxonomy;

    public function __construct($key, $taxonomy)
    {
        $this->key = $key;
        $this->taxonomy = $taxonomy;
    }

    public function mapData($data)
    {
        $result = [];
        $items = $data['items'];

        /**
         * @var \Illuminate\Database\Eloquent\Builder $query
         */
        $query = app($this->taxonomy->get('model'))->query();
        $items = $query->whereIn('id', $items)->get();

        foreach ($items as $item) {
            $result[] = $this->getData([
                'label' => $item->name,
                'model_id' => $item->id,
            ]);
        }

        return $result;
    }

    public function getData($item)
    {
        return [
            'label' => $item['label'],
            'model_class' => $this->taxonomy->get('model'),
            'model_id' => $item['model_id'],
        ];
    }

    public function addView()
    {
        return view('cms::backend.menu.boxs.taxonomy_add', [
            'taxonomy' => $this->taxonomy,
            'key' => $this->key,
        ]);
    }

    public function editView($item)
    {
        return view('cms::backend.menu.boxs.taxonomy_edit', [
            'taxonomy' => $this->taxonomy,
            'key' => $this->key,
            'item' => $item,
        ]);
    }

    public function getLinks($menuItems)
    {
        $lang = app()->getLocale();
        $permalink = HookAction::getPermalinks($this->taxonomy->get('taxonomy'));
        $base = $permalink->get('base');
        $query = app($this->taxonomy->get('model'))->query();
        $cacheKey = "menu-items-$lang-" . md5(json_encode($menuItems->pluck('model_id')->toArray()));
        $items = Cache::remember($cacheKey, get_config('cache_duration', 0), function () use ($query, $menuItems) {
            return $query->whereIn('id', $menuItems->pluck('model_id')->toArray())
                ->get(['id', 'path'])->keyBy('id');
        });
        return $menuItems->map(function ($item) use ($base, $items, $lang) {
            if (! empty($items[$item->model_id])) {
                $item->link = url()->to($lang . '/' . $base . '/' . $items[$item->model_id]->path);
            }

            return $item;
        });
    }
}
