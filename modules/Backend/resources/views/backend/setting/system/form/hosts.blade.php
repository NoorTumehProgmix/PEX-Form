<form method="post" action="{{ route('admin.setting.save') }}" class="form-ajax">
    <input type="hidden" name="form" value="hosts">

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

    <h5>{{ trans_cms('cms::app.hosts') }}</h5>
    <div class="form-group tags-only">
        <label for="hosts" class="form-label">
            {{ trans_cms('cms::app.hosts') }}
        </label>
        <select name="hosts[]" class="form-control" id="hosts" multiple="multiple">
            @if (get_config('hosts'))
                @foreach (get_config('hosts') as $keyword)
                    <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                @endforeach
            @endif
        </select>
    </div>

</form>
