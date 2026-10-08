<div class="modal fade" id="modal-edit-working-hours" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" class="update-working-hours-form" data-success="edit_working_time_success" data-notify="true">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal" id="modal-add">make edit</h5>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="{{ trans('cms::app.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            {{ trans('cms::app.all_days_start') }}</label>
                        <div class="row w-100 m-0 working-hours-checkbox">
                            <input type="time" name="all_days_start" class="form-control col-11 mr-2" value=""
                                autocomplete="off" placeholder="">
                            {{ Field::checkbox('', 'all_days_start_status', [
                                'value' => 1,
                                'checked' => false,
                                'class' => 'working-hours-checkbox',
                            ]) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            {{ trans('cms::app.all_days_end') }}</label>
                        <div class="row w-100 m-0 working-hours-checkbox">
                            <input type="time" name="all_days_end" class="form-control col-11 mr-2" value=""
                                autocomplete="off" placeholder="">
                            {{ Field::checkbox('', 'all_days_end_status', [
                                'value' => 1,
                                'checked' => false,
                                'class' => 'working-hours-checkbox',
                            ]) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            {{ trans('cms::app.friday_start') }}</label>
                        <div class="row w-100 m-0 working-hours-checkbox">
                            <input type="time" name="friday_start" class="form-control col-11 mr-2" value=""
                                autocomplete="off" placeholder="">
                            {{ Field::checkbox('', 'friday_start_status', [
                                'value' => 1,
                                'checked' => false,
                                'class' => 'working-hours-checkbox',
                            ]) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            {{ trans('cms::app.friday_end') }}</label>
                        <div class="row w-100 m-0 working-hours-checkbox">
                            <input type="time"name="friday_end" class="form-control col-11 mr-2" value=""
                                autocomplete="off" placeholder="">
                            {{ Field::checkbox('', 'friday_end_status', [
                                'value' => 1,
                                'checked' => false,
                                'class' => 'working-hours-checkbox',
                            ]) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label> {{ trans('cms::app.saturday_start') }}</label>

                        <div class="row w-100 m-0 working-hours-checkbox">
                            <input type="time"name="saturday_start" class="form-control col-11 mr-2" value=""
                                autocomplete="off" placeholder="">
                            {{ Field::checkbox('', 'saturday_start_status', [
                                'value' => 1,
                                'checked' => false,
                                'class' => 'working-hours-checkbox',
                            ]) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            {{ trans('cms::app.saturday_end') }}</label>

                        <div class="row w-100 m-0 working-hours-checkbox">
                            <input type="time" name="saturday_end" class="form-control col-11 mr-2" value=""
                                autocomplete="off" placeholder="">
                            {{ Field::checkbox('', 'saturday_end_status', [
                                'value' => 1,
                                'checked' => false,
                                'class' => 'working-hours-checkbox',
                            ]) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            {{ trans('cms::app.thursday_start') }}</label>
                        <div class="row w-100 m-0 working-hours-checkbox">
                            <input type="time" name="thursday_start" class="form-control col-11 mr-2" value=""
                                autocomplete="off" placeholder="">
                            {{ Field::checkbox('', 'thursday_start_status', [
                                'value' => 1,
                                'checked' => false,
                                'class' => 'working-hours-checkbox',
                            ]) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            {{ trans('cms::app.thursday_end') }}</label>
                        <div class="row w-100 m-0 working-hours-checkbox">
                            <input type="time" name="thursday_end" class="form-control col-11 mr-2" value=""
                                autocomplete="off" placeholder="">
                            {{ Field::checkbox('', 'thursday_end_status', [
                                'value' => 1,
                                'checked' => false,
                                'class' => 'working-hours-checkbox',
                            ]) }}
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-primary update-working-hours-btn">{{ trans('cms::app.add') }}</button>
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ trans('cms::app.close') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
