@php
    $value = $value ?? [];
   if ($value == 'none'){
        $value = [];
    }
    $value = !is_array($value) ? [$value] : $value;
    $identifier = $name;
@endphp
<input type="hidden" name="{{ $multiple ?? false ? "{$name}[]" : $name }}" value="{{ $value[0] ?? null }}">
<label class="col-form-label" for="{{ $id ?? $name }}">
    {{ $label ?? $name }} @if ($required ?? false)
        <abbr>*</abbr>
    @endif
</label>

<a href="javascript:void(0)" class="form-control" data-toggle="modal" data-label
   data-target="#modal-add-preview">{{ isset($value[0]) ? $options[$value[0]]['label'] : $options[''] ?? trans('cms::app.choose') }}</a>

<div class="modal fade pe-0" id="modal-add-preview" role="dialog" aria-labelledby="modal-add-preview-title"
     aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-add-preview-title">{{ $options[''] ?? trans('cms::app.choose') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('cms::app.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-header">
                <input type="text" class="form-control" name="search" oninput="filterOptions(this.value)"
                       placeholder="{{ trans('cms::app.search') }}">
            </div>
            <div class="modal-body">
                <div class="row d-flex justify-content-start flex-wrap">
                    @foreach ($options ?? [] as $keyName => $name)
                        @php
                            if (is_array($name)):
                                $label = $name['label'];
                            else:
                                $label = $name;
                            endif;

                            $key = $keyName;
                            $image = $keyName;
                            if(empty($image)){
                                $image = 'none';
                            }
                        @endphp
                        <div id='template-{{ $key }}'
                             class="col-6 col-md-4 col-lg-3 col-xl-2 d-flex flex-column justify-content-center align-items-center mt-4 option accordion"
                             onclick="chose('{{ $key }}','{{ $label }}')">
                            <div class="image-container d-flex justify-content-center align-items-center">
                                <img src="{{ upload_url('templates/' . $image . '.png') }}" alt="{{ $label }}"
                                     class="w-100">
                            </div>
                            <div class="col-form-label text-center"> {{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ trans('cms::app.close') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#template-' + $('[name="' + '{{ $identifier }}' + '"]').val().trim()).addClass('bg-selected');
    });

    function chose(key, label) {
        $('#template-' + $('[name="' + '{{ $identifier }}' + '"]').val().trim()).removeClass('bg-selected');
        $('[name="' + '{{ $identifier }}' + '"]').val(key);
        $('#template-' + key.trim()).addClass('bg-selected');
        $('#modal-add-preview').modal('hide');
        $('[data-label]').text(label);
    }

    function filterOptions(searchTerm) {
        const options = document.querySelectorAll('.option');
        const regex = new RegExp(searchTerm, 'i');
        options.forEach(option => {
            const label = option.querySelector('div.col-form-label').textContent.trim();
            if (regex.test(label)) {
                option.classList.remove('hidden');
            } else {
                option.classList.add('hidden');
            }
        });
    }
</script>
