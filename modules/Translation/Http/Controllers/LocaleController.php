<?php



namespace Juzaweb\Translation\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Juzaweb\CMS\Contracts\TranslationManager;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Support\ArrayPagination;
use Spatie\TranslationLoader\LanguageLine;
use Juzaweb\Backend\Models\ActionLog;
use Illuminate\Support\Facades\Auth;

class LocaleController extends BackendController
{
    public function __construct(protected TranslationManager $translationManager)
    {
    }

    public function index($type, $locale): View
    {

        global $jw_user;
        if (!$jw_user->can('translations.index')) {
            abort(403);
        }

        $data = $this->translationManager->modules()->get($type);

        $language = config("locales.{$locale}.name");



        $language = config("locales.{$locale}.name");



        if (empty($data)) {
            return abort(404);
        }

        $this->addBreadcrumb(
            [
                'title' => trans('cms::app.translations'),
                'url' => route('admin.translations.index'),
            ]
        );

        $this->addBreadcrumb(
            [
                'title' => $data?->get('title'),
                'url' => route('admin.translations.type', [$type]),
            ]
        );

        return view(
            'translation::translation.locale',
            [
                'title' => $language,
                'data' => $data,
                'type' => $type,
                'locale' => $locale,
            ]
        );
    }

    public function save(Request $request, $type, $locale): JsonResponse|RedirectResponse
    {
        global $jw_user;
        if (!$jw_user->can('translations.edit')) {
            abort(403);
        }

        $module = $this->translationManager->modules()->get($type);
        $group = $request->post('group');
        $value = $request->post('value');

        $model = LanguageLine::firstOrNew([
            'namespace' => $module->get('namespace'),
            'group' => $group,
            'key' => $request->post('key'),
        ]);

        $model->setTranslation($locale, $value);
        $model->save();

        $content = [
            'method' => "PUT",
            'table' => $model->getTable(),
            'id' => $model->id,
            'type' => $model->type,
            'label' => "updated translations",
            'title' => $request->post('value'),
            'path' => "",
            'form_data' => $request->all(),
        ];
        log_action($content);
        return $this->success(
            [
                'message' => 'ok',
            ]
        );
    }

    public function getDataTable(Request $request, $type, $locale): JsonResponse
    {
        global $jw_user;
        if (!$jw_user->can('translations.index')) {
            abort(403);
        }

        $search = strtolower($request->get('search'));
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $page = $offset <= 0 ? 1 : (round($offset / $limit)) + 1;

        $data = $this->translationManager->modules()->get($type);

        // if ($type == 'author_form_builder') {

        $result  = LanguageLine::where('namespace', $data['namespace'])
            ->where('group', $data['type'])
            ->where(function ($query) use ($search) {
                if ($search) {
                    $query->where('key', 'like', '%' . $search . '%')
                        ->orWhere('text->' . app()->getLocale(), 'like', '%' . $search . '%');
                }
            })
            ->get()
            ->map(function ($item) use ($locale) {

                $jsonValue = $item->text;

                return [
                    "key"   => $item->key,
                    "group" => $item->group,
                    "value" => $item->key,
                    "trans" => $jsonValue[$locale] ??  $item->key,
                ];
            })
            ->toArray();

        $items = ArrayPagination::make($result)->paginate($limit, $page)->values();

        $total = count($result);


        // } else {
        //     $result = $this->translationManager->locale($data)->translationLines($locale);
        //     if ($search) {
        //         $result = collect($result)
        //             ->filter(
        //                 function ($item) use ($search) {
        //                     return (
        //                         str_contains(strtolower($item['key']), $search) ||
        //                         str_contains(strtolower($item['value']), $search)
        //                     );
        //                 }
        //             );
        //     }
        //     $total = count($result);

        //     $items = ArrayPagination::make($result)->paginate($limit, $page)->values();

        //     $langs = LanguageLine::where(['namespace' => $data->get('namespace')])
        //         ->whereIn('key', $items->pluck('key')->toArray())
        //         ->get(['key', 'text'])
        //         ->keyBy('key');

        //     $items = $items->map(
        //         function ($item) use ($langs, $locale) {
        //             $item['trans'] = $langs->get($item['key'])->text[$locale] ?? $item['trans'];
        //             return $item;
        //         }
        //     );
        // }


        return response()->json(
            [
                'total' => $total,
                'rows' => $items,
            ]
        );
    }
}
