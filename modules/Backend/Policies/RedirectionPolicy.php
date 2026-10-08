<?php

namespace Juzaweb\Backend\Policies;

use Juzaweb\CMS\Abstracts\ResourcePolicy;

class RedirectionPolicy extends ResourcePolicy
{
    protected string $resourceType = 'redirections';
}
