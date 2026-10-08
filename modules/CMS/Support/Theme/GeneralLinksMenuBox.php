<?php



namespace Juzaweb\CMS\Support\Theme;

use Juzaweb\CMS\Abstracts\MenuBox;
use Progmix\Links\Models\Link;

class GeneralLinksMenuBox extends MenuBox
{
    public function mapData($data)
    {
        $result[] = $this->getData($data);

        return $result;
    }

    public function getData($item)
    {
        return [
            'label' => $item['label'] == "" ? Link::find($item['link'])['name'] : $item['label'],
            'link' => $item['link'],
        ];
    }

    public function addView()
    {
        $links = Link::orderBy('id', 'desc')
            ->get();
        return view('cms::backend.menu.boxs.general_add', [
            'items' => $links,
        ]);
    }

    public function editView($item)
    {
        return view('cms::backend.menu.boxs.general_edit', [
            'item' => $item,
        ]);
    }

    public function getLinks($menuItems)
    {
        return $menuItems;
    }
}
