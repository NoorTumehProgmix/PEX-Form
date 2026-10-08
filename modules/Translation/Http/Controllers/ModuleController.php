<?php



namespace Juzaweb\Translation\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Juzaweb\CMS\Contracts\TranslationManager;
use Juzaweb\CMS\Support\ArrayPagination;
use Juzaweb\Translation\Facades\Locale;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\Translation\Http\Requests\AddLanguageRequest;
use Juzaweb\Backend\Models\Language;
use Spatie\TranslationLoader\LanguageLine;

class ModuleController extends BackendController
{
    public function __construct(protected TranslationManager $translationManager)
    {
    }

    public function index($type): View
    {
        global $jw_user;
        if (!$jw_user->can('translations.index')) {
            abort(403);
        }

        $this->addBreadcrumb(
            [
                'title' => trans('cms::app.translations'),
                'url' => route('admin.translations.index')
            ]
        );

        $data = $this->translationManager->modules()->get($type);

        return view(
            'translation::translation.module',
            [
                'title' => $data?->get('title'),
                'type' => $type
            ]
        );
    }

    public function add(AddLanguageRequest $request, $type): JsonResponse
    {

        global $jw_user;
        if (!$jw_user->can('translations.create')) {
            abort(403);
        }

        $languages = $this->translationManager->locale($this->translationManager->modules()->get($type))->languages();
        $locale = $request->post('locale');


        if ($languages->get($locale)) {
            //return $this->error(trans('cms::app.language_already_exist'));
        }

        //Insert to Languages table
        $lang_name = $request->lang_custom_name == "" ? $locale : $request->lang_custom_name;
        Language::updateOrCreate([
            'code' => $locale,
            'name' => $lang_name,
            'default' => 0,
        ]);

        $customs = get_config('custom_languages', []);
        $customs[$type][] = $locale;
        set_config('custom_languages', $customs);

        return $this->success(
            [
                'message' => trans('cms::app.add_language_successfull')
            ]
        );
    }

    public function addFormBuilder(Request $request, $type = 'progmix_form_builder'): JsonResponse
    {
        global $jw_user;
        if (!$jw_user->can('translations.create')) {
            abort(403);
        }

        $data = $this->translationManager->modules()->get($type);

        $key = $request->key;
        $namespace = $data['namespace'];
        $this->validate($request, [
            'key' => ['required', Rule::unique('language_lines')->where(function ($query) use ($key, $namespace) {
                return $query->where('key', $key)
                    ->where('namespace', $namespace);
            })]
        ]);

        try {
            LanguageLine::create([
                'text' => [],
                'group' =>  $data['type'],
                'key' => $request->key,
                'namespace' =>  $data['namespace'],
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'object_key' => null,
                'object_type' => null,
            ]);
        } catch (Exception $e) {
            return $this->success(
                [
                    'message' => $e->getMessage()
                ]
            );
        }


        return $this->success(
            [
                'message' => trans('cms::app.add_language_successfull')
            ]
        );
    }
    public function edit(AddLanguageRequest $request, $type): JsonResponse
    {
        global $jw_user;
        if (!$jw_user->can('translations.edit')) {
            abort(403);
        }

        //Insert to Languages table
        $lang_name = $request->lang_custom_name == "" ? $locale : $request->lang_custom_name;
        Language::updateOrCreate([
            'code' => $locale,
            'name' => $lang_name,
            'default' => 0,
        ]);

        return $this->success(
            [
                'message' => trans('cms::app.add_language_successfull')
            ]
        );
    }

    public function getDataTable(Request $request, $type): JsonResponse
    {
        global $jw_user;
        if (!$jw_user->can('translations.index')) {
            abort(403);
        }

        $search = $request->get('search');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $page = $offset <= 0 ? 1 : (round($offset / $limit)) + 1;
        //$result = $this->translationManager->locale($this->translationManager->modules()->get($type))->languages();

        // Retrieve the list of languages from the "languages" table
        $result = Language::all();

        if ($search) {
            $result = collect($result)->filter(
                function ($item) use ($search) {
                    return (
                        str_contains($item['name'], $search) ||
                        str_contains($item['code'], $search)
                    );
                }
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
