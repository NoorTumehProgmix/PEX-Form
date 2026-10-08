<?php



namespace Juzaweb\Backend\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Http\Controllers\BackendController;

class WidgetController extends BackendController
{
    public function __construct()
    {
        do_action(Action::WIDGETS_INIT);
    }

    public function index(): \Illuminate\Contracts\View\View
    {
        global $jw_user;
        if (!$jw_user->can('widgets.index')) {
            abort(403);
        }

        $title = trans_cms('cms::app.widgets');
        $widgets = HookAction::getWidgets();
        $sidebars = HookAction::getSidebars();

        return view(
            'cms::backend.widget.index',
            compact(
                'title',
                'widgets',
                'sidebars'
            )
        );
    }

    public function update(Request $request, $key): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        global $jw_user;
        if (!$jw_user->can('widgets.edit')) {
            abort(403);
        }
        $content = collect($request->input('content', []))
            ->keyBy('key');

        foreach ($content as $wkey => $widget) {
            $widgetData = HookAction::getWidgets($widget['widget']);
            $data = $widgetData['widget']->update($widget);
            $content->put($wkey, $data);
        }

        set_theme_config('sidebar_' . $key, $content->toArray());

        return $this->success(
            [
                'message' => trans_cms('cms::app.save_successfully'),
            ]
        );
    }

    public function getWidgetItem(Request $request)
    {
        global $jw_user;
        if (!$jw_user->can('widgets.index')) {
            abort(403);
        }
        $this->validate(
            $request,
            [
                'widget' => 'required',
                'sidebars' => 'required|array',
            ]
        );

        $widget = $request->get('widget');
        $sidebars = $request->get('sidebars');

        $widgetData = HookAction::getWidgets($widget);
        $results = [];
        foreach ($sidebars as $sidebar) {
            $key = Str::random(10);
            $results[$sidebar] = [
                'key' => $key,
                'html' => view(
                    'cms::backend.widget.components.sidebar_widget_item',
                    [
                        'widget' => $widgetData,
                        'sidebar' => $sidebar,
                        'key' => $key,
                    ]
                )->render(),
            ];
        }

        return response()->json(
            [
                'widget' => $widget,
                'items' => $results,
            ]
        );
    }

    public function getWidgetForm($key)
    {
        global $jw_user;
        if (!$jw_user->can('widgets.index')) {
            abort(403);
        }

        $widget = HookAction::getWidgets($key);
        $key = Str::random(10);

        return response()->json(
            [
                'key' => $key,
                'html' => view(
                    'cms::backend.post.components.page_block_item',
                    compact(
                        'widget',
                        'key'
                    )
                )->render(),
            ]
        );
    }
}
