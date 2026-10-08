<?php


namespace Juzaweb\API\Http\Controllers\Admin;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Http\Controllers\ApiController;

class AdminApiController extends ApiController
{
    public function callAction($method, $parameters): \Symfony\Component\HttpFoundation\Response
    {
        do_action(Action::BACKEND_CALL_ACTION, $method, $parameters);

        return parent::callAction($method, $parameters);
    }
}
