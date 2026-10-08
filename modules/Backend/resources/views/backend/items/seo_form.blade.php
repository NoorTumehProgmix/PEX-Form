@php
    $ogImage = $data?->json_metas['metas']['meta_og_image'] ? $data->json_metas['metas']['meta_og_image'] :get_og_default();
    $ogImageTwitter = $data?->json_metas['metas']['meta_twitter_image'] ? $data->json_metas['metas']['meta_twitter_image'] :get_og_default();
@endphp
<div class="card mt-3">
    <div class="card-header row">
        <div class="col-md-6">
            <h4 class="card-title">{{ trans_cms('cms::app.custom_seo') }}</h4>
        </div>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="google-tab" data-toggle="tab" href="#google" role="tab"
                   aria-controls="google" aria-selected="true">Google</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="facebook-tab" data-toggle="tab" href="#facebook" role="tab"
                   aria-controls="facebook" aria-selected="false">Facebook</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="twitter-tab" data-toggle="tab" href="#twitter" role="tab"
                   aria-controls="twitter" aria-selected="false">Twitter</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active box-custom-seo" id="google" role="tabpanel"
                 aria-labelledby="google-tab">

                <input type="hidden" id="model_exists" value="{{ @$model->id }}">
                <div class="form-group">
                    <label for="meta_title" class="form-label">
                        {{ trans_cms('cms::app.title') }}
                    </label>
                    <input type="text" name="meta_title" id="meta_title" class="form-control"
                           value="{{ seo_string($data?->json_metas['metas']['meta_title'] ?? '') }}">
                </div>
                <div class="form-group">
                    <label for="meta_heading" class="form-label">
                        H1 Heading
                    </label>
                    <input type="text" name="meta_heading" id="meta_heading" class="form-control"
                           value="{{ seo_string($data?->json_metas['metas']['meta_heading'] ?? '') }}">
                </div>

                <div class="form-group">
                    <label for="meta_author" class="form-label">
                        Author
                    </label>
                    <input type="text" name="meta_author" id="meta_author" class="form-control"
                           value="{{ $data?->json_metas['metas']['meta_author'] ?? '' }}">
                </div>
                @php
                    $meta_canonical = $data?->json_metas['metas']['meta_canonical'];

                @endphp
                <div class="form-group seo-review">
                    <label for="meta_canonical" class="form-label">
                        Canonical
                    </label>
                    <input type="text" name="meta_canonical" id="meta_canonical" class="form-control"
                           value="{{ isset($model->id) ? $model->getLink() : '' }}" disabled readonly>

                </div>

                <label>
                    Should Search Engines show & follow this page?
                    <input type="radio" name="meta_showRobots" value="yes"
                        {{ $data?->json_metas['metas']['meta_showRobots'] == 'yes' ? 'checked' : '' }}> Yes
                    <input type="radio" name="meta_showRobots" value="no"
                        {{ $data?->json_metas['metas']['meta_showRobots'] == 'no' ? 'checked' : '' }}> No
                </label>
                <div class="form-group">
                    <label for="meta_robots" class="form-label">
                        Advanced Meta Robots
                    </label>
                    @php
                        $option = $data?->json_metas['metas']['meta_robots'] ?? '';
                    @endphp
                    <select name="meta_robots" class="form-control" id="meta_robots">
                        <option value="">Select</option>
                        <option value="nosnippet" {{ $option == 'nosnippet' ? 'selected' : '' }}>No Snippet</option>
                        <option value="noarchive" {{ $option == 'noarchive' ? 'selected' : '' }}>No Archive</option>
                        <option value="noimageindex" {{ $option == 'noimageindex' ? 'selected' : '' }}>No Image Index
                        </option>
                    </select>
                </div>
                <div class="form-group tags-only">
                    <label for="meta_keywords" class="form-label">
                        {{ trans_cms('cms::app.keywords') }}
                    </label>

                    <select name="meta_keywords[]" class="form-control" id="meta_keywords" multiple="multiple">
                        @if ($data && $data?->json_metas['metas']['meta_keywords'] != '')

                            @if (is_string($data?->json_metas['metas']['meta_keywords']))
                                @foreach (explode(',', $data?->json_metas['metas']['meta_keywords']) as $keyword)
                                    <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                                @endforeach
                            @else
                                @foreach ($data?->json_metas['metas']['meta_keywords'] as $keyword)
                                    <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                                @endforeach
                            @endif

                        @endif
                    </select>
                </div>

                <div class="form-group tags-only">
                    <label for="meta_title_keywords" class="form-label">
                        {{ trans_cms('cms::app.title_keywords') }}
                    </label>
                    <select name="meta_title_keywords[]" class="form-control" id="meta_title_keywords"
                            multiple="multiple">
                        @if ($data && $data?->json_metas['metas']['meta_title_keywords'] != '')

                            @if (is_string($data?->json_metas['metas']['meta_title_keywords']))
                                @foreach (explode(',', $data?->json_metas['metas']['meta_title_keywords']) as $keyword)
                                    <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                                @endforeach
                            @else
                                @foreach ($data?->json_metas['metas']['meta_title_keywords'] as $keyword)
                                    <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                                @endforeach
                            @endif

                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="meta_description" class="form-label">{{ trans_cms('cms::app.description') }}</label>
                    <textarea data-maxlength="160" name="meta_description" id="meta_description" class="form-control"
                              rows="4">{{ seo_string($data?->json_metas['metas']['meta_description'] ?? $model->content) }}</textarea>
                    <small class="char-count"></small>
                    <small>Maximum 160 characters</small>
                </div>

                <div class="seo-review">
                    <h5>{{ trans_cms('cms::app.preview') }}</h5>
                    <div class="review-title">{{ seo_string($data->meta_title ?? $model->title) }}</div>
                    <div class="review-url">{{ isset($model->id) ? $model->getLink() : '' }}</div>
                    <div
                        class="review-description">{{ seo_string($data?->json_metas['metas']['meta_description'] ?? $model->content) }}
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="facebook" role="tabpanel" aria-labelledby="facebook-tab">

                <div class="form-group mt-3">
                    <label for="meta_og_site_name" class="form-label">
                        OG Site Name
                    </label>
                    <input type="text" name="meta_og_site_name" id="meta_og_site_name" class="form-control"
                           value="{{ $data?->json_metas['metas']['meta_og_site_name'] ?? '' }}">
                </div>

                <div class="form-group">
                    <label for="meta_og_title" class="form-label">
                        OG Title
                    </label>
                    <input type="text" name="meta_og_title" id="meta_og_title" class="form-control"
                           value="{{ $data?->json_metas['metas']['meta_og_title'] ?? '' }}">
                </div>

                <div class="form-group">
                    <label for="meta_og_type" class="form-label">
                        OG Type
                    </label>
                    @php
                        $option = $data?->json_metas['metas']['meta_og_type'] ?? '';
                    @endphp
                    <select name="meta_og_type" class="form-control" id="meta_og_type">
                        <option value="website" {{ $option == 'website' ? 'selected' : '' }}>Website</option>
                        <option value="article" {{ $option == 'article' ? 'selected' : '' }}>Article</option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="meta_og_url" class="form-label">
                        OG URL
                    </label>
                    <input type="text" name="meta_og_url" id="meta_og_url" class="form-control"
                           value="{{ isset($data->path) ? env('APP_URL') . $data->path : '' }}" disabled readonly>
                </div>

                {{ Field::image($model, 'meta_og_image', ['value' => $ogImage]) }}

                <div class="form-group">
                    <label for="meta_og_description" class="form-label">Meta OG Description</label>
                    <textarea data-maxlength="200" name="meta_og_description" id="meta_og_description"
                              class="form-control"
                              rows="4">{{ seo_string($data?->json_metas['metas']['meta_og_description'] ?? $model->content) }}</textarea>
                    <small class="char-count"></small>
                    <small>Maximum 200 characters</small>
                </div>

                <div class="facebook-preview">
                    <div class="facebook-img">
                        <img name="image_preview" src="{{ upload_url($ogImage) }}" alt=""/>
                    </div>
                    <div class="info">
                        <div class="info-title meta_og_url ">{{ isset($model->id) ? $model->getLink() : '' }}</div>
                        <h2 class="info-subtitle meta_og_title">
                            {{ seo_string($data->meta_og_title ?? $model->title) }}</h2>
                        <div class="info-title meta_og_description">
                            {{ seo_string($data?->json_metas['metas']['meta_og_description'] ?? $model->content) }}
                        </div>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="twitter" role="tabpanel" aria-labelledby="twitter-tab">

                <div class="form-group">
                    <label for="meta_twitter_card" class="form-label">
                        Twitter card
                    </label>
                    @php
                        $option = $data?->json_metas['metas']['meta_twitter_card'] ?? '';
                    @endphp
                    <select name="meta_twitter_card" class="form-control" id="meta_twitter_card">
                        <option value="summary" {{ $option == 'summary' ? 'summary' : '' }}>Summary Card</option>
                        <option value="summary_large_image" {{ $option == 'summary_large_image' ? 'selected' : '' }}>
                            Summary Card with Large Image
                        </option>
                        <option value="app" {{ $option == 'app' ? 'selected' : '' }}>App Card</option>
                        <option value="player" {{ $option == 'player' ? 'selected' : '' }}>Player Card</option>
                        <option value="product" {{ $option == 'product' ? 'selected' : '' }}>Product Card</option>
                        <option value="gallery" {{ $option == 'gallery' ? 'selected' : '' }}>Gallery Card</option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="meta_twitter_title" class="form-label">
                        Twitter Title
                    </label>
                    <input type="text" name="meta_twitter_title" id="meta_twitter_title" class="form-control"
                           value="{{ $data?->json_metas['metas']['meta_twitter_title'] ?? '' }}">
                </div>

                <div class="form-group">
                    <label for="meta_twitter_image_alt" class="form-label">
                        Twitter Image Alt
                    </label>
                    <input type="text" name="meta_twitter_image_alt" id="meta_twitter_image_alt"
                           class="form-control"
                           value="{{ $data?->json_metas['metas']['meta_twitter_image_alt'] ?? '' }}">
                </div>

                {{ Field::image($model, 'meta_twitter_image', ['value' => $ogImageTwitter]) }}

                <div class="form-group">
                    <label for="meta_twitter_description" class="form-label">Meta OG Description</label>
                    <textarea data-maxlength="200" name="meta_twitter_description" id="meta_twitter_description"
                              class="form-control"
                              rows="4">{{ seo_string($data?->json_metas['metas']['meta_twitter_description'] ?? $model->content) }}</textarea>
                    <small class="char-count"></small>
                    <small>Maximum 200 characters</small>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('textarea').on("input", function () {
            var maxlength = $(this).data("maxlength");
            var currentLength = $(this).val().length;
            $(this).closest(".form-group").find(".char-count").text(currentLength + "/" + maxlength);
        });
    });
</script>
{{-- <script>
    $(document).ready(function() {
        // Listen for changes in the slug input field
        $('[name="slug"]').on('change', function() {
            var slugValue = $('[name="slug"]').val();
            var metaCanonicalInput = $('#meta_canonical');
            var metaOgUrlInput = $('#meta_og_url');
            metaCanonicalValue = '{{ $data?->json_metas['metas']['meta_canonical'] }}' + '/' +
                slugValue;
            metaCanonicalInput.val(slugValue ? metaCanonicalValue :
                '{{ $data?->json_metas['metas']['meta_canonical'] }}');
            metaOgUrlInput.val(metaCanonicalInput.val());
            $(".meta_og_url").text(metaCanonicalInput.val());
        });
    })
</script> --}}
