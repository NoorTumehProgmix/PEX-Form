<?php

namespace Juzaweb\Subscriptions\Http\Controllers\Backend;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Juzaweb\Subscriptions\Http\Datatables\SubscriptionDatatable;
use Juzaweb\Subscriptions\Models\Subscription;
use Juzaweb\Backend\Exports\SubscriptionExport;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Support\Email;
use Juzaweb\CMS\Traits\ResourceController;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class SubscriptionController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
        afterSave as tAfterSave;
    }

    protected $viewPrefix = 'subscriptions::backend.subscription';

    protected function getDataTable(...$params)
    {
        return new SubscriptionDatatable();
    }

    protected function validator(array $attributes, ...$params)
    {
        $validator = Validator::make($attributes, [
            // Rules
        ]);

        return $validator;
    }

    protected function getModel(...$params)
    {
        return Subscription::class;
    }

    protected function getTitle(...$params)
    {
        return trans('subscriptions::content.subscriptions');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }

    protected function afterSave($data, $model, ...$params)
    {
        if ($this->hasPermission('subscriptions.manage', $model)) {
            $this->tAfterSave($data, $model);
            if (isset($data['email_sent']) && $data['email_sent'] == 1 && $data['status'] != "") {
                Email::make()
                    ->withTemplate($data['status'])
                    ->setEmails([$model['email']])
                    ->setParams(
                        [
                            'name' => $model['first_name'] . ' ' . $model['last_name'],
                            'email' => $model['email'],

                        ]
                    )
                    ->send();
            }
        }
    }
    protected function hasPermission($ability, $arguments = [], ...$params)
    {
        $response = Gate::inspect($ability, $arguments);
        return $response->allowed();
    }

    public function exportTable()
    {
        $filename = 'subscriptions_' . Carbon::now()->format('Y-m-d-H-i') . '.xlsx';

        return Excel::download(new SubscriptionExport, $filename);
    }
}
