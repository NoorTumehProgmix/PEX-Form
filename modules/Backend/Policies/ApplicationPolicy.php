<?php

namespace Juzaweb\Backend\Policies;

use Juzaweb\CMS\Abstracts\ResourcePolicy;

class ApplicationPolicy extends ResourcePolicy
{
    protected string $resourceType = 'applications';
}
