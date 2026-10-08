<?php

namespace Juzaweb\Backend\Policies;

use Juzaweb\CMS\Abstracts\ResourcePolicy;

class ForumRegistrationPolicy extends ResourcePolicy
{
    protected string $resourceType = 'forum_registration';
}
