<form method="post" action="{{ route('admin.setting.save') }}" class="form-ajax">
    <input type="hidden" name="form" value="templates">

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

    <h5>{{ trans_cms('cms::app.templates_management') }}</h5>
    <div class="form-group">
        <textarea name="templates" style="width: 100%" rows="20">
{{ @get_config('templates') }}
        </textarea>
    </div>

</form>
