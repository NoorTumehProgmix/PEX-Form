<?php

namespace Juzaweb\Backend\Policies;

use Juzaweb\CMS\Abstracts\ResourcePolicy;

class SubscriptionPolicy extends ResourcePolicy
{
    protected string $resourceType = 'subscriptions';
}
