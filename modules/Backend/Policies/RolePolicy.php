<?php


namespace Juzaweb\Backend\Policies;

use Juzaweb\CMS\Abstracts\ResourcePolicy;

class RolePolicy extends ResourcePolicy
{
    protected string $resourceType = 'roles';
}
