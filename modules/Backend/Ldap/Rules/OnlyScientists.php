<?php

namespace Juzaweb\Backend\Ldap\Rules;

use Illuminate\Database\Eloquent\Model as Eloquent;
use LdapRecord\Laravel\Auth\Rule;
use LdapRecord\Models\OpenLDAP\Group;
use LdapRecord\Models\Model as LdapRecord;

class OnlyScientists implements Rule
{
    /**
     * Check if the rule passes validation.
     */
    public function passes(LdapRecord $user, Eloquent $model = null): bool
    {
        $scientists = Group::find('ou=scientists,dc=example,dc=com');

        return $user->groups()->recursive()->exists($scientists);
    }
}
