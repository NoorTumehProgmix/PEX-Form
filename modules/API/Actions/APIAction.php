<?php


namespace Juzaweb\API\Actions;

use Juzaweb\API\Support\Documentation\ApplicationSwaggerDocumentation;
use Juzaweb\API\Support\Documentation\AuthSwaggerDocumentation;
use Juzaweb\API\Support\Documentation\PostTypeAdminSwaggerDocumentation;
use Juzaweb\API\Support\Documentation\PostTypeSwaggerDocumentation;
use Juzaweb\API\Support\Swagger\SwaggerDocument;
use Juzaweb\CMS\Abstracts\Action;

class APIAction extends Action
{
    public function handle()
    {
        $this->addAction(Action::BACKEND_INIT, [$this, 'addAdminMenu']);
        if (config('juzaweb.api.frontend.enable')) {
            $this->addAction(Action::API_DOCUMENT_INIT, [$this, 'addAPIDocumentation'], 1);
            $this->addAction(Action::API_DOCUMENT_INIT, [$this, 'addAdminDocumentation'], 1);
        }
    }

    public function addAPIDocumentation()
    {
        $document = SwaggerDocument::make('frontend');
        $document->setTitle('Frontend', "");
        $document->append(AuthSwaggerDocumentation::class);
        // $document->append(PostTypeSwaggerDocumentation::class);
        $this->hookAction->registerAPIDocument($document);
    }

    public function addAdminDocumentation()
    {
        $apiAdmin = SwaggerDocument::make('admin');
        $apiAdmin->setTitle('Admin', "Welcome to the API documentation for your website. This API provides endpoints to interact with the website's resources. Please note that access to these APIs is restricted to authenticated admin users only.");
        $apiAdmin->setPrefix('admin');
        // $apiAdmin->append(PostTypeAdminSwaggerDocumentation::class);
        $apiAdmin->append(ApplicationSwaggerDocumentation::class);
        // Apply security only to admin API paths
        $apiAdmin->appendSecurity('token', []);
        $this->hookAction->registerAPIDocument($apiAdmin);
    }

    public function addAdminMenu()
    {
        $this->hookAction->registerAdminPage(
            'api.documentation',
            [
                'title' => trans_cms('cms::app.api_documentation'),
                'menu' => [
                    'icon' => 'fa fa-book',
                    'position' => 95,
                ],
            ]
        );
    }
}
