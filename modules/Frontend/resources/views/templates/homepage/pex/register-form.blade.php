<div class="form-card reveal" id="form-card">
    <form id="reg-form" class="reg-form" novalidate method="POST"
        action="{{ route('forum-registration.store') }}">
        @csrf
        <div class="form-grid">

            <div class="form-group">
                <label for="f-name">الاسم الكامل <span aria-hidden="true" class="req">*</span></label>
                <input type="text" id="f-name" name="name" placeholder="الاسم الثلاثي" required
                    autocomplete="name" maxlength="191" />
                <span class="field-error" data-error-for="f-name" aria-live="polite"></span>
            </div>

            <div class="form-group">
                <label for="f-institution">المؤسسة / الشركة <span aria-hidden="true" class="req">*</span></label>
                <input type="text" id="f-institution" name="institution" placeholder="اسم المؤسسة أو الشركة"
                    required maxlength="191" />
                <span class="field-error" data-error-for="f-institution" aria-live="polite"></span>
            </div>

            <div class="form-group">
                <label for="f-jobtitle">المسمى الوظيفي</label>
                <input type="text" id="f-jobtitle" name="job_title" placeholder="المسمى الوظيفي"
                    maxlength="191" />
            </div>

            <div class="form-group">
                <label for="f-email">البريد الإلكتروني <span aria-hidden="true" class="req">*</span></label>
                <input type="email" id="f-email" name="email" placeholder="email@example.com" required
                    autocomplete="email" inputmode="email" maxlength="191" />
                <span class="field-error" data-error-for="f-email" aria-live="polite"></span>
            </div>

            <div class="form-group">
                <label for="f-phone">رقم الهاتف <span aria-hidden="true" class="req">*</span></label>
                <input type="tel" id="f-phone" name="phone" placeholder="+970 5X XXX XXXX" required autocomplete="tel"
                    inputmode="tel" dir="ltr" class="input-ltr" maxlength="20" />
                <span class="field-error" data-error-for="f-phone" aria-live="polite"></span>
            </div>

            <div class="form-group">
                <label for="f-parttype">نوع المشاركة <span aria-hidden="true" class="req">*</span></label>
                <div class="select-wrap">
                    <select id="f-parttype" name="part_type" required>
                        <option value="" disabled selected>اختر نوع المشاركة</option>
                        <option value="حضور">حضور</option>
                        <option value="متحدث">متحدث</option>
                        <option value="راعٍ">راعٍ</option>
                        <option value="شريك استراتيجي">شريك استراتيجي</option>
                    </select>
                </div>
                <span class="field-error" data-error-for="f-parttype" aria-live="polite"></span>
            </div>

            <div class="form-group form-group--conditional" id="sponsor-type-group" hidden>
                <label for="f-sponsortype">نوع الرعاية <span aria-hidden="true" class="req">*</span></label>
                <div class="select-wrap">
                    <select id="f-sponsortype" name="sponsor_type" disabled>
                        <option value="" disabled selected>اختر نوع الرعاية</option>
                        <option value="راعٍ ماسي">راعٍ ماسي</option>
                        <option value="راعٍ ذهبي">راعٍ ذهبي</option>
                        <option value="راعٍ فضي">راعٍ فضي</option>
                    </select>
                </div>
                <span class="field-error" data-error-for="f-sponsortype" aria-live="polite"></span>
            </div>

        </div>

        <div class="form-alert" id="form-alert" role="alert" hidden></div>

        <div class="form-footer">
            <p class="form-note"><span aria-hidden="true" class="req">*</span> الحقول الإلزامية</p>
            <button type="submit"
                class="btn-submit @if(get_config('captcha')) g-recaptcha @endif"
                id="submit-btn"
                @if(get_config('captcha'))
                    data-sitekey="{{ get_config('google_captcha.site_key') }}"
                    data-callback="onRegisterSubmit"
                    data-action="submit"
                @endif>
                <span class="btn-label">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <path d="M22 2L11 13" />
                        <polygon points="22 2 15 22 11 13 2 9 22 2" />
                    </svg>
                    إرسال التسجيل
                </span>
                <span class="btn-spinner" aria-hidden="true"></span>
            </button>
            @if(get_config('captcha'))
                @error('g-recaptcha-response')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            @endif
        </div>
    </form>

    <div class="form-success" id="form-success" role="status" aria-live="polite">
        <div class="success-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12" />
            </svg>
        </div>
        <div class="success-title">تم استلام تسجيلك بنجاح!</div>
        <p class="success-msg">سيتواصل معك فريق الملتقى قريباً لتأكيد المشاركة.</p>
    </div>
</div>
