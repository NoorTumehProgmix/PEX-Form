<?php


namespace Juzaweb\API\Http\Controllers;

use Illuminate\Http\Request;
use Juzaweb\Backend\Http\Resources\UserResource;
use Juzaweb\CMS\Http\Controllers\ApiController;

class UserController extends ApiController
{
    public function profile(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
