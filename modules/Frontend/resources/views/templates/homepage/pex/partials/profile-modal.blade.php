{{--
    نافذة التعريف الموحّدة (Profile Modal) — تُستخدم من قسم المتحدثين وقسم
    الأجندة معاً. الهيكل بالكامل HTML ثابت مُولَّد من بلايد (بلا أي بناء
    DOM عبر JS)، وتُفتح/تُملأ بالاعتماد فقط على بيانات data-* الموجودة
    مسبقاً على كل بطاقة/شخص قابل للنقر (data-name / data-session /
    data-session-label) — بلا أي window.*Data و بلا أي قوائم JS عامة.

    يُدرَج هذا الملف مرة واحدة فقط في home.blade.php بعد كل الأقسام.
--}}
<div class="profile-modal" id="profile-modal" hidden>
    <div class="profile-dialog" role="dialog" aria-modal="true" aria-labelledby="profile-name">
        <button type="button" class="profile-close" id="profile-close" aria-label="{{ __('messages.profile_close') }}">
            <i class=" icon-close icon"></i>
        </button>

        <div class="profile-main">
            <div class="profile-avatar-wrap" id="profile-avatar-wrap"></div>
            <h3 class="profile-name" id="profile-name"></h3>
            <div class="profile-role" id="profile-role"></div>
            <div class="profile-label">{{ __('messages.profile_bio_label') }}</div>
            <p class="profile-bio" id="profile-bio"></p>
            <div class="profile-label">{{ __('messages.profile_participation_label') }}</div>
            <p class="profile-desc" id="profile-desc"></p>
        </div>

        <div class="profile-session" id="profile-session">
            <div class="profile-session-title">{{ __('messages.profile_session_title') }}</div>
            <div class="profile-people-nav">
                <button type="button" class="profile-arrow profile-prev" id="profile-prev"
                    aria-label="{{ __('messages.profile_prev_person') }}">
                    <i class="icon-arrow-right icon"></i>
                </button>
                <div class="profile-people-track" id="profile-track"></div>
                <button type="button" class="profile-arrow profile-next" id="profile-next"
                    aria-label="{{ __('messages.profile_next_person') }}">
                    <i class="icon-arrow-left icon"></i>
                </button>
            </div>
        </div>
    </div>
</div>
