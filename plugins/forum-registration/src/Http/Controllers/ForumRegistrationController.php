<?php

namespace Progmix\ForumRegistration\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Traits\ResourceController;
use Progmix\ForumRegistration\Http\Datatables\ForumRegistrationDatatable;
use Progmix\ForumRegistration\Models\ForumRegistration;

class ForumRegistrationController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
        getDataForIndex as traitGetDataForIndex;
    }

    protected $viewPrefix = 'forum-registration::backend';

    protected function getDataTable(...$params)
    {
        return new ForumRegistrationDatatable();
    }

    protected function validator(array $attributes, ...$params)
    {
        $partTypes = ['حضور', 'متحدث', 'راعٍ', 'شريك استراتيجي'];
        $sponsorTypes = ['راعٍ ماسي', 'راعٍ ذهبي', 'راعٍ فضي'];

        return Validator::make($attributes, [
            'name' => 'required|string|max:191',
            'institution' => 'required|string|max:191',
            'job_title' => 'nullable|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => [
                'nullable',
                'regex:/^(?:\+|00)?(?:970|972|0)?[\s\-]?5[69][\s\-]?\d{3}[\s\-]?\d{4}$/',
                'min:8',
                'max:20',
            ],
            'part_type' => ['required', 'string', Rule::in($partTypes)],
            'sponsor_type' => [
                Rule::requiredIf(fn () => ($attributes['part_type'] ?? null) === 'راعٍ'),
                'nullable',
                'string',
                Rule::in($sponsorTypes),
            ],
        ]);
    }

    protected function getModel(...$params)
    {
        return ForumRegistration::class;
    }

    protected function getTitle(...$params)
    {
        return trans('forum-registration::content.title');
    }

    protected function getDataForForm($model, ...$params): array
    {
        return $this->DataForForm($model);
    }

    protected function getDataForIndex(...$params): array
    {
        $data = $this->traitGetDataForIndex(...$params);
        $data['totalRegistrations'] = ForumRegistration::count();
        $data['statsByPartType'] = ForumRegistration::select('part_type', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('part_type')
            ->pluck('count', 'part_type')
            ->toArray();

        return $data;
    }

    public function export()
    {
        $fileName = 'forum_registrations_' . date('Y_m_d_His') . '.csv';
        $registrations = ForumRegistration::all();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Name', 'Institution', 'Job Title', 'Email', 'Phone', 'Part Type', 'Sponsor Type', 'Created At'];

        $callback = function() use($registrations, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 Excel support
            fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            
            fputcsv($file, $columns);

            foreach ($registrations as $reg) {
                fputcsv($file, [
                    $reg->id,
                    $reg->name,
                    $reg->institution,
                    $reg->job_title,
                    $reg->email,
                    $reg->phone,
                    $reg->part_type,
                    $reg->sponsor_type,
                    $reg->created_at ? $reg->created_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
