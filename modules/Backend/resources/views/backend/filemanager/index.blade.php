<link rel="stylesheet" href="{{ asset('jw-styles/juzaweb/css/filemanager.min.css?v=0.0.0.1') }}">
<div class="modal fade" id="juzawebFileManagerModal" data-multi="{{ $multiChoose }}" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">File Manager</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <nav class="navbar sticky-top navbar-expand-lg navbar-dark" id="nav">

                    <a class="navbar-brand d-block" id="show_tree">
                        <i class="fa fa-bars fa-fw"></i>
                    </a>
                    <a class="navbar-brand d-block d-lg-none" id="current_folder"></a>
                    <a id="loading" class="navbar-brand"><i class="fa fa-spinner fa-spin"></i></a>
                    <div class="ml-auto px-2">
                        {{-- <a class="navbar-link d-none" id="multi_selection_toggle">
            <i class="fa fa-check-double fa-fw"></i>
            <span class="d-none d-lg-inline">{{ trans_cms('cms::filemanager.menu-multiple') }}</span>
        </a> --}}
                    </div>
                    <a class="navbar-toggler collapsed border-0 px-1 py-2 m-0" data-toggle="collapse"
                        data-target="#nav-buttons">
                        <i class="fa fa-cog fa-fw"></i>
                    </a>
                    <div class="icon-wrapper">
                        <a class="btn btn-light" id="to-previous" title="Parent">
                            <i class="fa fa-level-up"></i>
                        </a>
                        <a class="btn btn-light" title="New Folder" id="add-folder"><i class="fa fa-folder"></i></a>
                        <form action="{{ route('filemanager.upload') }}" role='form' id='uploadFormShort'
                            name='uploadForm' method='post' enctype='multipart/form-data'>
                            <div class="form-group" id="attachment">
                                <a class="btn btn-primary" title="Upload" id="upload-button-short"><i
                                        class="fa fa-upload"></i></a>
                            </div>
                            <input type='hidden' name='working_dir' class='working_dir'>
                            <input type='hidden' name='type' class='type' value='{{ request('type') }}'>
                            <input type='hidden' name='_token' value='{{ csrf_token() }}'>
                        </form>

                    </div>
                    {{-- <div class="collapse navbar-collapse flex-grow-0" id="nav-buttons">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" data-display="grid">
                                    <i class="fa fa-th-large fa-fw"></i>
                                    <span>{{ trans_cms('cms::filemanager.nav-thumbnails') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-display="list">
                                    <i class="fa fa-list-ul fa-fw"></i>
                                    <span>{{ trans_cms('cms::filemanager.nav-list') }}</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-sort fa-fw"></i>{{ trans_cms('cms::filemanager.nav-sort') }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-right border-0"></div>
                            </li>
                        </ul>
                    </div> --}}
                </nav>



                <div class="d-flex flex-row">
                    <div id="tree"></div>

                    <div class="w-100 p-2" style="min-height:650px;">
                        <div id="alerts"></div>

                        <nav aria-label="breadcrumb" class="d-lg-block" id="breadcrumbs">

                            <form action="" id="file-search" method="get" class="breadcrumb form-inline">
                                <input type="hidden" name="type" value="{{ @$_GET['type'] }}" />
                                <input type="search" class="form-control search" style="width:100%;max-width:500px;"
                                    name="search" placeholder="{{ trans_cms('cms::app.search_by_name') }}"
                                    value="{{ @$_GET['search'] }}" autocomplete="off">
                            </form>
                        </nav>

                        <div id="empty" class="d-none">
                            <i class="fa fa-folder-open"></i>
                            {{ trans_cms('cms::filemanager.message-empty') }}
                        </div>

                        <div id="content"></div>
                        <div id="pagination"></div>

                        <a id="item-template" class="d-none">
                            <div class="square"></div>

                            <div>
                                <div class="item_name text-truncate"></div>
                                <time class="text-muted font-weight-light text-truncate"></time>
                            </div>
                        </a>
                    </div>
                    <form style='flex-shrink:0;width:280px;display:none;padding:10px;box-sizing:border-box;'
                        data-route="{{ route('admin.media.update', ['__ID__']) }}" action="" method="post"
                        class="form-ajax" id="mediameta_form">
                        <div id="file-jquery-message"></div>
                        @csrf
                        @method('put')
                        <input type="hidden" name="is_file" value="1">
                        {{ Field::text(trans_cms('cms::app.name'), 'name', ['value' => '']) }}
                        {{ Field::textarea(trans_cms('cms::app.caption'), 'caption', ['value' => '']) }}
                        {{ Field::textarea(trans_cms('cms::app.description'), 'description', ['value' => '']) }}
                        {{ Field::textarea(trans_cms('cms::app.alternative_text'), 'text', ['value' => '']) }}
                        <button type="submit" class="btn btn-primary mb-2">{{ trans_cms('cms::app.save') }}</button>
                    </form>
                </div>
                <div id="fab"></div>
                <nav class="bg-light fixed-bottom border-top d-none" id="actions">
                    <a data-action="open" data-multiple="false"><i
                            class="fa fa-folder-open"></i>{{ trans_cms('cms::filemanager.btn-open') }}
                    </a>
                    <a data-action="preview" data-multiple="true"><i
                            class="fa fa-images"></i>{{ trans_cms('cms::filemanager.menu-view') }}
                    </a>
                    <a data-action="use" data-multiple="true"><i
                            class="fa fa-check"></i>{{ trans_cms('cms::filemanager.btn-confirm') }}
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="uploadModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active text-capitalize" id="upload-tab" data-toggle="tab"
                                href="#upload-form" role="tab" aria-controls="upload-form"
                                aria-selected="true">{{ trans_cms('cms::app.upload_media') }}</a>
                        </li>
                        @if (config('juzaweb.filemanager.upload_from_url'))
                            <li class="nav-item">
                                <a class="nav-link text-capitalize" id="import-tab" data-toggle="tab"
                                    href="#import-form" role="tab" aria-controls="import-form"
                                    aria-selected="false">{{ trans_cms('cms::app.file_manager.upload_from_url') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-capitalize" id="external-tab" data-toggle="tab"
                                    href="#external-form" role="tab" aria-controls="profile"
                                    aria-selected="false">{{ trans_cms('cms::app.file_manager.external_url') }}</a>
                            </li>
                        @endif
                    </ul>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aia-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="upload-form" role="tabpanel"
                        aria-labelledby="upload-tab">
                        <form action="{{ route('filemanager.upload') }}" role='form' id='uploadForm'
                            name='uploadForm' method='post' enctype='multipart/form-data' class="dropzone">
                            <div class="form-group" id="attachment">
                                <div class="controls text-center">
                                    <div class="input-group w-100">
                                        <a class="btn btn-primary w-100 text-white"
                                            id="upload-button">{{ trans_cms('cms::filemanager.message-choose') }}</a>
                                    </div>
                                </div>
                            </div>
                            <input type='hidden' name='working_dir' class='working_dir'>
                            <input type='hidden' name='type' class='type' value='{{ request('type') }}'>
                            <input type='hidden' name='_token' value='{{ csrf_token() }}'>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="import-form" role="tabpanel" aria-labelledby="import-tab">
                        <form action="{{ route('filemanager.import') }}" role="form" method="post"
                            id="import-url">

                            {{ Field::text(trans_cms('cms::app.url'), 'url', ['required' => true]) }}

                            <div class="form-check">
                                <input type="checkbox" name="download" class="form-check-input" value="1"
                                    id="download-checkbox" checked>
                                <label class="form-check-label"
                                    for="download-checkbox">{{ trans_cms('cms::app.file_manager.download_to_server') }}</label>
                            </div>

                            <input type="hidden" name="working_dir" class='working_dir'>
                            <input type="hidden" name="type" class="type" value="{{ request('type') }}">

                            <button type="submit" class="btn btn-success mt-2">
                                <i class="fa fa-cloud-upload"></i>
                                {{ trans_cms('cms::app.file_manager.upload_file') }}
                            </button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="external-form" role="tabpanel" aria-labelledby="external-tab">
                        <form action="{{ route('filemanager.import') }}" role="form" method="post"
                            id="import-url-ext">

                            {{ Field::text(trans_cms('cms::app.url'), 'url', ['required' => true]) }}
                            <input type="hidden" name="working_dir" class='working_dir'>
                            <input type="hidden" name="type" class="type" value="youtube">

                            <button type="submit" class="btn btn-success mt-2">
                                <i class="fa fa-link"></i> {{ trans_cms('cms::app.file_manager.insert_link') }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary w-100" data-dismiss="modal">
                    {{ trans_cms('cms::filemanager.btn-close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="notify" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary w-100"
                    data-dismiss="modal">{{ trans_cms('cms::filemanager.btn-close') }}</button>
                <button type="button" class="btn btn-primary w-100"
                    data-dismiss="modal">{{ trans_cms('cms::filemanager.btn-confirm') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary w-100"
                    data-dismiss="modal">{{ trans_cms('cms::filemanager.btn-close') }}</button>
                <button type="button" class="btn btn-primary w-100"
                    data-dismiss="modal">{{ trans_cms('cms::filemanager.btn-confirm') }}</button>
            </div>
        </div>
    </div>
</div>

<div id="carouselTemplate" class="d-none carousel slide bg-light" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#previewCarousel" data-slide-to="0" class="active"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <a class="carousel-label"></a>
            <div class="carousel-image"></div>
        </div>
    </div>
    <a class="carousel-control-prev" href="#previewCarousel" role="button" data-slide="prev">
        <div class="carousel-control-background" aria-hidden="true">
            <i class="fa fa-chevron-left"></i>
        </div>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#previewCarousel" role="button" data-slide="next">
        <div class="carousel-control-background" aria-hidden="true">
            <i class="fa fa-chevron-right"></i>
        </div>
        <span class="sr-only">Next</span>
    </a>
</div>


<script>
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#uploadForm", {
        paramName: "upload",
        uploadMultiple: false,
        parallelUploads: 5,
        timeout: 0,
        clickable: '#upload-button',
        dictDefaultMessage: lang['message-drop'],
        init: function() {
            this.on('success', function(file, response) {
                loadFolders();
            });
        },

        acceptedFiles: "{{ implode(',', $mimeTypes) }}",
        maxFilesize: parseInt("{{ $maxSize }}"),
        chunking: true,
        forceChunking: true,
        chunkSize: 1048576,
    });
    var myDropzone = new Dropzone("#uploadFormShort", {
        paramName: "upload",
        uploadMultiple: false,
        parallelUploads: 5,
        timeout: 0,
        clickable: '#upload-button-short',
        dictDefaultMessage: lang['message-drop'],
        init: function() {
            this.on('success', function(file, response) {
                loadFolders();
            });
        },

        acceptedFiles: "{{ implode(',', $mimeTypes) }}",
        maxFilesize: parseInt("{{ $maxSize }}"),
        chunking: true,
        forceChunking: true,
        chunkSize: 1048576,
    });
</script>
