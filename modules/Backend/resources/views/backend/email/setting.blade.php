<div class="row mt-3">
    <div class="col-md-7">
        <h5>{{ trans_cms('cms::app.setting') }}</h5>

        @php
            $config = get_config('email');
        @endphp

        @component('cms::components.form', [
            'method' => 'post',
            'action' => route('admin.setting.email.save'),
        ])

            @component('cms::components.form_input', [
                'label' => trans_cms('cms::app.email_host'),
                'name' => 'email[host]',
                'value' => $config['host'] ?? '',
            ])
            @endcomponent

            <div class="row">
                <div class="col-md-6">
                    @component('cms::components.form_input', [
                        'label' => trans_cms('cms::app.email_port'),
                        'name' => 'email[port]',
                        'value' => $config['port'] ?? '',
                    ])
                    @endcomponent
                </div>
                <div class="col-md-6">
                    @component('cms::components.form_select', [
                        'label' => trans_cms('cms::app.email_encryption'),
                        'name' => 'email[encryption]',
                        'value' => $config['encryption'] ?? '',
                        'options' => [
                            '' => 'none',
                            'tls' => 'tls',
                            'ssl' => 'ssl',
                        ],
                    ])
                    @endcomponent
                </div>
            </div>

            @component('cms::components.form_input', [
                'label' => trans_cms('cms::app.email_username'),
                'name' => 'email[username]',
                'value' => $config['username'] ?? '',
            ])
            @endcomponent

            @component('cms::components.form_input', [
                'label' => trans_cms('cms::app.tenant_id'),
                'name' => 'email[tenant_id]',
                'value' => $config['tenant_id'] ?? '',
            ])
            @endcomponent

            @component('cms::components.form_input', [
                'label' => trans_cms('cms::app.client_id'),
                'name' => 'email[client_id]',
                'value' => $config['client_id'] ?? '',
            ])
            @endcomponent
            @component('cms::components.form_input', [
                'label' => trans_cms('cms::app.client_secret'),
                'name' => 'email[client_secret]',
                'value' => $config['client_secret'] ?? '',
            ])
            @endcomponent

            @component('cms::components.form_input', [
                'label' => trans_cms('cms::app.email_password'),
                'name' => 'email[password]',
                'value' => $config['password'] ?? '',
                'type' => 'password',
            ])
            @endcomponent
            <hr>

            @component('cms::components.form_input', [
                'label' => trans_cms('cms::app.email_from_address'),
                'name' => 'email[from_address]',
                'value' => $config['from_address'] ?? '',
            ])
            @endcomponent

            @component('cms::components.form_input', [
                'label' => trans_cms('cms::app.email_from_name'),
                'name' => 'email[from_name]',
                'value' => $config['from_name'] ?? '',
            ])
            @endcomponent

            @component('cms::components.form_input', [
                'label' => trans_cms('cms::app.contact_email'),
                'name' => 'email[contact_email]',
                'value' => $config['contact_email'] ?? '',
            ])
            @endcomponent

          

            <div class="mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> {{ trans_cms('cms::app.save') }}
                </button>
            </div>
        @endcomponent

    </div>

    <div class="col-md-5">
        <h5>{{ trans_cms('cms::app.send_email_test') }}</h5>

        @component('cms::components.form', [
            'method' => 'post',
            'action' => route('admin.setting.email.test-email'),
        ])
            @component('cms::components.form_input', [
                'label' => trans_cms('cms::app.email'),
                'name' => 'email',
                'required' => true,
            ])
            @endcomponent

            <button type="submit" class="btn btn-success">
                <i class="fa fa-send"></i>
                {{ trans_cms('cms::app.send_email_test') }}
            </button>
        @endcomponent
    </div>
</div>
