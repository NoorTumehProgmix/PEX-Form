<?php

namespace Progmix\FormBuilder\Http\Controllers;

use DB;
use Progmix\FormBuilder\Http\Datatables\FormsDatatable;
use Progmix\FormBuilder\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Juzaweb\Backend\Models\Language;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Traits\ResourceController;
use Spatie\TranslationLoader\LanguageLine;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;


class FormBuilderController extends BackendController
{

    use ResourceController;

    protected $viewPrefix = 'formBuilder::backend';
    protected $viewStaticPrefix = 'formBuilder::backend.static';

    protected function getDataTable(...$params)
    {
        return new FormsDatatable();
    }

    protected function validator(array $attributes, ...$params)
    {
        return true;
    }

    protected function getModel(...$params)
    {
        return Form::class;
    }

    protected function getTitle(...$params)
    {
        return trans('cms::app.forms_builder');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }

    public function create($type, ...$params)
    {
        $this->checkPermission('create', $this->getModel(...$params), ...$params);

        if ($type == Form::TYPES['FORM_STATIC']) {
            return view($this->viewStaticPrefix . '.index', [
                'title'  => 'form builder',
                'form'   => null,
                'type'   => Form::TYPES['FORM_STATIC'],
                'create' => true
            ]);
        }
        return view($this->viewPrefix . '.formio.add', [
            'title' => 'form builder',
            'type'  => Form::TYPES['FORM_BUILDER'],
        ]);
    }

    public function store(Request $request, $type, ...$params)
    {
        $this->checkPermission('create', $this->getModel(...$params), ...$params);

        $request->validate([
            'form_name'       => 'required|string|min:2|max:190|unique:forms,name',
            'json_definition' => 'nullable|array',
            'js_editor'       => 'nullable|sometimes|string|max:65535',
            'mails.*'       => 'email|required|string|max:155',
        ]);

        if ($type == Form::TYPES['FORM_STATIC']) {
            DB::transaction(function () use ($request) {
                $fileName    = Str::slug($request->input('form_name'));
                if (Form::where('side_code', json_encode($fileName))->exists()) {
                    return redirect()->back()->withErrors(['form_name' => 'File already exists.'])->withInput();
                }

                Form::create([
                    'name'            => $request->input('form_name'),
                    'form_definition' => json_encode($fileName),
                    'validations'     => null,
                    'type'            => Form::TYPES['FORM_STATIC'],
                    'side_code'       => json_encode($fileName),
                    'submittable'       => ($request->has('submittable')  && $request->submittable == 1),
                    'is_database_submittable'       => ($request->has('database') && $request->database == 1),
                    'destinations'       => ($request->has('submittable') && $request->has('mail')) ? json_encode($request->mails) : null,
                ]);
            });
            return redirect('admin-cp/form-builder');
        } else {
            $formData              = $request->input('json_definition');
            $data               = $formData;
            foreach ($data as $key => $component) {
                $data[$key] = $this->changeInputsPrefixesSuffixesImage($component);
            }
            $form                  = new Form();
            $form->form_definition = json_encode($data);
            $form->name            = $request->input('form_name');
            $validations           = $this->getValidations($formData, $form->id);
            $form->validations     = json_encode($validations);
            $form->type            = Form::TYPES['FORM_BUILDER'];
            $form->side_code       = json_encode($request->input('js_editor'));
            $form->submittable       = ($request->has('submittable') && $request->submittable == 1);
            $form->is_database_submittable       = ($request->has('database') && $request->database == 1);
            $form->destinations       = ($request->has('submittable') && $request->mail) ? json_encode($request->mails) : null;
            $form->save();
        }


        return response()->json('success', 200);
    }

    public function edit($id, ...$params)
    {
        $form = Form::findOrFail($id);
        $this->checkPermission('edit', $form, ...$params);
        $title = $form->name;
        if ($form->type == Form::TYPES['FORM_STATIC']) {
            $create = false;
            return view($this->viewStaticPrefix . '.index', compact('form', 'title', 'create'));
        } else {
            $data = json_decode($form->form_definition, true);
            foreach ($data as &$component) {
                $this->restoreInputsPrefixesSuffixesImage($component);
            }

            $form->form_definition = json_encode($data);
        }
        return view($this->viewPrefix . '.formio.edit', compact('form', 'title'));
    }

    public function update(Form $form, Request $request, ...$params)
    {
        $this->checkPermission('edit', $form, ...$params);
        $request->validate([
            'form_name'       => 'required|string|min:2|max:190',
            'json_definition' => 'nullable|array',
            'js_editor'       => 'nullable|sometimes|string|max:65535',
            'mails.*'       => 'email|required|string|max:155',
        ]);

        if ($form->type == Form::TYPES['FORM_STATIC']) {
            $form->name = $request->input('form_name');
            $form->submittable       = ($request->has('submittable') && $request->submittable == 1);
            $form->is_database_submittable       = ($request->has('database') && $request->database == 1);
            $form->destinations       = ($request->has('submittable') && $request->has('mail')) ?  json_encode($request->mails) : null;
            $form->save();
            return redirect('admin-cp/form-builder');
        } else {
            $formData              = $request->input('json_definition');
            $data               = $formData;

            foreach ($data as $key => $component) {
                $data[$key] =  $this->changeInputsPrefixesSuffixesImage($component);
            }
            $validations           = $this->getValidations($formData, $form->id);

            $form->form_definition = json_encode($data);
            $form->name            = $request->input('form_name');
            $form->validations     = json_encode($validations);
            $form->side_code       = json_encode($request->input('js_editor'));
            $form->submittable       = ($request->has('submittable')  && $request->submittable == 1);
            $form->is_database_submittable       = ($request->has('database') && $request->database == 1);
            $form->destinations       = ($request->has('submittable') && $request->mail) ?  json_encode($request->mails) : null;
            $form->save();
            return response()->json(['id' => $form->id], 200);
        }
    }

    public function getFormJson(Form $form)
    {
        $data               = json_decode($form->form_definition, true);
        $lang               = [];
        $translationsResult = [];
        $key                = [];

        $translationsResult['ar'] = [
            "error"         => "يرجى مراجعة الأخطاء في النموذج",
            "invalid_date"  => "{{field}} ليس تاريخًا صالحًا.",
            "invalid_email" => "{{field}} يجب أن يكون عنوان بريد إلكتروني صالحًا.",
            "invalid_regex" => "{{field}} لا يتطابق مع النمط {{regex}}.",
            "mask"          => "{{field}} لا يتطابق مع القناع.",
            "max"           => "{{field}} لا يمكن أن يكون أكبر من {{max}}.",
            "maxLength"     => "{{field}} يجب أن يكون أقصر من {{length}} حرفًا.",
            "min"           => "{{field}} لا يمكن أن يكون أقل من {{min}}.",
            "minLength"     => "{{field}} يجب أن لا يكون أقل من {{length}} حرفًا.",
            "next"          => "التالي",
            "pattern"       => "{{field}} لا يتطابق مع النمط {{pattern}}",
            "previous"      => "السابق",
            "required"      => "{{field}} مطلوب",
            "complete"      => "تم إرسال النموذج بنجاح",
            "maxDate"       => "{{field}} يجب ألا يحتوي على تاريخ بعد {{- maxDate}}",
            "minDate"       => "{{field}} يجب ألا يحتوي على تاريخ قبل {{- minDate}}",
            "maxYear"       => "{{field}} يجب ألا يحتوي على سنة أكبر من {{maxYear}}",
            "minYear"       => "{{field}} يجب ألا يحتوي على سنة أقل من {{minYear}}"
        ];

        $content = null;
        if ($form->type != Form::TYPES['FORM_STATIC']) {
            foreach ($data as $component) {
                $this->getTrans($component, $translationsResult, $key);
            }
        } else {
            $content = view('formBuilder::forms.' . trim($form->form_definition, '"'))->render();
        }
        $langJson = json_encode($translationsResult);

        return [
            'formDefinition' => $form->form_definition,
            'langJson'       => $langJson,
            'sideCode'       => $form->side_code,
            'content'        => $content,
            'type'           => $form->type
        ];
    }

    private function getTrans($component, &$translationsResult, &$key)
    {
        if (isset($component['label']) && !empty($component['label'])) {
            $labelKey = $component['label'];
            $key[]    = $labelKey;
            if ($labelKey) {
                $translations = LanguageLine::where('namespace', 'formBuilder')->where('group', 'plugin')->where('key', $labelKey)->get();

                if ($translations->count()) {
                    foreach ($translations as $translation) {
                        $translationsData = $translation->text;

                        if (!empty($translationsData)) {
                            foreach ($translationsData as $lang => $translationText) {

                                if (!isset($translationsResult[$lang])) {
                                    $translationsResult[$lang] = [];
                                }
                                $translationsResult[$lang][$translation->key] = $translationText;
                            }
                        }
                    }
                }
            }
        }

        if (isset($component['columns']) && count($component['columns']) > 0) {
            foreach ($component['columns'] as $componentColumn) {
                $this->getTrans($componentColumn, $translationsResult, $key);
            }
        }

        if (isset($component['components']) && count($component['components']) > 0) {
            foreach ($component['components'] as $componentColumnComponents) {
                $this->getTrans($componentColumnComponents, $translationsResult, $key);
            }
        }
    }

    private function changeInputsPrefixesSuffixesImage(&$component)
    {
        if (isset($component['suffix']) && !empty($component['suffix'])) {
            $component['suffix'] =  $this->removeDomain($component['suffix']);
        }

        if (isset($component['prefix']) && !empty($component['prefix'])) {
            $component['prefix'] =  $this->removeDomain($component['prefix']);
        }

        if (isset($component['columns']) && count($component['columns']) > 0) {
            foreach ($component['columns'] as $key => $componentColumn) {
                $component['columns'][$key] = $this->changeInputsPrefixesSuffixesImage($componentColumn);
            }
        }

        if (isset($component['components']) && count($component['components']) > 0) {
            foreach ($component['components'] as $key => $componentColumnComponents) {
                $component['components'][$key] =  $this->changeInputsPrefixesSuffixesImage($componentColumnComponents);
            }
        }
        return  $component;
    }

    private function removeDomain($fullPath)
    {
        $pattern = '/<img\s+src="([^"]+)"/';
        preg_match_all($pattern, $fullPath, $matches);
        foreach ($matches[1] as $originalSrc) {
            $path = Str::after($originalSrc, '/storage/');
            $newSrc = 'src="' . $path . '"';
            $fullPath = str_replace('src="' . $originalSrc . '"', $newSrc, $fullPath);
        }
        return $fullPath;
    }

    private function restoreDomain($fullPath)
    {
        $pattern = '/<img\s+src="([^"]+)"/';
        preg_match_all($pattern, $fullPath, $matches);
        foreach ($matches[1] as $path) {
            $fullUrl = upload_url($path);
            $fullPath = str_replace('src="' . $path . '"', 'src="' . $fullUrl . '"', $fullPath);
        }
        return $fullPath;
    }

    private function restoreInputsPrefixesSuffixesImage(&$component)
    {
        if (isset($component['suffix']) && !empty($component['suffix'])) {
            $component['suffix'] = $this->restoreDomain($component['suffix']);
        }

        if (isset($component['prefix']) && !empty($component['prefix'])) {
            $component['prefix'] = $this->restoreDomain($component['prefix']);
        }

        if (isset($component['columns']) && count($component['columns']) > 0) {
            foreach ($component['columns'] as &$column) {
                $this->restoreInputsPrefixesSuffixesImage($column);
            }
        }

        if (isset($component['components']) && count($component['components']) > 0) {
            foreach ($component['components'] as &$subComponent) {
                $this->restoreInputsPrefixesSuffixesImage($subComponent);
            }
        }
    }

    public function submitForm(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Form submitted successfully!');
    }

    /**
     * @throws \Exception
     */
    protected function getDataForIndex(...$params)
    {
        $dataTable = $this->getDataTable(...$params);
        $dataTable->setDataUrl(action([static::class, 'datatable'], $params));
        $dataTable->setActionUrl(action([static::class, 'bulkActions'], $params));
        $dataTable->setCurrentUrl(action([static::class, 'index'], $params, false));

        $canCreate = 1; // $this->hasPermission(
        //     'create',
        //     $this->getModel(...$params),
        //     ...$params
        // );

        $data = [
            'title'      => $this->getTitle(...$params),
            'dataTable'  => $dataTable,
            'canCreate'  => $canCreate,
            'linkCreate' => action([static::class, 'create'], $params),
        ];

        if (method_exists($this, 'getSetting')) {
            $data['setting'] = $this->getSetting(...$params);
        }

        return $data;
    }

    private function getValidations($formData, $id)
    {
        $validations = [];
        foreach ($formData as $component) {
            if (isset($component['validate'])) {
                foreach ($component['validate'] as $attributes => $value) {
                    if ($value) {
                        if (in_array($attributes, ['minLength'])) {
                            $validations[$component['key']][] = 'min:' . $value;
                        }
                        if (in_array($attributes, ['maxLength'])) {
                            $validations[$component['key']][] = 'max:' . $value;
                        }
                        if ($attributes == 'required' && $value == true) {
                            $validations[$component['key']][] = 'required';
                        }
                        //'onlyAvailableItems','minSelectedCount', 'maxSelectedCount'
                        if (in_array($attributes, ['minWords', 'maxWords', 'min', 'max'])) {
                            $validations[$component['key']][] = $attributes . ':' . $value;
                        }

                        if ($attributes == 'onlyAvailableItems' && $value == true) {
                            $validations[$component['key']][] = 'onlyAvailableItems:' . $id . ',' . $component['key'];
                        }

                        if ($attributes == 'pattern') {
                            $validations[$component['key']][] = 'regex:' . $value;
                        }
                    }
                }
            }

            if (isset($component['unique'])) {
                $validations[$component['key']][] = 'uniqueJson:' . $id . ',' . $component['key'];
            }

            if ($component['type'] == 'datetime') {
                if (isset($component['datePicker'])) {
                    $validations[$component['key']][] = 'before:' . $component['maxDate'];
                    $validations[$component['key']][] = 'after:' . $component['minDate'];
                }
            }
            if ($component['type'] == 'day') {
                $validations[$component['key']][] = 'before:' . $component['maxDate'];
                $validations[$component['key']][] = 'after:' . $component['minDate'];
            }
        }
        return $validations;
    }

    public function ibanGenerate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bc' => 'required',
            'an' => 'required|numeric|digits_between:5,7',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $bc = $request->select;
        $an = $request->an;
        $cc = $request->cc;
        $lc = $request->lc;
        $sa = $request->sa;

        $accountn = strlen($an);
        $has_ad   = 'no';

        if ($accountn == 5) {

            $has_ad = 'yes';

            $an = '00' . $an;
        }


        if ($has_ad == 'no') {

            if ($accountn == 6) {
                $an = '0' . $an;
            }
        }


        $start = '25102128';
        $end   = '252800';


        $med = $bc . $an . $cc . $lc . $sa;

        $full_no = $start . $med . $end;

        $num1 = $full_no;
        $num2 = "97";

        $x = bcmod($num1, $num2);

        $y = 98 - $x;

        $y = $y . '';


        if (strlen($y) == 1) {

            $y = '0' . $y;
        }

        $ibannumber = 'PS' . $y . 'PALS' . $med;


        return response()->json(['success' => true, "iban" => $ibannumber]);
    }
}
