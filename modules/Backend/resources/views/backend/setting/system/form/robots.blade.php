<form method="post" action="{{ route('admin.setting.save') }}" class="form-ajax">
    <input type="hidden" name="form" value="robots">
    <div class="row mt-3">
        <div class="col-md-6"></div>

        <div class="col-md-6">
            <div class="btn-group float-right">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> {{ trans_cms('cms::app.save') }}
                </button>
            </div>
        </div>
    </div>

    <h5>{{ trans_cms('cms::app.robots') }}</h5>
    <div class="form-group">
        <textarea name="robots_file" style="width: 100%" rows="20">{{ $content }}</textarea>

    </div>

</form>
