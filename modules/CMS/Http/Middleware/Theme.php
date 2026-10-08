<?php



namespace Juzaweb\CMS\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use Juzaweb\Backend\Http\Resources\UserResource;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\ThemeLoader as ThemeFacade;

class Theme
{
    public function handle($request, Closure $next)
    {
        View::composer(
            '*',
            function ($view) {
                global $jw_user;
                $user = $jw_user ? (new UserResource($jw_user))->toArray(request()) : null;

                $view->with('user', $user);
                $view->with('is_admin', $user ? $user['is_admin'] : false);
                $view->with('auth', (bool) $user);
                $view->with('guest', !$user);
            }
        );


        $currentTheme = jw_current_theme();
        $themePath = ThemeFacade::getThemePath($currentTheme);

        if (is_dir($themePath)) {
            ThemeFacade::set($currentTheme);
        }


        do_action(Action::FRONTEND_INIT, $request);

        return $next($request);
    }
}
