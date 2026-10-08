<?php

namespace Juzaweb\CMS\Support;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Juzaweb\Backend\Models\EmailList;

class SendEmail
{
    protected EmailList $mail;

    public function __construct(EmailList $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Send email by row email_lists table
     *
     * @return bool
     * @throws Exception
     */
    public function send(): bool
    {
        $validate = $this->validate();
        if ($validate !== true) {
            $this->updateError($validate);
            return false;
        }

        $this->updateStatus('processing');

        try {
            $body = $this->mail->getBody();
            $subject = $this->mail->getSubject();
            $attachments = $this->resolveAttachments();

            $client_id = get_config('email.client_id');
            $tenant_id = get_config('email.tenant_id');
            $client_secret = get_config('email.client_secret');
            if ($client_id != "" && $tenant_id != "" && $client_secret != "") {
                $oauth_token = $this->getOAuthToken($client_id, $client_secret, $tenant_id);
                if ($oauth_token) {
                    $userPrincipalName = config('mail.username');
                    $graphUrl = "https://graph.microsoft.com/v1.0/users/$userPrincipalName/sendMail";
                    $message = [
                        "message" => [
                            "subject" => $subject,
                            "body" => [
                                "contentType" => "HTML",
                                "content" => $body,
                            ],
                            "toRecipients" => [
                                ["emailAddress" => ["address" => $this->mail->email]],
                            ],
                        ],
                    ];

                    if (! empty($attachments)) {
                        $message['message']['attachments'] = array_map(
                            static fn (array $attachment) => [
                                '@odata.type' => '#microsoft.graph.fileAttachment',
                                'name' => $attachment['name'],
                                'contentType' => $attachment['mime'],
                                'contentBytes' => base64_encode($attachment['data']),
                            ],
                            $attachments
                        );
                    }

                    $response = Http::withToken($oauth_token)->post($graphUrl, $message);
                }
            } else {
                Mail::send(
                    'cms::backend.email.layouts.default',
                    [
                        'body' => $body,
                    ],
                    function ($message) use ($subject, $attachments) {
                        $message->to([$this->mail->email])
                            ->subject($subject);

                        foreach ($attachments as $attachment) {
                            $message->attachData(
                                $attachment['data'],
                                $attachment['name'],
                                ['mime' => $attachment['mime']]
                            );
                        }
                    }
                );
            }

            /*if (Mail::failures()) {
                $this->updateError(array_merge([
                    'title' => 'Mail failures',
                ], Mail::failures()));

                return false;
            }*/

            $this->updateStatus('success', $subject, $body);

            return true;
        } catch (Exception $e) {
            $this->updateError(
                [
                    'title' => 'Send mail exception',
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'line' => $e->getLine(),
                ]
            );

            if (config('app.debug')) {
                throw $e;
            }
            report($e);
            return false;
        }
    }

    protected function updateStatus(
        string $status,
        $subject = null,
        $body = null
    ): bool {
        $update = [
            'status' => $status
        ];

        if ($subject && $body) {
            $data = $this->mail->data;
            $data['subject'] = $subject;
            $data['body'] = $body;
            $update['data'] = $data;
        }

        return $this->mail->update($update);
    }

    protected function updateError(array $error = []): bool
    {
        return $this->mail->update(
            [
                'error' => $error,
                'status' => 'error',
            ]
        );
    }

    /**
     * Send mail validate
     *
     * @return bool|array
     */
    protected function validate(): bool|array
    {
        return true;
    }

    /**
     * @return array<int, array{data: string, name: string, mime: string}>
     */
    protected function resolveAttachments(): array
    {
        $attachments = Arr::get($this->mail->data, 'attachments', []);
        $resolved = [];

        foreach ($attachments as $attachment) {
            if (isset($attachment['path'])) {
                $disk = $attachment['disk'] ?? 'local';
                $path = $attachment['path'];

                if (! Storage::disk($disk)->exists($path)) {
                    continue;
                }

                $resolved[] = [
                    'data' => Storage::disk($disk)->get($path),
                    'name' => $attachment['name'],
                    'mime' => $attachment['mime'] ?? 'application/octet-stream',
                ];

                continue;
            }

            if (isset($attachment['data'], $attachment['name'])) {
                $resolved[] = [
                    'data' => $attachment['data'],
                    'name' => $attachment['name'],
                    'mime' => $attachment['mime'] ?? 'application/octet-stream',
                ];
            }
        }

        return $resolved;
    }

    protected function getOAuthToken($clientId, $clientSecret, $tenantId)
    {

        $graphUrl = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token";

        $response = Http::asForm()->post($graphUrl, [
            'client_id' => $clientId,
            'scope' => 'https://graph.microsoft.com/.default',
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
        ]);

        $accessToken = $response['access_token'];
        return $accessToken;
    }
}
