<?php



namespace Juzaweb\Backend\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Http\Controllers\BackendController;

class PermalinkController extends BackendController
{
    public function index(): \Illuminate\Contracts\View\View
    {
        global $jw_user;
        if (!$jw_user->can('settings.permalinks')) {
            abort(403);
        }

        $title = trans_cms('cms::app.permalinks');
        $permalinks = HookAction::getPermalinks();

        return view(
            'cms::backend.permalink.index',
            compact(
                'title',
                'permalinks'
            )
        );
    }

    public function save(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        global $jw_user;
        if (!$jw_user->can('settings.permalinks')) {
            abort(403);
        }
        $request->validate(
            [
                'permalink' => 'required|array',
                // 'permalink.*.base' => 'required|string|min:3|max:15',
            ]
        );

        $permalinks = HookAction::getPermalinks();
        $data = $request->post('permalink');
        $result = [];

        foreach ($permalinks as $key => $permalink) {
            $result[$key] = [
                'base' => $data[$key]['base']
            ];
        }

        set_config('permalinks', $result);

        do_action(Action::PERMALINKS_SAVED_ACTION, $permalinks);

        return $this->success(
            [
                'message' => trans_cms('cms::app.save_successfully'),
                'redirect' => route('admin.permalink'),
            ]
        );
    }
}
