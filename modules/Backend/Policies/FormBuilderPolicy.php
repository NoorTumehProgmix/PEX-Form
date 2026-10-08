<?php

namespace Juzaweb\Backend\Policies;

use Juzaweb\CMS\Abstracts\ResourcePolicy;

class FormBuilderPolicy extends ResourcePolicy
{
    protected string $resourceType = 'formBuilder';
}
