@extends('cms::layouts.backend')

@section('content')
    @php
        if (isset($_GET['parent'])) {
            $linkCreate = $linkCreate . '?parent=' . $_GET['parent'];
        }
        if (isset($_GET['pages'])) {
            $linkCreate = $linkCreate . '?parent=' . $_GET['parent'];
        }
    @endphp
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="btn-group float-right">
                @if ($setting['key'] == 'branches')
                    <a href="{{ route('post.export', $setting['key']) }}" class="btn btn-success mx-2">
                        <i class="fa fa-file-excel-o"></i> {{ trans_cms('cms::app.export_excel') }}</a>
                @endif

                @if ($setting['key'] == 'branches')
                    <a href="javascript:void(0)" class="btn btn-success mx-2" data-toggle="modal" data-target="#upload-modal"><i
                            class="fa fa-cloud-upload"></i> {{ trans_cms('cms::app.import') }}</a>
                    @include('cms::backend.post.components.import_modal')
                @endif

                @if ($canCreate)
                    <a href="{{ $linkCreate }}" class="btn btn-success"><i class="fa fa-plus-circle"></i>
                        {{ trans_cms('cms::app.add_new') }}</a>
                @endif

                @do_action("post_type.{$setting->get('key')}.btn_group")
            </div>
        </div>
    </div>

    {{ $dataTable->render() }}

    @do_action("post_type.{$setting->get('key')}.index")
    <script>
        // $(document).ready(function () {
        // new Dropzone("#branchimportForm", {
        //     uploadMultiple: false,
        //     parallelUploads: 5,
        //     timeout: 0,
        //     clickable: '#branch-upload-button',
        //     dictDefaultMessage: "{{ trans_cms('cms::filemanager.message-drop') }}",
        //     init: function () {
        //         this.on('success', function (file, response) {
        //             if(response.status == false) {
        //                 this.defaultOptions.error(file, response.data.message);
        //             }
        //         });
        //     },
        //     headers: {
        //         'Authorization': "Bearer {{ csrf_token() }}"
        //     },
        //     acceptedFiles: ".xlsx,.xls",
        //     maxFilesize: 1024,
        //     chunking: true,
        //     chunkSize: 1048576,
        // });
        // });
    </script>
@endsection
