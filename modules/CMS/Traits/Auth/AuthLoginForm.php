<?php



namespace Juzaweb\CMS\Traits\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Juzaweb\CMS\Http\Requests\Auth\LoginRequest;
use Juzaweb\CMS\Models\User;
use Juzaweb\Backend\Models\ActionLog;
use Illuminate\Http\Request;
use Juzaweb\CMS\Traits\ResponseMessage;
use Illuminate\Pipeline\Pipeline;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;

trait AuthLoginForm
{
    use ResponseMessage;

    public function index(): View
    {
        do_action('login.index');

        do_action('recaptcha.init');

        $socialites = get_config('socialites', []);

        return view(
            $this->getViewForm(),
            [
                'title' => trans_cms('cms::app.login'),
                'socialites' => $socialites
            ]
        );
    }

    public function login(LoginRequest $request)
    {

        do_action('login.handle', $request);

        $email = $request->email;
        $password = $request->password;
        $remember = $request->filled('remember');

        $user = User::where('email', $email)->first(['status', 'is_admin', 'id']);


        $authenticated = true;
        if (empty($user)) {
            $authenticated = false;
            session()->flash('message', trans_cms('cms::message.login_form.login_failed'));
            session()->flash('status', 'error');
            return redirect()->back();
        }

        $user->should_re_login = 0;
        $user->save();
        if ($user->status != 'active') {
            $authenticated = false;
            if ($user->status == 'verification') {
                session()->flash('message', trans_cms('cms::message.login_form.verification'));
            }

            session()->flash('message', trans_cms('cms::message.login_form.user_is_banned'));
            session()->flash('status', 'error');
            return redirect()->back();
        }

        if ($authenticated) {
            $content = [
                'method' => "POST",
                'table' => "",
                'id' => $user->id,
                'type' => "login",
                'label' => "Logged in",
                'title' => "",
                'path' => "",
            ];
            log_action($content, $user->id);
            return $this->loginPipeline($request)->then(function ($request) {
                return app(LoginResponse::class);
            });
        }

        do_action('login.failed');

        session()->flash('message', trans_cms('cms::message.login_form.login_failed'));
        session()->flash('status', 'error');
        return redirect()->back();
    }

    protected function loginPipeline(LoginRequest $request)
    {
        return (new Pipeline(app()))
            ->send($request)
            ->through(array_filter([
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
                Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ]));
    }
    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            $user = jw_current_user();
            $content = [
                'method' => "POST",
                'table' => "",
                'id' => $user->id,
                'type' => "logout",
                'label' => "Logged out",
                'title' => "",
                'path' => "",
            ];
            log_action($content);


            Auth::logout();
        }


        return redirect()->to(config('juzaweb.admin_prefix'));
    }

    protected function getViewForm(): string
    {
        return 'cms::auth.login';
    }
}
