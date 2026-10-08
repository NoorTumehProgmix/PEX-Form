<?php

namespace Juzaweb\Backend\Policies;

use Juzaweb\CMS\Abstracts\ResourcePolicy;

class LinkPolicy extends ResourcePolicy
{
    protected string $resourceType = 'links';
}
