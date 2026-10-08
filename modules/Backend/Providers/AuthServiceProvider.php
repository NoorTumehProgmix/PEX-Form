<?php

namespace Juzaweb\Backend\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use Progmix\ContactUs\Models\Contact;
use Progmix\ForumRegistration\Models\ForumRegistration;
use Juzaweb\Subscriptions\Models\Subscription;
use Juzaweb\Backend\Policies\SubscriptionPolicy;

use Juzaweb\Backend\Policies\PostPolicy;
use Juzaweb\Backend\Policies\TaxonomyPolicy;
use Juzaweb\Backend\Policies\UserPolicy;
use Juzaweb\Backend\Policies\ContactPolicy;
use Juzaweb\Backend\Policies\ForumRegistrationPolicy;
use Juzaweb\Backend\Policies\FormBuilderPolicy;
use Juzaweb\Backend\Policies\SearchLogPolicy;
use Juzaweb\Backend\Policies\RedirectionPolicy;
use Juzaweb\Backend\Policies\RestrictionPolicy;
use Juzaweb\Backend\Policies\LinkPolicy;
use Progmix\FormBuilder\Models\Form;
use Progmix\SearchLog\Models\SearchLog;
use Progmix\Redirections\Models\Redirection;
use Progmix\Links\Models\Link;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Taxonomy;
use Juzaweb\CMS\Models\User;
use Progmix\Restrictions\Models\Restriction;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Post::class      => PostPolicy::class,
        Taxonomy::class  => TaxonomyPolicy::class,
        User::class      => UserPolicy::class,
        Contact::class   => ContactPolicy::class,
        ForumRegistration::class => ForumRegistrationPolicy::class,
        Subscription::class => SubscriptionPolicy::class,
        SearchLog::class => SearchLogPolicy::class,
        Redirection::class => RedirectionPolicy::class,
        Restriction::class => RestrictionPolicy::class,
        Link::class => LinkPolicy::class,
        Form::class      => FormBuilderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(
            function ($user, $ability) {
                if ($user->isAdmin()) {
                    return true;
                }

                return null;
            }
        );

        ResetPassword::createUrlUsing(
            function ($notifiable, $token) {
                return config('app.frontend_url')
                    . "/password-reset/{$token}?email={$notifiable->getEmailForPasswordReset()}";
            }
        );

        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });

    }
}
