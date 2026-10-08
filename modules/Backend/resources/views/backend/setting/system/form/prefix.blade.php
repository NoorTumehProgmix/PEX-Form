<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"></div>

            <div class="col-md-6">
                <div class="btn-group float-right">
                    <button type="reset" class="btn btn-success" id="generate_prefix_btn">
                        <i class="fa fa-refresh"></i> {{ trans_cms('cms::app.generate') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">
                <h5>{{ trans_cms('cms::app.generate_prefix') }}</h5>
                <div class="relative">
                    {{ Field::text(trans_cms('cms::app.prefix'), 'admin_prefix', [
                        'disabled' => true,
                        'value' => config('juzaweb.admin_prefix'),
                    ]) }}
                    <span class="copy-field"><i class="fa fa-copy"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#generate_prefix_btn").click(function(e) {
        e.preventDefault();
        var prefix = generateRandomString(10);
        $('input[name="admin_prefix"]').val(prefix);
    });

    function generateRandomString(length) {
        const characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        return result;
    }
    $(".copy-field").click(function() {
        const valueToCopy = $('input[name="admin_prefix"]').val();
        const tempInput = $('<input>');
        $("body").append(tempInput);
        tempInput.val(valueToCopy).select();
        document.execCommand("copy");
        tempInput.remove();
        alert('Copied to clipboard: ' + valueToCopy);
    });
</script>
