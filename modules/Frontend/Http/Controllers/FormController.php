<?php

namespace Juzaweb\Frontend\Http\Controllers;

use Juzaweb\Frontend\Http\Requests\FormSubmissionsRequest;
use Juzaweb\Backend\Models\Post;
use Illuminate\Support\Facades\Response;
use Juzaweb\Backend\Models\Plugins\FormSubmission;
use Juzaweb\Backend\Models\Plugins\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\TranslationLoader\LanguageLine;
use Illuminate\Support\Facades\View;

class FormController extends Controller
{

    public function saveFormJson(FormSubmissionsRequest $request)
    {
        $path = $request->pagePath;
        $slugs = explode('/', $path);

        $page = Post::where('slug', array_pop($slugs))->first();
        $metadata['page'] = [
            'id' => $page->id,
            'name' => $page->name,
            'type' => $page->type,
            'slug' => $page->slug,
            'path' =>  $request->pagePath,
        ];

        $metadata['client'] =  [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->headers->get('referer'),
        ];

        $submission = $request->submission;
        $submission['data']['page'] =   $page->title;
        $form = Form::findOrFail($request->dynamicCode['id']);
        // if ($form->is_database_submittable) {
        //     FormSubmission::create([
        //         'form_id' => $request->dynamicCode['id'],
        //         'form_data' => json_encode($submission),
        //         'meta_data' => json_encode($metadata),
        //     ]);
        // }

        if ($form->destinations != null) {
            $destinations = json_decode($form->destinations);
            foreach ($destinations as $key => $value) {
                send_email_notification_for_forms($value, url($request->pagePath), $submission);
            }
        }


        return Response::json(['message' => 'Success'], 200);
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
            foreach ($data as $keyValue => $component) {
                $data[$keyValue] = $this->getTrans($data[$keyValue], $translationsResult, $key);
            }
        } else {
            $formDefinition = trim($form->form_definition, '"');
            $viewPath = 'frontend::forms.' . $formDefinition;
            if (View::exists($viewPath)) {
                $content = view($viewPath)->render();
            }
        }
        $langJson = json_encode($translationsResult);

        return [
            'formDefinition' => json_encode($data),
            'langJson'       => $langJson,
            'sideCode'       => $form->side_code,
            'content'        => $content,
            'type'           => $form->type,
            'submittable'    => $form->submittable,
            'recaptchaSiteKey' =>  get_config('google_captcha.site_key')
        ];
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

    private function getTrans(&$component, &$translationsResult, &$key)
    {
        if (isset($component['suffix']) && !empty($component['suffix'])) {
            $component['suffix'] = $this->restoreDomain($component['suffix']);
        }

        if (isset($component['prefix']) && !empty($component['prefix'])) {
            $component['prefix'] = $this->restoreDomain($component['prefix']);
        }


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
            foreach ($component['columns'] as $keyValue => $componentColumn) {
                $component['columns'][$keyValue] = $this->getTrans($componentColumn, $translationsResult, $key);
            }
        }

        if (isset($component['components']) && count($component['components']) > 0) {
            foreach ($component['components'] as $keyValue => $componentColumnComponents) {
                $component['components'][$keyValue] = $this->getTrans($componentColumnComponents, $translationsResult, $key);
            }
        }

        return  $component;
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

    // private function restoreInputsPrefixesSuffixesImage(&$component)
    // {
    //     if (isset($component['suffix']) && !empty($component['suffix'])) {
    //         $component['suffix'] = $this->restoreDomain($component['suffix']);
    //     }

    //     if (isset($component['prefix']) && !empty($component['prefix'])) {
    //         $component['prefix'] = $this->restoreDomain($component['prefix']);
    //     }

    //     if (isset($component['columns']) && count($component['columns']) > 0) {
    //         foreach ($component['columns'] as &$column) {
    //             $this->restoreInputsPrefixesSuffixesImage($column);
    //         }
    //     }

    //     if (isset($component['components']) && count($component['components']) > 0) {
    //         foreach ($component['components'] as &$subComponent) {
    //             $this->restoreInputsPrefixesSuffixesImage($subComponent);
    //         }
    //     }
    // }
}
