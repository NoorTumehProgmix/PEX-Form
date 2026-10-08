<?php

namespace Juzaweb\Backend\Policies;

use Juzaweb\CMS\Abstracts\ResourcePolicy;

class RestrictionPolicy extends ResourcePolicy
{
    protected string $resourceType = 'restrictions';
}
