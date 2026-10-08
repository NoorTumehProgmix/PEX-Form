<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="upload-modal-label"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="upload-modal-label">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active text-capitalize" id="upload-tab" data-toggle="tab" href="#upload"
                                role="tab" aria-controls="home" aria-selected="true">{{
                                trans_cms(('cms::app.upload_data')) }}</a>
                        </li>
                    </ul>
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans_cms('cms::app.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                        <form action="{{ route('post.import',$setting['key']) }}" role="form" id="branchimportForm"
                            name="importForm" method="post" class="dropzone" enctype='multipart/form-data'>

                            <div class="form-group" id="attachment">
                                <div class="controls text-center">
                                    <div class="input-group w-100">
                                        {{-- <a class="btn btn-primary w-100 text-white" id="branch-upload-button">
                                            {{ trans_cms('cms::filemanager.message-choose') }}
                                        </a> --}}
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="type" id="type" value="{{$setting['key']}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> {{ trans_cms('cms::app.close') }}
                </button>
            </div>
        </div>
    </div>
</div>
