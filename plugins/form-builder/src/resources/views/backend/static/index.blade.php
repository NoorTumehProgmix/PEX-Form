@extends('formBuilder::layouts.form-builder')

@section('form-builder-content')
    <form action="{{ !$create ? route('form.update', $form?->id) : route('form.store', $type) }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6 text-right">
                <div class="btn-group">
                    <button type="submit" class="btn btn-success px-5">
                        <i class="fa fa-save"></i> {{ trans_cms('cms::app.save') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <div class="row mb-2">
                    <div class="col-md-6">
                        {{ Field::text(trans_cms('cms::app.name'), 'form_name', [
                            'value' => $form?->name,
                        ]) }}
                        @error('form_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        {{ Field::checkbox(trans_cms('cms::app.is_submittable'), 'submittable', [
                            'value' => 1,
                            'checked' => ($form?->submittable == null ? true : $form?->submittable) ? true : false,
                        ]) }}

                        <div class="form-group" id="submitted-destinations">


                            <label class="col-form-label">{{ trans_cms('cms::app.submit_destinations') }}</label>
                            {{ Field::checkbox(trans_cms('cms::app.database'), 'database', [
                                'value' => 1,
                                'checked' => $form?->is_database_submittable ? true : false,
                            ]) }}

                            {{ Field::checkbox(trans_cms('cms::app.mail'), 'mail', [
                                'value' => 1,
                                'checked' => $form?->destinations == null ? old('mail') : true,
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
                                </div>
                                @error('mails.*')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <button type="button" class="btn btn-primary add-key-value mt-2" id="addInput">
                                    {{ trans_cms('cms::app.add_mail') }}</button>
                            </div>
                        </div>

                        <div id="path">{{ json_decode($form?->form_definition) }}</div>
                        <div id="jsPath">{{ json_decode($form?->form_definition) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </form>

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
        function slugify(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-') // Replace spaces with -
                .replace(/[^\w\-]+/g, '') // Remove all non-word chars
                .replace(/\-\-+/g, '-') // Replace multiple - with single -
                .replace(/^-+/, '') // Trim - from start of text
                .replace(/-+$/, '') // Trim - from end of text
                .replace(/[^a-z0-9 -]/g, '') // remove any non-alphanumeric characters
                .replace(/\s+/g, '-') // replace spaces with hyphens
                .replace(/-+/g, '-'); // remove consecutive hyphens;
        }

        const formNameInput = document.querySelector('[name="form_name"]');
        const path = document.getElementById('path');
        const jsPath = document.getElementById('jsPath');
        @if (!$form)
            formNameInput.addEventListener('input', function() {
                path.textContent = 'blade file (frontend server): `modules/Frontend/resources/views/forms/' +
                slugify(
                    formNameInput
                    .value) +
                '.blade.php`' + ' Create this file and type your HTML here.';
                jsPath.textContent = 'javascript file (frontend server): `public/forms/js/' + slugify(formNameInput
                    .value) +
                '.js`' + ' Optional (Your JavaScript goes here)';
            });
        @else
            @php
                $formDef = trim($form?->form_definition, '"');
                $formCode = trim($form?->side_code, '"');
            @endphp
            path.textContent = 'blade file (frontend server): `modules/Frontend/resources/views/forms/' + slugify(
                '{{ $formDef }}') +
            '.blade.php`';

            jsPath.textContent = 'javascript file (frontend server): `public/forms/js/' + slugify(
                '{{ $formCode }}') +
            '.js`';
        @endif
    </script>
@endsection
