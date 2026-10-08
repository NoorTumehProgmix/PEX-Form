<?php


namespace Juzaweb\API\Http\Controllers\Documentation;

use Illuminate\Http\JsonResponse;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Contracts\HookActionContract as HookAction;
use Juzaweb\CMS\Http\Controllers\BackendController;

class SwaggerDocumentController extends BackendController
{
    public function __construct(protected HookAction $hookAction)
    {
        do_action(Action::API_DOCUMENT_INIT);
    }

    public function index(string $document): JsonResponse
    {
        global $jw_user;
        if (!$jw_user->can("api.$document")) {
            abort(403);
        }

        $documentation = $this->hookAction->getAPIDocuments($document);

        if (empty($documentation)) {
            abort(404);
        }

        return response()->json(
            $documentation,
            200,
            [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
