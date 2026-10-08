import $ from '../jquery';
import 'jquery-validation';


((
    ($) => {
        const MobileExpression = /^(?:(?:(\+?972|\(\+?972\)|\+?\(972\)|\+?970|\(\+?970\)|\+?\(970\))(?:\s|\.|-)?([1-9]\d?))|(0[23489]{1})|(0[57]{1}[0-9]))(?:\s|\.|-)?([^0\D]{1}\d{2}(?:\s|\.|-)?\d{4})$/;
        const phoneExpression = /^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\/]?)?((?:\(?\d{1,}\)?[\-\.\/]?){0,})(?:[\-\.\/]?(?:#|ext\.?|extension|x)[\-\.\/]?(\d+))?$/;
        const form = $("#validate-form");

        jQuery.validator.addMethod("palestineMobile", function (value, element) {
            return MobileExpression.test(value);
        }, window.validation.wrong_phone);

        jQuery.validator.addMethod("landlineNumber", function (value, element) {
            return phoneExpression.test(value);
        }, window.validation.wrong_phone);

        jQuery.extend(jQuery.validator.messages, {
            required: window.validation.required,
            email: window.validation.email,
            date: window.validation.date,
            dateISO: window.validation.dateISO,
            number: window.validation.number,
            digits: window.validation.digits,
            equalTo: window.validation.equalTo,
            accept: window.validation.accept,
            maxlength: window.validation.maxlength,
            minlength: window.validation.minlength,
            rangelength: window.validation.rangelength,
            range: window.validation.range,
            max: window.validation.max,
            min: window.validation.min
        });

        form.validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 191,
                },
                landline_number: {
                    required: true,
                    landlineNumber: true,
                    minlength: 7,
                    maxlength: 15,
                },
                mobile: {
                    required: true,
                    palestineMobile: true,
                    minlength: 8,
                    maxlength: 20,
                },
                email: {
                    required: true,
                    email: true,
                },
                subject: {
                    required: true,
                    minlength: 2,
                    maxlength: 191,
                },
                message: {
                    required: true,
                    minlength: 10,
                }
            },
            messages: {
                name: {
                    minlength: window.validation.minlength.replace(':attribute', 2),
                    maxlength: window.validation.maxlength.replace(':attribute', 191)
                },
                landline_number: {
                    landlineNumber: window.validation.wrong_phone,
                    minlength: window.validation.minlength.replace(':attribute', 7),
                    maxlength: window.validation.maxlength.replace(':attribute', 15)
                },
                mobile: {
                    palestineMobile: window.validation.wrong_phone,
                    minlength: window.validation.minlength.replace(':attribute', 8),
                    maxlength: window.validation.maxlength.replace(':attribute', 20)
                },
                subject: {
                    minlength: window.validation.minlength.replace(':attribute', 2),
                    maxlength: window.validation.maxlength.replace(':attribute', 191)
                },
                message: {
                    minlength: window.validation.minlength.replace(':attribute', 10),
                    maxlength: window.validation.maxlength.replace(':attribute', 191)
                }
            },
            submitHandler: function (form) {
                grecaptcha.execute();
            },
            errorPlacement: function (error, element) {
                error.appendTo(element.closest('.form-group'));
            }
        });

        let captchaLoaded = false;
        form.on('click', '.g-recaptcha', function (e) {
            if (window.functions.has_captcha && !captchaLoaded) {
                const script = $(`<script src="${window.functions.captcha_url}"></script>`);
                $('head').append(script);
                captchaLoaded = true;
            }
        })

        window.onSubmit = function (token) {
            if (form.valid()) {
                form.unbind('submit').submit();
            }
        }

        $(window).scroll(function (event) {
            if (window.functions.has_captcha && !captchaLoaded) {
                const script = $(`<script src="${window.functions.captcha_url}"></script>`);
                $('head').append(script);
                captchaLoaded = true;
            }
        });

        $(window).on('mousemove click touchstart touchmove', function (e) {
            if (window.functions.has_captcha && !captchaLoaded) {
                const script = $(`<script src="${window.functions.captcha_url}"></script>`);
                $('head').append(script);
                captchaLoaded = true;
            }
        });
    }
)(jQuery));
