@php
    use Illuminate\Database\Eloquent\ModelNotFoundException;

    try {
        $agendaPage = get_page_by_template('agenda', ['pages']);
    } catch (ModelNotFoundException $e) {
        $agendaPage = null;
    }

    $speakerDirectory = get_speaker_directory();
    $agendaItems = get_agenda_items($speakerDirectory['namesById'], $speakerDirectory['profiles']);
@endphp
<section class="agenda" id="agenda" aria-labelledby="agenda-title">
    <div class="container">
        <div class="reveal section-header">
            <span class="badge">{!! $agendaPage?->subtitle !!}</span>
            <h2 class="section-title" id="agenda-title">{!! $agendaPage?->title !!}</h2>
            <div class="gold-line" aria-hidden="true"></div>
            <p class="section-sub">{!! $agendaPage?->content !!}</p>
        </div>

        <div class="agenda-daytab reveal">
            <i class="icon-calendar1 icon"></i>
            <span id="agenda-daytab-label">الثلاثاء 15 أيلول 2026</span>
        </div>

        @if (empty($agendaItems))
            <div class="alert alert-warning">
                <p class="alert-text">{{ __('messages.agenda_coming_soon') }}</p>
            </div>
        @endif

        <div class="agenda-list" id="agenda-list" aria-label="{{ __('messages.agenda_list_aria') }}">
            @foreach ($agendaItems as $idx => $item)
                <article class="agenda-card{{ $item['typeKey'] ? ' type-' . $item['typeKey'] : '' }}">
                    <div class="agenda-time">
                        <span class="agenda-hours" dir="ltr">{{ $item['end'] }} – {{ $item['start'] }}</span>
                    </div>
                    <div class="agenda-body">
                        @if ($item['type'])
                            <span class="agenda-type badge-{{ $item['typeKey'] ?: 'plain' }}">{{ $item['type'] }}</span>
                        @endif
                        <h3 class="agenda-title">{{ $item['title'] }}</h3>

                        @if (!empty($item['participants']))
                            <div class="agenda-people">
                                @foreach ($item['participants'] as $person)
                                    <button type="button" class="agenda-person is-clickable"
                                        aria-label="{{ __('messages.view_profile_aria', ['name' => $person['name'], 'role' => $person['role']]) }}"
                                        data-name="{{ $person['name'] }}"
                                        data-session-label="{{ $item['title'] }}"
                                        data-session="{{ json_encode($item['participants'], JSON_UNESCAPED_UNICODE) }}">
                                        @include('frontend::templates.homepage.pex.partials.avatar', [
                                            'name' => $person['name'],
                                            'role' => $person['role'],
                                            'photo' => $person['photo'],
                                            'extraCls' => 'agenda-avatar',
                                        ])
                                        <div class="agenda-person-info">
                                            <div class="agenda-person-name">
                                                {{ $person['name'] }}
                                                @if ($person['name'] === $item['chairName'])
                                                    <span class="chair-tag">{{ __('messages.chair_tag') }}</span>
                                                @endif
                                            </div>
                                            <div class="agenda-person-role">{{ $person['role'] }}</div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if ($item['hasDetails'] && $item['description'])
                            <button type="button" class="agenda-toggle" aria-expanded="false"
                                aria-controls="agenda-desc-{{ $idx }}">
                                <span>{{ __('messages.agenda_details_toggle') }}</span>
                                <i class="icon-arrow-down icon chev"></i>
                            </button>
                            <div class="agenda-desc" id="agenda-desc-{{ $idx }}" hidden>{{ $item['description'] }}</div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
