<?php

/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/juzacms
 * @author     Juzaweb Team <admin@juzaweb.com>
 * @link       https://juzaweb.com
 * @license    MIT
 */

namespace Juzaweb\Backend\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Support\Email;

class EmailController extends BackendController
{
    public function save(Request $request): \Illuminate\Http\JsonResponse  | \Illuminate\Http\RedirectResponse
    {
        $email = $request->only(
            [
                'email.host',
                'email.port',
                'email.encryption',
                'email.username',
                'email.password',
                'email.from_address',
                'email.from_name',
                'email.contact_email',
                'email.tenant_id',
                'email.client_id',
                'email.client_secret',
            ]
        );

        $email = $email['email'];

        set_config('email', $email);
        $content = [
            'method' => "PUT",
            'table' => "configs",
            'id' => "",
            'type' => "email",
            'label' => "updated email settings",
            'title' => "",
            'path' => "",
            'form_data' => $request->all(),
        ];
        log_action($content);
        return $this->success(
            [
                'message' => trans_cms('cms::app.save_successfully'),
            ]
        );
    }



    public function sendTestMail(Request $request): \Illuminate\Http\JsonResponse  | \Illuminate\Http\RedirectResponse
    {
        $request->validate(
            [
                'email' => 'required|email',
            ]
        );
        $email = $request->post('email');
        Email::make()
            ->setEmails($email)
            ->withTemplate('notification')
            ->setSubject('Send email for {{ name }}')
            ->setBody(
                'Hello {{ name }}, If you receive this email, it means that your config email on Progmix CMS is active.'
            )
            ->setParams(['name' => Auth::user()->name])
            ->send();

        return $this->success(
            [
                'message' => trans_cms('cms::app.send_mail_successfully'),
            ]
        );
    }
}
