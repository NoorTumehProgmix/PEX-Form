<?php


namespace Juzaweb\Backend\Events;

use Juzaweb\CMS\Models\User;

class RegisterSuccessful
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
