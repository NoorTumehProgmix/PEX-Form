@php
    $contactPage = get_page_by_template('contact', ['pages']);
    $contactData = get_contact_data();
@endphp

<section class="contact" id="contact" aria-labelledby="contact-title">
    <div class="container">
        <div class="contact-grid">
            <div>
                <span class="badge">{{ $contactPage?->subtitle }}</span>
                <h2 class="section-title" id="contact-title">{{ $contactPage?->title }}</h2>
                <div class="gold-line" aria-hidden="true"></div>
                <p class="section-sub">{{ strip_editor_tags($contactPage?->content ?? '') }}</p>

                <div class="contact-info reveal">
                    <a class="contact-item" id="contact-email" href="mailto:{{ $contactData['email'] }}">
                        <div class="contact-item-icon" aria-hidden="true">
                            <i class="icon-envelope-regular-full icon icon--lg icon--teal-dark"></i>
                        </div>
                        <div>
                            <div class="contact-item-label">{{ __('messages.email') }}</div>
                            <div class="contact-item-value">{{ $contactData['email'] }}</div>
                        </div>
                    </a>

                    <div class="contact-item">
                        <div class="contact-item-icon" aria-hidden="true">
                            <i class="icon-phone-solid-full icon icon--lg icon--teal-dark"></i>
                        </div>
                        <div>
                            <div class="contact-item-label">{{ __('messages.phone') }}</div>
                            <div class="contact-item-value contact-phones" id="contact-phones">
                                @if ($contactData['phones'])
                                    <a href="tel:{{ $contactData['phones'][0] }}">{{ $contactData['phones'][0] }}</a>
                                @endif
                                @if ($contactData['fax'])
                                    <a href="tel:{{ $contactData['fax'] }}">{{ $contactData['fax'] }}</a>
                                @endif    
                            </div>
                        </div>
                    </div>

                    <a class="contact-item" id="contact-website" href="{{ $contactData['websiteUrl'] }}" target="_blank"
                        rel="noopener noreferrer">
                        <div class="contact-item-icon" aria-hidden="true">
                            <i class="icon-globe-solid-full icon icon--lg icon--teal-dark"></i>
                        </div>
                        <div>
                            <div class="contact-item-label">{{ __('messages.website') }}</div>
                            <div class="contact-item-value">{{ $contactData['website'] }}</div>
                        </div>
                    </a>
                </div>

                <div class="share-buttons reveal">
                    <button class="share-btn linkedin" id="share-linkedin" type="button"
                        aria-label="{{ __('messages.share_linkedin') }}" data-linkedin-link="{{ $contactData['linkedinLink'] }}">
                        <i class="icon-linkedin icon icon-white icon--lg"></i>
                        {{ __('messages.share_linkedin') }}
                    </button>
                    <button class="share-btn whatsapp" id="share-whatsapp" type="button"
                        aria-label="{{ __('messages.share_whatsapp') }}" data-whatsapp-link="{{ $contactData['whatsappLink'] }}">
                        <i class="icon-whatsapp icon icon-white icon--lg"></i>
                        {{ __('messages.share_whatsapp') }}
                    </button>
                </div>
            </div>

            <div class="reveal contact-side">
                <a class="map-card" id="map-card" href="{{ $contactData['mapLink'] }}" target="_blank"
                    rel="noopener noreferrer" aria-label="فتح موقع فندق الميلينيوم بلاستين - رام الله على خرائط جوجل">
                    <div class="map-embed" aria-hidden="true">
                        <iframe title="خريطة موقع فندق الميلينيوم بلاستين - رام الله"
                            src="{{ $contactData['mapLink'] }}" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" tabindex="-1"></iframe>
                    </div>
                    <span class="map-btn">
                        <i class="icon-address icon icon--lg"></i>
                        {{ __('messages.map_description') }}
                    </span>
                </a>
            </div>

        </div>
    </div>
</section>
