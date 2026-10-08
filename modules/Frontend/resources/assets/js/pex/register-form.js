import $ from "../jquery.js";
import { PEX_CONFIG as CFG } from "./config.js";
import { PEX_DATA as DATA } from "./data.js";

var MAX_FIELD_LEN = 191;
var PHONE_MIN_LEN = 8;
var PHONE_MAX_LEN = 20;

function validationMessages() {
  return window.validationMessages || {};
}

function maxLenMsg(limit) {
  var m = validationMessages();
  return m.maxlength
    ? m.maxlength.replace(':attribute', limit)
    : 'الحد الأقصى المسموح به هو ' + limit + ' حرف';
}

function minLenMsg(limit) {
  var m = validationMessages();
  return m.minlength
    ? m.minlength.replace(':attribute', limit)
    : 'الحد الأدنى المطلوب هو ' + limit + ' حرف';
}

function requiredMsg() {
  return validationMessages().required || 'هذا الحقل مطلوب.';
}

function emailMsg() {
  return validationMessages().email || 'صيغة البريد الإلكتروني غير صحيحة';
}

function phoneMsg() {
  return validationMessages().wrong_phone || 'صيغة رقم الهاتف غير صحيحة';
}

function uniqueEmailMsg() {
  return validationMessages().unique_email || 'عنوان البريد الإلكتروني هذا مسجل بالفعل.';
}

export function initRegisterForm() {
  var $regForm = $('#reg-form');
  if (!$regForm.length) return;

  var $formSuccess = $('#form-success');
  var $formAlert = $('#form-alert');
  var $submitBtn = $('#submit-btn');
  var $partTypeSelect = $('#f-parttype');
  var $sponsorTypeGroup = $('#sponsor-type-group');
  var $sponsorTypeSelect = $('#f-sponsortype');
  var c = DATA.contact || {};

  if (!$partTypeSelect.length || !$sponsorTypeGroup.length || !$sponsorTypeSelect.length || !$submitBtn.length) return;

  var palestinePhoneRe = /^(?:\+|00)?(?:970|972|0)?[\s\-]?5[69][\s\-]?\d{3}[\s\-]?\d{4}$/;
  var captchaLoaded = false;
  var captchaAction = 'submit';

  function getCaptchaSiteKey() {
    return (window.functions && window.functions.captcha_site_key) || $submitBtn.attr('data-sitekey') || '';
  }

  function loadCaptchaScript() {
    if (!window.functions || !window.functions.has_captcha || captchaLoaded) return;
    var siteKey = getCaptchaSiteKey();
    var src = siteKey
      ? window.functions.captcha_url + '?render=' + encodeURIComponent(siteKey)
      : window.functions.captcha_url;
    $('<script>').attr({ src: src, async: true, defer: true }).appendTo('head');
    captchaLoaded = true;
  }

  function primeCaptcha() {
    if (window.functions && window.functions.has_captcha) loadCaptchaScript();
  }

  ['scroll', 'mousemove', 'click', 'touchstart', 'touchmove'].forEach(function (evt) {
    $(window).one(evt, primeCaptcha);
  });
  $regForm.one('focusin', primeCaptcha);

  if ($submitBtn.hasClass('g-recaptcha')) {
    $submitBtn.on('click', function () {
      loadCaptchaScript();
    });
  }

  function setError($field, msg) {
    var $group = $field.closest('.form-group');
    if ($group.length) $group.addClass('has-error');
    var $span = $('[data-error-for="' + $field.attr('id') + '"]');
    if ($span.length) $span.text(msg);
    $field.attr('aria-invalid', 'true');
  }

  function clearError($field) {
    var $group = $field.closest('.form-group');
    if ($group.length) $group.removeClass('has-error');
    var $span = $('[data-error-for="' + $field.attr('id') + '"]');
    if ($span.length) $span.text('');
    $field.removeAttr('aria-invalid');
  }

  function isValidEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }

  function isValidPhone(v) {
    if (!v) return true;
    return palestinePhoneRe.test(v);
  }

  function validateMaxLength($field, ok, firstBad) {
    if ($field.val().trim().length > MAX_FIELD_LEN) {
      setError($field, maxLenMsg(MAX_FIELD_LEN));
      return { ok: false, firstBad: firstBad || $field };
    }
    clearError($field);
    return { ok: ok, firstBad: firstBad };
  }

  function toggleSponsorTypeField() {
    var isSponsor = $partTypeSelect.val() === 'راعٍ';
    if (isSponsor) {
      $sponsorTypeGroup.prop('hidden', false);
      requestAnimationFrame(function () { $sponsorTypeGroup.addClass('is-visible'); });
      $sponsorTypeSelect.prop('disabled', false);
      $sponsorTypeSelect.prop('required', true);
    } else {
      $sponsorTypeGroup.removeClass('is-visible');
      $sponsorTypeSelect.prop('required', false);
      $sponsorTypeSelect.prop('disabled', true);
      $sponsorTypeSelect.val('');
      clearError($sponsorTypeSelect);
      setTimeout(function () {
        if ($partTypeSelect.val() !== 'راعٍ') $sponsorTypeGroup.prop('hidden', true);
      }, 400);
    }
  }

  $partTypeSelect.on('change', toggleSponsorTypeField);

  function validate() {
    var ok = true, firstBad = null;
    var $nameField = $('#f-name');
    var $institutionField = $('#f-institution');
    var $jobTitleField = $('#f-jobtitle');

    if (!$nameField.val().trim()) {
      setError($nameField, requiredMsg()); ok = false; firstBad = firstBad || $nameField;
    } else {
      var nameCheck = validateMaxLength($nameField, ok, firstBad);
      ok = nameCheck.ok; firstBad = nameCheck.firstBad;
    }

    if (!$institutionField.val().trim()) {
      setError($institutionField, requiredMsg()); ok = false; firstBad = firstBad || $institutionField;
    } else {
      var instCheck = validateMaxLength($institutionField, ok, firstBad);
      ok = instCheck.ok; firstBad = instCheck.firstBad;
    }

    if ($jobTitleField.val().trim()) {
      var jobCheck = validateMaxLength($jobTitleField, ok, firstBad);
      ok = jobCheck.ok; firstBad = jobCheck.firstBad;
    } else {
      clearError($jobTitleField);
    }

    var $email = $('#f-email');
    if (!$email.val().trim()) {
      setError($email, requiredMsg()); ok = false; firstBad = firstBad || $email;
    } else if (!isValidEmail($email.val().trim())) {
      setError($email, emailMsg()); ok = false; firstBad = firstBad || $email;
    } else if ($email.val().trim().length > MAX_FIELD_LEN) {
      setError($email, maxLenMsg(MAX_FIELD_LEN)); ok = false; firstBad = firstBad || $email;
    } else {
      clearError($email);
    }

    var $phone = $('#f-phone');
    var phoneVal = $phone.val().trim();
    if (!phoneVal) {
      setError($phone, requiredMsg()); ok = false; firstBad = firstBad || $phone;
    } else if (phoneVal.length < PHONE_MIN_LEN) {
      setError($phone, minLenMsg(PHONE_MIN_LEN)); ok = false; firstBad = firstBad || $phone;
    } else if (phoneVal.length > PHONE_MAX_LEN) {
      setError($phone, maxLenMsg(PHONE_MAX_LEN)); ok = false; firstBad = firstBad || $phone;
    } else if (!isValidPhone(phoneVal)) {
      setError($phone, phoneMsg()); ok = false; firstBad = firstBad || $phone;
    } else {
      clearError($phone);
    }

    if (!$partTypeSelect.val()) {
      setError($partTypeSelect, requiredMsg()); ok = false; firstBad = firstBad || $partTypeSelect;
    } else {
      clearError($partTypeSelect);
    }

    if ($partTypeSelect.val() === 'راعٍ' && !$sponsorTypeSelect.val()) {
      setError($sponsorTypeSelect, requiredMsg()); ok = false; firstBad = firstBad || $sponsorTypeSelect;
    } else {
      clearError($sponsorTypeSelect);
    }

    if (firstBad) firstBad.get(0).focus({ preventScroll: false });
    return ok;
  }

  function showAlert(msg) { $formAlert.text(msg); $formAlert.prop('hidden', false); }
  function hideAlert() { $formAlert.prop('hidden', true); }

  $regForm.find('input, select').on('input change', function () {
    clearError($(this)); hideAlert();
  });

  var $phoneField = $('#f-phone');
  if ($phoneField.length) {
    $phoneField.on('input', function () {
      this.value = this.value.replace(/[^\d\+\-\s]/g, '');
    });
  }

  function collectPayload() {
    var payload = {
      name: $('#f-name').val().trim(),
      institution: $('#f-institution').val().trim(),
      job_title: $('#f-jobtitle').val().trim(),
      email: $('#f-email').val().trim(),
      phone: $('#f-phone').val().trim(),
      part_type: $partTypeSelect.val()
    };
    if ($partTypeSelect.val() === 'راعٍ') payload.sponsor_type = $sponsorTypeSelect.val();
    return payload;
  }

  function getCsrfToken() {
    var $input = $('#reg-form input[name="_token"]');
    if ($input.length && $input.val()) return $input.val();
    var $meta = $('meta[name="csrf-token"]');
    return $meta.length ? $meta.attr('content') : '';
  }

  function sendRegistration(payload, captchaToken) {
    var endpoint = $regForm.attr('action') || CFG.FORM_ENDPOINT;

    if (!endpoint) {
      console.warn('[PEX] Registration endpoint is not configured.');
      return Promise.reject(new Error('NOT_CONFIGURED'));
    }

    if (CFG.FORM_MODE === 'google' && CFG.FORM_ENDPOINT) {
      var fd = new FormData();
      var map = CFG.GOOGLE_FIELD_MAP || {};
      Object.keys(payload).forEach(function (k) {
        if (map[k] && payload[k]) fd.append(map[k], payload[k]);
      });
      // ملاحظة: نبقي fetch هنا عمداً — jQuery.ajax يعتمد XMLHttpRequest الذي لا
      // يدعم mode:'no-cors' (طلب "أعمى" بلا قراءة استجابة) اللازم لإرسال بيانات
      // لخدمة خارجية مثل Google Forms لا ترسل رؤوس CORS. تحويله لـ $.ajax كان
      // سيكسر هذا المسار بصمت عند تفعيل FORM_MODE='google'.
      return fetch(CFG.FORM_ENDPOINT, { method: 'POST', mode: 'no-cors', body: fd });
    }

    var body = new FormData();
    Object.keys(payload).forEach(function (k) {
      if (payload[k]) body.append(k, payload[k]);
    });
    if (captchaToken) body.append('g-recaptcha-response', captchaToken);

    var csrf = getCsrfToken();
    if (csrf) body.append('_token', csrf);

    return new Promise(function (resolve, reject) {
      $.ajax({
        url: endpoint,
        method: 'POST',
        data: body,
        processData: false,
        contentType: false,
        dataType: 'json',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf
        }
      }).done(function (data) {
        resolve(data);
      }).fail(function (jqXHR) {
        var data = jqXHR.responseJSON || {};
        var msg = data.message || ('HTTP_' + jqXHR.status);
        if (data.errors) {
          var firstKey = Object.keys(data.errors)[0];
          if (firstKey && data.errors[firstKey][0]) msg = data.errors[firstKey][0];
        }
        var err = new Error(msg);
        err.status = jqXHR.status;
        err.errors = data.errors || {};
        reject(err);
      });
    });
  }

  function setLoading(on) {
    $submitBtn.toggleClass('is-loading', on);
    $submitBtn.prop('disabled', on);
  }

  function showSuccess() {
    $regForm.hide();
    $formSuccess.addClass('show');
    if ($formSuccess.length && typeof $formSuccess.get(0).focus === 'function') {
      $formSuccess.get(0).focus();
    }
  }

  function submitRegistration(captchaToken) {
    setLoading(true);
    sendRegistration(collectPayload(), captchaToken)
      .then(function () {
        setLoading(false);
        showSuccess();
      })
      .catch(function (err) {
        setLoading(false);
        if (err && err.message === 'NOT_CONFIGURED') {
          showAlert('نظام التسجيل غير متاح حالياً. يرجى المحاولة لاحقاً أو التواصل عبر البريد ' + (c.email || 'pex@pex.ps') + '.');
        } else if (err && err.status === 422) {
          var msg = err.message || 'يرجى مراجعة الحقول المدخلة والمحاولة مرة أخرى.';
          if (err.errors && err.errors.email) {
            setError($('#f-email'), err.errors.email[0] || uniqueEmailMsg());
          }
          showAlert(msg);
        } else if (err && (err.status === 419 || (err.message && err.message.indexOf('CSRF') !== -1))) {
          showAlert('انتهت صلاحية الجلسة. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
        } else if (err && err.status === 429) {
          showAlert('تم تجاوز عدد المحاولات المسموح به. يرجى الانتظار دقيقة ثم المحاولة مرة أخرى.');
        } else {
          showAlert('تعذّر إرسال التسجيل. تحقق من اتصال الإنترنت وحاول مرة أخرى، أو تواصل معنا عبر البريد ' + (c.email || 'pex@pex.ps') + '.');
        }
      });
  }

  window.onRegisterSubmit = function (token) {
    if (!validate()) return;
    submitRegistration(token);
  };

  $regForm.on('submit', function (e) {
    e.preventDefault();
    hideAlert();
    if (!validate()) return;

    if (window.functions && window.functions.has_captcha) {
      loadCaptchaScript();
      var attempts = 0;
      var tryExecute = function () {
        var siteKey = getCaptchaSiteKey();
        if (window.grecaptcha && window.grecaptcha.execute && siteKey) {
          window.grecaptcha.ready(function () {
            window.grecaptcha.execute(siteKey, { action: captchaAction })
              .then(function (token) {
                window.onRegisterSubmit(token);
              })
              .catch(function () {
                showAlert(validationMessages().recaptcha || 'تعذّر تحميل التحقق الأمني. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
              });
          });
          return;
        }
        if (++attempts < 30) {
          setTimeout(tryExecute, 100);
          return;
        }
        showAlert(validationMessages().recaptcha || 'تعذّر تحميل التحقق الأمني. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
      };
      tryExecute();
      return;
    }

    submitRegistration(null);
  });
}

export function initScrollReveal() {
  var $revealEls = $('.reveal');
  if ('IntersectionObserver' in window) {
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { $(entry.target).addClass('visible'); obs.unobserve(entry.target); }
      });
    }, { threshold: 0.12 });
    $revealEls.each(function () { obs.observe(this); });
  } else {
    $revealEls.addClass('visible');
  }
}
