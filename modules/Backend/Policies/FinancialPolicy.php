<?php

namespace Juzaweb\Backend\Policies;

use Juzaweb\CMS\Abstracts\ResourcePolicy;

class FinancialPolicy extends ResourcePolicy
{
    protected string $resourceType = 'financial';
}
