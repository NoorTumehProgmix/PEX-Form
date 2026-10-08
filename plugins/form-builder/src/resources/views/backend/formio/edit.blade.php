@extends('formBuilder::layouts.form-builder')

@section('form-builder-content')
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            <div class="btn-group">
                <button id="submitFormBtn" class="btn btn-success px-5">
                    <i class="fa fa-save"></i> {{ trans_cms('cms::app.save') }}
                </button>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="alert alert-danger jw-message d-none text-cebter mt-1" id="errors">
            <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">
                <span aria-hidden="true">×</span>
            </button>
            <i class="fa fa-times"></i>
            {{ __('formBuilder::content.form_error_message') }}
        </div>
        <div class="col-md-9">
            <div class="row mb-2">
                <div class="col-md-6">
                    <label>{{ trans_cms('cms::app.name') }}</label>
                    <input id="formNameInput" name="form_name" value='{{ $form->name }}' class="form-control" required />
                    <br>
                    {{ Field::checkbox(trans_cms('cms::app.is_submittable'), 'submittable', [
                        'value' => 1,
                        'checked' => $form->submittable ? true :false,
                    ]) }}
                    <div class="form-group" id="submitted-destinations">
                        <label class="col-form-label">{{ trans_cms('cms::app.submit_destinations') }}</label>
                        {{ Field::checkbox(trans_cms('cms::app.database'), 'database', [
                            'value' => 1,
                            'checked' => $form?->is_database_submittable ? true : false,
                        ]) }}

                        {{ Field::checkbox(trans_cms('cms::app.mail'), 'mail', [
                            'value' => 1,
                            'checked' => $form?->destinations == null ? false : true,
                        ]) }}
                        <div id='form-mails'>
                            <div id="inputContainer">
                                @if ($form?->destinations != null)
                                    @foreach (json_decode($form->destinations) as $mail)
                                        <div class="inputGroup d-flex mt-2">
                                            <input type="text" class="form-control" name="mails[]"
                                                value="{{ $mail }}">
                                            <button type="button" class="removeInput btn btn-danger remove-item">
                                                {{ trans_cms('cms::app.remove') }}</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="inputGroup d-flex">
                                        <input type="text" class="form-control" name="mails[]">
                                        <button type="button" class="removeInput btn btn-danger remove-item">
                                            {{ trans_cms('cms::app.remove') }}</button>
                                    </div>
                                @endif
                                @error('mails.*')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="button" class="btn btn-primary add-key-value mt-2" id="addInput">
                                {{ trans_cms('cms::app.add_mail') }}</button>
                            @error('mails')
                                <span>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="formio"></div>

    {{ Field::textarea(trans_cms('formBuilder::content.js_editor'), 'side_code', [
        'value' => json_decode($form->side_code),
    ]) }}
    <template id="image-view-prefix">
        <div class="form-image-form-builder-prefix text-center col-1 d-flex align-items-center justify-content-center">
            <a href="javascript:void(0)" class="image-clear">
                <span class="tox-icon tox-tbtn__icon-wrap"><svg width="24" height="24" focusable="false">
                        <path
                            d="M19 4a2 2 0 012 2v12a2 2 0 01-2 2h-4v-2h4V8H5v10h4v2H5a2 2 0 01-2-2V6c0-1.1.9-2 2-2h14zm-8 9.4l-2.3 2.3a1 1 0 11-1.4-1.4l4-4a1 1 0 011.4 0l4 4a1 1 0 01-1.4 1.4L13 13.4V20a1 1 0 01-2 0v-6.6z"
                            fill-rule="nonzero"></path>
                    </svg></span>
            </a>
        </div>
    </template>

    <template id="image-view-suffix">
        <div class="form-image-form-builder-suffix text-center col-1 d-flex align-items-center justify-content-center">
            <a href="javascript:void(0)" class="image-clear">
                <span class="tox-icon tox-tbtn__icon-wrap"><svg width="24" height="24" focusable="false">
                        <path
                            d="M19 4a2 2 0 012 2v12a2 2 0 01-2 2h-4v-2h4V8H5v10h4v2H5a2 2 0 01-2-2V6c0-1.1.9-2 2-2h14zm-8 9.4l-2.3 2.3a1 1 0 11-1.4-1.4l4-4a1 1 0 011.4 0l4 4a1 1 0 01-1.4 1.4L13 13.4V20a1 1 0 01-2 0v-6.6z"
                            fill-rule="nonzero"></path>
                    </svg></span>
            </a>
        </div>
    </template>

    <template id="repeater-template">
        <div class="inputGroup d-flex mt-2">
            <input type="text" class="form-control" name="mails[]">
            <button type="button"
                class="removeInput btn btn-danger remove-item">{{ trans_cms('cms::app.remove') }}</button>
        </div>
    </template>

    <script>
        $(document).ready(function() {

            $('[name="submittable"]').change(function() {
                if (!$(this).prop('checked')) {
                    $('#submitted-destinations').hide();
                } else {
                    $('#submitted-destinations').show();
                }
            });

            $('#addInput').click(function() {
                var template = document.getElementById('repeater-template').content.cloneNode(true);
                $('#inputContainer').append(template);
            });

            $(document).on('click', '.removeInput', function() {
                $(this).closest('.inputGroup').remove();
            });
        });


        if ($('input[name="mail"]').prop('checked')) {
            $('#form-mails').show();
        } else {
            $('#form-mails').hide();
        }

        $('input[name="mail"]').change(function() {
            if ($(this).prop('checked')) {
                $('#form-mails').show();
            } else {
                $('#form-mails').hide();
            }
        });
    </script>
    <script>
        function makeKeyFieldReadOnly() {
            var inputElement = document.querySelector(`input[name="data[key]"]`);
            if (inputElement) {
                inputElement.setAttribute('readonly', 'readonly');
            }
        }

        function addPrefixImage(component) {
            var template = $('#image-view-prefix').html();
            var inputElement = $('input[name="data[prefix]"]');
            inputElement.addClass('col-11');
            inputElement.closest('[ref="element"]').addClass('d-flex');
            if (inputElement.length && !inputElement.next().hasClass('form-image-form-builder-prefix')) {
                inputElement.after(template);
            }

            $('body').on('click', '.form-image-form-builder-prefix', function() {
                let item = $(this);
                let targetInput = item.find('.input-path');
                let targetPreview = item.find('.dropify-render');
                let targetName = item.find('.dropify-filename-inner');
                let prefix = juzaweb.adminPrefix + '/file-manager';
                var type = 'image';

                juzawebFileManager({
                    type: type,
                    prefix: prefix
                }, function(files) {
                    let file = files[0];
                    var inputElement1 = $('input[name="data[prefix]"]');
                    inputElement1.val('<img src="' + file.url + '" alt="' + file.name + '">');
                    inputElement1.trigger('change');
                    component.prefix = '<img src="' + file.url + '" alt="' + file.name + '">';

                });
            });

        }

        function addSuffixImage(component) {
            var template = $('#image-view-suffix').html();
            var inputElement = $('input[name="data[suffix]"]');
            inputElement.addClass('col-11');
            inputElement.closest('[ref="element"]').addClass('d-flex');
            if (inputElement.length && !inputElement.next().hasClass('form-image-form-builder-suffix')) {
                inputElement.after(template);
            }
            $('body').on('click', '.form-image-form-builder-suffix', function() {
                let item = $(this);
                let targetInput = item.find('.input-path');
                let targetPreview = item.find('.dropify-render');
                let targetName = item.find('.dropify-filename-inner');
                let prefix = juzaweb.adminPrefix + '/file-manager';
                var type = 'image';

                juzawebFileManager({
                    type: type,
                    prefix: prefix
                }, function(files) {
                    let file = files[0];
                    console.log(file);
                    var inputElement1 = $('input[name="data[suffix]"]');
                    inputElement1.val('<img src="' + file.url + '" alt="' + file.name + '">');
                    inputElement1.trigger('change');
                    component.suffix = '<img src="' + file.url + '" alt="' + file.name + '">';
                });
            });

        }

        function changeLabelAndPlaceholderTooltip(inputName, newTooltipText) {
            var inputElement = $('input[name="' + inputName + '"]');

            if (inputElement.length) {
                var closestLabel = inputElement.closest('.form-group').find('label[ref="label"]');

                if (closestLabel.length) {
                    var iconElement = closestLabel.find('i');

                    if (iconElement.length) {
                        iconElement.attr('data-tooltip', newTooltipText);

                        iconElement.hover(function() {
                            setTimeout(() => {
                                $('.tippy-content').text(newTooltipText);
                            }, 100);
                        });
                    }
                }
            }
        }

        window.addEventListener('load', function() {
            var formDefinition = {!! $form->form_definition !!};

            window.builder1 = new Formio.builder(document.getElementById('formio'), {
                components: formDefinition
            }).then(function(builder) {
                builder.on('addComponent', function(component) {
                    makeKeyFieldReadOnly();
                    addPrefixImage(component);
                    addSuffixImage(component);
                    changeLabelAndPlaceholderTooltip('data[label]',
                        'This label will appear next to the field. It should be in English (translation available in the translation module).'
                    );
                    changeLabelAndPlaceholderTooltip('data[placeholder]',
                        'The placeholder text that will appear when this field is empty. It should be in English (translation available in the translation module).'
                    );
                });
                builder.on('updateComponent', function(component) {
                    makeKeyFieldReadOnly();
                    addPrefixImage(component);
                    addSuffixImage(component);
                    changeLabelAndPlaceholderTooltip('data[label]',
                        'This label will appear next to the field. It should be in English (translation available in the translation module).'
                    );
                    changeLabelAndPlaceholderTooltip('data[placeholder]',
                        'The placeholder text that will appear when this field is empty. It should be in English (translation available in the translation module).'
                    );
                });

                var submitBtn = document.getElementById('submitFormBtn');

                submitBtn.addEventListener('click', function() {
                    var formData = builder.schema.components;
                    var formName = document.getElementById('formNameInput').value;
                    var url = "{{ route('form.update', ':formId') }}".replace(':formId',
                        '{{ $form->id }}');
                    var jsEditor = document.querySelector('[name="side_code"]').value;
                    var submittable = document.querySelector('input[name="submittable"]').checked ?
                        1 : 0;
                    var database = document.querySelector('input[name="database"]').checked ? 1 : 0;
                    var mail = document.querySelector('input[name="mail"]').checked ? 1 : 0;
                    var mails = [];
                    document.querySelectorAll('input[name="mails[]"]').forEach(function(input) {
                        if (input.value) {
                            mails.push(input.value);
                        }
                    });
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                json_definition: formData,
                                form_name: formName,
                                js_editor: jsEditor,
                                submittable: submittable,
                                database: database,
                                mail: mail,
                                mails: mails
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            $('#errors').removeClass('d-block');
                            $('#errors').addClass('d-none');
                            location.reload();
                        })
                        .catch(error => {
                            $('#errors').removeClass('d-none');
                            $('#errors').addClass('d-block');
                            console.error('Error saving form:', error.message);
                        });
                });
            });
        });
    </script>
@endsection
