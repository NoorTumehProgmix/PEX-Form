<?php

namespace Progmix\FormBuilder\Http\Controllers;

use Progmix\FormBuilder\Http\Datatables\FormSubmissionsDatatable;
use Progmix\FormBuilder\Models\FormSubmission;
use Progmix\FormBuilder\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Juzaweb\Backend\Models\Language;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Traits\ResourceController;
use Progmix\FormBuilder\Http\Requests\FormSubmissionsRequest;
use Juzaweb\Backend\Exports\FromSubmissionsExport;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Juzaweb\Backend\Models\Post;

class FormSubmissionController extends BackendController
{

    use ResourceController {
        getDataForForm as DataForForm;
        afterSave as tAfterSave;
    }
    protected $viewPrefix = 'formBuilder::backend.submissions';

    public function index(...$params): View
    {

        $langsArray = Language::where('default', 1)->get();
        $langs = $langsArray->pluck('name', 'code')->toArray();

        $this->checkPermission(
            'index',
            $this->getModel(...$params),
            ...$params
        );

        if (method_exists($this, 'getBreadcrumbPrefix')) {
            $this->getBreadcrumbPrefix(...$params);
        }

        return view(
            "{$this->viewPrefix}.index",
            array_merge(
                [
                    'langs' => $langs,
                ],
                $this->getDataForIndex(...$params)
            )
        );
    }

    protected function getDataTable(...$params)
    {
        return new FormSubmissionsDatatable();
    }

    protected function validator(array $attributes, ...$params)
    {
        return true;
    }

    protected function getModel(...$params)
    {
        return FormSubmission::class;
    }

    protected function getTitle(...$params)
    {
        return __('cms::app.forms_Submissions');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }

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
        if ($form->is_database_submittable) {
            FormSubmission::create([
                'form_id' => $request->dynamicCode['id'],
                'form_data' => json_encode($submission),
                'meta_data' => json_encode($metadata),
            ]);
        }

        if ($form->destinations != null) {
            $destinations = json_decode($form->destinations);
            foreach ($destinations as $key => $value) {
                send_email_notification($value, url($request->pagePath), $submission);
            }
        }

        return Response::json(['message' => 'Success'], 200);
    }

    public function view($id)
    {
        $formSubmission = FormSubmission::findOrFail($id);
        $formData = json_decode($formSubmission['form_data'], true);
        $data = $formData['data'];
        unset($data['submit']);

        $lang = app()->getLocale();

        $title = $formSubmission->form->name;
        return view($this->viewPrefix . '.show', compact('formSubmission', 'lang', 'data', 'title'));
    }

    public function exportTable(Request $request)
    {
        $form = $request->input('formsSelect');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if (isset($form)) {
            $form_data = Form::find($form);
            $filename = Str::slug($form_data->name) . '_submissions_' . Carbon::now()->format('Y-m-d-H-i') . '.xlsx';
            return Excel::download(new FromSubmissionsExport($form, $start_date, $end_date), $filename);
        } else {
            $filename = 'forms_submissions.xlsx';
            return Excel::download(new FromSubmissionsExport(null, $start_date, $end_date), $filename);
        }
    }
}
