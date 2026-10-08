<?php



namespace Juzaweb\Translation\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Juzaweb\CMS\Contracts\TranslationManager;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Support\ArrayPagination;

class TranslationController extends BackendController
{
    public function __construct(protected TranslationManager $translationManager)
    {
    }

    public function index(): View
    {
        global $jw_user;
        if (!$jw_user->can('translations.index')) {
            abort(403);
        }

        return view(
            'translation::translation.index',
            [
                'title' => trans_cms('cms::app.translations'),
            ]
        );
    }

    public function getDataTable(Request $request): JsonResponse
    {
        global $jw_user;
        if (!$jw_user->can('translations.index')) {
            abort(403);
        }

        $search = $request->get('search');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $page = $offset <= 0 ? 1 : (round($offset / $limit)) + 1;

        $result = $this->translationManager->modules();

        if ($search) {
            $result = $result->filter(
                fn ($item) => str_contains(strtolower($item['title']), strtolower($search))
            );
        }



        $total = count($result);
        $items = ArrayPagination::make($result)->paginate($limit, $page)->values();

        return response()->json(
            [
                'total' => $total,
                'rows' => $items
            ]
        );
    }
}
