<?php

namespace Juzaweb\CMS\Support;

use Juzaweb\CMS\Jobs\SendEmailJob;
use Juzaweb\Backend\Models\EmailList;
use Juzaweb\Backend\Models\EmailTemplate;

class Email
{
    protected array $emails;
    protected string $template;
    protected array $params = [];
    protected int $priority = 1;
    protected string $subject;
    protected string $body;
    protected array $attachments = [];

    /**
     * Make email service
     * */
    public static function make(): Email
    {
        return new Email();
    }

    /**
     * Set template for email by template code
     *
     * @param string $templateCode
     * @return $this
     * */
    public function withTemplate(string $templateCode): static
    {
        $this->template = $templateCode;

        return $this;
    }

    protected function renderTemplate($template, $params)
    {
        $templatePath = resource_path("views/templates/$template.blade.php");
        // Check if the template file exists
        if (file_exists($templatePath)) {
            // Render the Blade template
            return view("templates.$template", $params)->render();
        }
    }

    /**
     * Set emails will send
     *
     * @param array|string $emails
     * @return $this
     */
    public function setEmails(array|string $emails): static
    {
        if (is_array($emails)) {
            $this->emails = array_unique($emails);
        } else {
            $this->emails = [$emails];
        }

        return $this;
    }

    /**
     * Attach a file to the email
     *
     * @param string $data The file data (e.g., from `file_get_contents`)
     * @param string $name The name of the file
     * @param string $mime The MIME type of the file
     * @return $this
     */
    public function attach(string $data, string $name, string $mime): static
    {
        $this->attachments[] = [
            'data' => $data,
            'name' => $name,
            'mime' => $mime,
        ];

        return $this;
    }

    public function attachFromStorage(string $path, string $name, string $mime, string $disk = 'local'): static
    {
        $this->attachments[] = [
            'path' => $path,
            'disk' => $disk,
            'name' => $name,
            'mime' => $mime,
        ];

        return $this;
    }

    /**
     * Set params for email
     *
     * @param array $params
     * @return $this
     */
    public function setParams(array $params): static
    {
        $this->params = $params;

        return $this;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function setSubject($subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function setBody($body): static
    {
        $this->body = $body;

        return $this;
    }

    public function send(): bool
    {
        $templateId = $this->validate();
        $data = [];

        if (isset($this->subject)) {
            $data['subject'] = $this->subject;
        }

        if (isset($this->body)) {
            $data['body'] = $this->body;
        }

        if (! empty($this->attachments)) {
            $data['attachments'] = $this->attachments;
        }

        foreach ($this->emails as $email) {
            $emailList = EmailList::create(
                [
                    'email' => $email,
                    'template_id' => $templateId,
                    'template_code' => $this->template ?? "",
                    'params' => $this->params,
                    'priority' => $this->priority,
                    'data' => $data,
                ]
            );
            $method = config('juzaweb.email.method');
            switch ($method) {
                case 'sync':
                    (new SendEmail($emailList))->send();
                    break;
                case 'queue':
                    SendEmailJob::dispatch($emailList);
                    break;
            }
        }

        return true;
    }

    protected function validate(): ?int
    {
        if (empty($this->template)) {
            return null;
        }

        $template = EmailTemplate::where(['code' => $this->template])->first(['id']);
        if (empty($template)) {
            return null;
        }

        return $template->id;
    }
}