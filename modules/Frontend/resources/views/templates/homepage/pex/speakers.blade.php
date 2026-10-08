@php
    use Illuminate\Database\Eloquent\ModelNotFoundException;

    try {
        $speakersPage = get_page_by_template('speakers', ['pages']);
    } catch (ModelNotFoundException $e) {
        $speakersPage = null;
    }

    // ملف تعريف كامل لكل متحدث/مدير جلسة (مرتّب بالاسم) + أول جلسة أجندة
    // يظهر فيها كل متحدث. منطق البناء موجود في
    // modules/Frontend/Helpers/frontend.php (get_speaker_directory /
    // get_speaker_first_sessions) ليتشارك فيه هذا الملف و agenda.blade.php.
    $speakerDirectory = get_speaker_directory();
    $speakerProfiles = $speakerDirectory['profiles'];
    $speakerFirstSession = get_speaker_first_sessions($speakerDirectory['namesById']);
@endphp
<section class="speakers" id="speakers" aria-labelledby="speakers-title">
    <div class="container">
        <div class="reveal section-header">
            <span class="badge">{!! $speakersPage?->subtitle !!}</span>
            <h2 class="section-title" id="speakers-title">{!! $speakersPage?->title !!}</h2>
            <div class="gold-line" aria-hidden="true"></div>
            <p class="section-sub">{!! $speakersPage?->content !!}</p>
        </div>

        @if (empty($speakerProfiles))
            <div class="alert alert-warning">
                <p class="alert-text">{{ __('messages.speakers_coming_soon') }}</p>
            </div>
        @endif

        <div class="speakers-grid" id="speakers-grid">
            @foreach ($speakerProfiles as $sp)
                @continue($sp['roleType'] === 'moderator')
                @php($session = get_speaker_session_summary($sp['name'], $speakerFirstSession, $speakerProfiles))
                <button type="button" class="speaker-card is-clickable"
                    aria-label="{{ __('messages.view_profile_aria', ['name' => $sp['name'], 'role' => $sp['role']]) }}"
                    data-name="{{ $sp['name'] }}"
                    data-session-label="{{ $session['label'] }}"
                    data-session="{{ json_encode($session['participants'], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT) }}">
                    @include('frontend::templates.homepage.pex.partials.avatar', [
                        'name' => $sp['name'],
                        'role' => $sp['role'],
                        'photo' => $sp['photo'],
                    ])
                    <h3 class="speaker-name">{{ $sp['name'] }}</h3>
                    <div class="speaker-role">{{ $sp['role'] }}</div>
                    @if ($sp['bio'])
                        <p class="speaker-bio">{{ $sp['bio'] }}</p>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</section>
