@extends('cms::layouts.backend')

@section('content')
    <div class="row">
        <div class="col-xl-3 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap flex-column align-items-center">
                        <div class="juzaweb__utils__avatar juzaweb__utils__avatar--size64 mb-3">
                            <img src="{{ $jw_user->getAvatar() }}" alt="Mary Stanform">
                        </div>
                        <div class="text-center">
                            <div class="text-dark font-weight-bold font-size-18">{{ $jw_user->name }}</div>

                            <div class="text-uppercase font-size-12 mb-3">
                                {{ $jw_user->is_admin ? 'Administrator' : 'User' }}
                            </div>

                            {{-- <button class="btn btn-primary btn-with-addon">
                                <span class="btn-addon">
                                    <i class="btn-addon-icon fa fa-plus-circle"></i>
                                </span>
                                Request Access
                            </button> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary">
                    <h4 class="card-title text-white">{{ trans_cms('cms::profile.about_me') }}</h4>
                </div>

                <div class="card-body">
                    <strong>
                        <i class="fa fa-user mr-1"></i> {{ trans_cms('cms::profile.full_name') }}
                    </strong>
                    <p class="text-muted">{{ $jw_user->name }}</p>

                    <hr>
                    <strong>
                        <i class="fa fa-envelope mr-1"></i> {{ trans_cms('cms::profile.email') }}
                    </strong>
                    <p class="text-muted">{{ $jw_user->email }}</p>

                    <hr>

                </div>

            </div>
        </div>

        <div class="col-xl-9 col-lg-12">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link" href="{{ route("admin.profile") }}#settings"
                                >{{ trans_cms('cms::app.settings') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route("admin.profile") }}#notifications"
                                >{{ trans_cms('cms::app.notifications') }}</a></li>
                        <li class="nav-item text-capitalize"><a class="nav-link" href="{{ route("admin.profile") }}#change-password"
                                >{{ trans_cms('cms::app.change_password') }}</a></li>
                        <li class="nav-item text-capitalize"><a class="nav-link active" href="#2fa"
                                data-toggle="tab">{{ trans_cms('cms::app.2fa') }}</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">

                        <div class="tab-pane active" id="2fa">
                            <form method="post" action="{{ route('two-factor.enable') }}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <p>{{ trans_cms('cms::app.enable_disable_2fa') }}</p>
                                            @if (session('status') == 'two-factor-authentication-enabled')
                                                <div class="mb-4 font-medium text-sm">
                                                    Please finish configuring two factor authentication below.
                                                </div>
                                            @endif
                                            @if (!$jw_user->two_factor_secret)
                                                <button type="submit"
                                                    class="btn btn-success">{{ trans_cms('cms::app.enable') }}</button>
                                            @else
                                                @method('delete')
                                                <div>{!! $jw_user->twoFactorQrCodeSvg() !!}</div>
                                                <button type="submit"
                                                    class="btn btn-success">{{ trans_cms('cms::app.disable') }}</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
