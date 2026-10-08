@php
    $timezones = timezone_identifiers_list();
    $sitetimezone = get_config('timezone', 'UTC');
    $dateFormat = get_config('date_format', 'F j, Y');
    $timeFormat = get_config('time_format', 'g:i a');
    $captcha = get_config('captcha');

    $dateFormats = [
        'F j, Y' => now()->format('F j, Y'),
        'Y-m-d' => now()->format('Y-m-d'),
        'm/d/Y' => now()->format('m/d/Y'),
        'd/m/Y' => now()->format('d/m/Y'),
    ];

    $timeFormats = [
        'g:i a' => now()->format('g:i a'),
        'g:i A' => now()->format('g:i A'),
        'H:i' => now()->format('H:i'),
    ];
@endphp
<form action="{{ route('admin.setting.save') }}" method="post" class="form-ajax">
    <input type="hidden" name="form" value="{{ $component }}">

    <div class="card">

        <div class="card-header">
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="btn-group float-right">
                        <button type="submit" class="btn btn-success"> {{ trans_cms('cms::app.save') }} </button>
                        <button type="reset" class="btn btn-default"> {{ trans_cms('cms::app.reset') }} </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>{{ trans_cms('cms::app.general') }}</h5>
                    @php
                        $currentLang = Lang::locale();
                    @endphp
                    {{ Field::text(trans_cms('cms::app.site_title'), "title_$currentLang", [
                        'value' => get_config("title_$currentLang"),
                    ]) }}

                    <div class="form-group">
                        <label class="col-form-label" for="description">{{ trans_cms('cms::app.tagline') }}</label>
                        <textarea class="form-control" name="description_{{ $currentLang }}" id="description" rows="4">{{ get_config("description_$currentLang") }}</textarea>
                        <p class="description">{{ trans_cms('cms::app.site_description_note') }}</p>
                    </div>

                    {{ Field::text(trans_cms('cms::app.sitename'), "sitename_$currentLang", [
                        'value' => get_config("sitename_$currentLang"),
                    ]) }}

                    <div class="form-group tags-only">
                        <label for="meta_keywords" class="form-label">
                            {{ trans_cms('cms::app.keywords') }}
                        </label>
                        <select name="site_keywords_{{ $currentLang }}[]" class="form-control" id="site_keywords"
                            multiple="multiple">
                            @if (get_config("site_keywords_$currentLang"))
                                @foreach (get_config("site_keywords_$currentLang") as $keyword)
                                    <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group colors-select">
                        <label for="theme_colors" class="form-label">
                            {{ trans_cms('cms::app.theme_colors') }}
                        </label>
                        <div class="color-pickers">
                            @for ($i = 0; $i < 25; $i++)
                                <input type="color" value="{{ @get_config('theme_colors')[$i] }}"
                                    name="theme_colors[]" class="form-control color-picker">
                            @endfor
                        </div>
                    </div>

                    {{ Field::text(trans_cms('cms::app.location'), "location_$currentLang", [
                        'value' => get_config("location_$currentLang"),
                    ]) }}
                    {{ Field::text(trans_cms('cms::app.map_link'), 'map_link', [
                        'value' => get_config('map_link'),
                    ]) }}
                    {{ Field::text(trans_cms('cms::app.bo_box'), "bo_box_$currentLang", [
                        'value' => get_config("bo_box_$currentLang"),
                    ]) }}
                    {{ Field::text(trans_cms('cms::app.mail'), 'mail', [
                        'value' => get_config('mail'),
                    ]) }}
                    {{ Field::text(trans_cms('cms::app.phone'), 'phone', [
                        'value' => get_config('phone'),
                    ]) }}
                    {{ Field::text(trans_cms('cms::app.fax'), 'fax', [
                        'value' => get_config('fax'),
                    ]) }}

                    <div class="row">
                        <div class="col-md-6">
                            {{ Field::image(trans_cms('cms::app.logo'), "logo_$currentLang", [
                                'value' => get_config("logo_$currentLang"),
                            ]) }}
                        </div>
                        <div class="col-md-6">
                            {{ Field::image(trans_cms('cms::app.white_logo'), "white_logo_$currentLang", [
                                'value' => get_config("white_logo_$currentLang"),
                            ]) }}
                        </div>
                        <div class="col-md-6">
                            {{ Field::image(trans_cms('cms::app.footer_logo'), "footer_logo_$currentLang", [
                                'value' => get_config("footer_logo_$currentLang"),
                            ]) }}
                        </div>
                        <div class="col-md-6">
                            {{ Field::image(trans_cms('cms::app.pages_pattern'), 'banner', [
                                'value' => get_config('banner'),
                            ]) }}
                        </div>
                        <div class="col-md-6">
                            {{ Field::image(trans_cms('cms::app.og_image_default'), "og_image_default_$currentLang", [
                                'value' => get_config("og_image_default_$currentLang"),
                            ]) }}
                            <label class="col-form-label"><b>OG Image Must be of size 1200 X 630</b> <a
                                    href="{{ get_og_default() }}" download="" class="col-form-label">Download
                                    Sample</a></label>
                        </div>
                        <div class="col-md-6">
                            {{ Field::image(trans_cms('cms::app.thumbnail_default'), "thumbnail_default_$currentLang", [
                                'value' => get_config("thumbnail_default_$currentLang"),
                            ]) }}
                            <a href="{{ get_default_thumbnail() }}" download="" class="col-form-label">Download
                                Sample</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">
                            {{ trans_cms('List pages show at most') }}
                        </label>

                        <input type="number" class="form-control" name="posts_per_page"
                            value="{{ get_config('posts_per_page', 12) }}">
                    </div>

                    <div class="form-group">
                        <label class="col-form-label" for="timezone">{{ trans_cms('cms::app.timezone') }}</label>
                        <select name="timezone" id="timezone" class="form-control select2">
                            @foreach ($timezones as $timezone)
                                <option value="{{ $timezone }}" @if ($sitetimezone == $timezone) selected @endif>
                                    {{ $timezone }}</option>
                            @endforeach
                        </select>
                        <p class="description">{{ trans_cms('cms::app.timezone_description') }}</p>
                        <p class="description">{{ trans_cms('cms::app.current_time') }}
                            {{ now()->format('Y-m-d H:i:s') }}
                        </p>
                    </div>
                    @php
                        $langs = \Juzaweb\Backend\Models\Language::all();
                    @endphp
                    <div class="form-group">
                        <label class="col-form-label" for="language">{{ trans_cms('cms::app.site_language') }}</label>
                        <select name="language" id="language" class="form-control">
                            @foreach ($langs as $lang)
                                <option value="{{ $lang['code'] }}" @selected($lang['code'] == get_config('language'))>{{ $lang['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label" for="date_format">{{ trans_cms('cms::app.date_format') }}</label>
                        <fieldset>
                            @foreach ($dateFormats as $key => $item)
                                <label class="mb-2">
                                    <input type="radio" name="date_format" value="{{ $key }}"
                                        @if ($key == $dateFormat) checked="checked" @endif>
                                    <span
                                        class="date-time-text format-i18n mr-2">{{ $item }}</span><code>{{ $key }}</code>
                                </label><br>
                            @endforeach

                            <label>
                                <input type="radio" name="date_format" id="date_format_custom_radio" value="custom">
                                <span class="date-time-text date-time-custom-text">{{ trans_cms('cms::app.custom') }}:
                                    <span class="screen-reader-text ml-1">
                                        {{ trans_cms('cms::app.general_settings.enter_a_custom_date') }}</span>
                                </span>
                            </label>

                            <label for="date_format_custom"
                                class="screen-reader-text">{{ trans_cms('cms::app.general_settings.custom_date_format') }}
                                :</label>
                            <input type="text" name="date_format_custom" id="date_format_custom"
                                value="{{ get_config('date_format_custom', 'F j, Y') }}" class="form-control w-25">
                            <br>
                            {{--
                    <p><strong>Preview:</strong>
                        <span class="example">September 2, 2021</span><span class="spinner"></span>
                    </p> --}}
                        </fieldset>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label"
                            for="time_format">{{ trans_cms('cms::app.time_format') }}</label>
                        <fieldset>
                            @foreach ($timeFormats as $key => $item)
                                <label>
                                    <input type="radio" name="time_format" value="{{ $key }}"
                                        @if ($key == $timeFormat) checked="checked" @endif />
                                    <span class="date-time-text format-i18n mr-2">{{ $item }}</span>
                                    <code>{{ $key }}</code>
                                </label>
                                <br />
                            @endforeach

                            <label class="screen-reader-text">
                                <input type="radio" name="time_format" value="custom"
                                    @if ($timeFormat == 'custom') checked="checked" @endif />
                                {{ trans_cms('cms::app.general_settings.custom_time_format') }}:
                            </label>

                            <input type="text" name="time_format_custom" id="time_format_custom"
                                value="{{ get_config('time_format_custom', 'g:i a') }}" class="form-control w-25" />

                            <br />

                            {{-- <p>
                    <strong>Preview:</strong>
                    <span class="example">6:47 am</span><span class="spinner"></span>
                </p> --}}

                        </fieldset>
                    </div>

                </div>

                <div class="col-md-4">
                    <h5>{{ trans_cms('cms::app.cache') }}</h5>
                    {{ Field::text(trans_cms('cms::app.cache_duration'), 'cache_duration', [
                        'value' => get_config('cache_duration'),
                    ]) }}


                    <h5>{{ trans_cms('cms::app.analytics') }}</h5>

                    <div class="form-group">
                        <label class="col-form-label" for="fb_app_id">{{ trans_cms('cms::app.fb_app_id') }}</label>
                        <input type="text" name="fb_app_id" class="form-control" id="fb_app_id"
                            value="{{ get_config('fb_app_id') }}" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label class="col-form-label"
                            for="facebook_pixel_id">{{ trans_cms('cms::app.facebook_pixel_id') }}</label>
                        <input type="text" name="facebook_pixel_id" class="form-control" id="facebook_pixel_id"
                            value="{{ get_config('facebook_pixel_id') }}" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label class="col-form-label"
                            for="google_analytics">{{ trans_cms('cms::app.google_analytics_id') }}</label>
                        <input type="text" name="google_analytics" class="form-control" id="google_analytics"
                            value="{{ get_config('google_analytics') }}" autocomplete="off">
                    </div>

                    <h5>{{ trans_cms('cms::app.captcha') }}</h5>
                    <div class="form-group">
                        <label class="col-form-label" for="captcha">{{ trans_cms('cms::app.captcha') }}</label>
                        <select name="captcha" id="captcha" class="form-control">
                            <option value="">{{ trans_cms('cms::app.disabled') }}</option>
                            <option value="google_captcha_v2_invisible"
                                @if ($captcha == 'google_captcha_v2_invisible') selected @endif>
                                {{ trans_cms('cms::app.google_captcha.google_captcha_v2_invisible') }}</option>
                        </select>
                    </div>

                    {{ Field::text(trans_cms('cms::app.google_captcha.site_key'), 'google_captcha[site_key]', [
                        'value' => get_config('google_captcha')['site_key'] ?? '',
                    ]) }}

                    <div class="form-group">
                        <label class="col-form-label">{{ trans_cms('cms::app.google_captcha.secret_key') }}</label>
                        <input type="password" class="form-control" name="google_captcha[secret_key]"
                            value="{{ get_config('google_captcha')['secret_key'] ?? '' }}" />
                    </div>
                    <!-- {{ Field::security(trans_cms('cms::app.google_captcha.secret_key'), 'google_captcha[secret_key]', [
                        'value' => get_config('google_captcha')['secret_key'] ?? '',
                    ]) }} -->

                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-md-6"></div>

                <div class="col-md-6">
                    <div class="btn-group float-right">
                        <button type="submit" class="btn btn-success">
                            {{ trans_cms('cms::app.save') }}
                        </button>

                        <button type="reset" class="btn btn-default">
                            {{ trans_cms('cms::app.reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>
