@php
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    try {
        $aboutPage = get_page_by_template('about', ['pages']);
        $aboutStats = get_about_stats($aboutPage);
    } catch (ModelNotFoundException $e) {
        $aboutPage = null;
        $aboutStats = [];
    }
@endphp
<section class="about" id="about" aria-labelledby="about-title">
    <div class="container">
        <div class="about-grid">
            <div class="about-text reveal">
                <span class="badge">{!! $aboutPage?->subtitle !!}</span>
                <h2 class="section-title" id="about-title">{!! $aboutPage?->title !!}</h2>
                <div class="gold-line" aria-hidden="true"></div>
                <p>
                    {!! $aboutPage?->content !!}
                </p>
            </div>
            <div class="about-stats reveal" id="about-stats">
                @foreach ($aboutStats as $stat)
                    <div class="stat-card">
                        <div class="stat-number">{{ $stat['number'] }}</div>
                        <div class="stat-label">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
