@extends('layouts.adminlte.template',['page_title' => trans('users.title'), 'fa_icon_class' => 'fa-users'])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('users.create.title')
            @endslot
            @slot('body')
                {!! Form::open(['route' => 'admin::users.store','class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
                <div class="form-group">
                    <label for="enabled" class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <input type="hidden" name="enabled" value="0">
                        {!! Form::checkbox('enabled', 1, 1, [ 'id' => 'enabled','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('common.active'), 'data-off' => trans('common.disabled'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="enabled" class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <input type="hidden" name="pwd_change_required" value="0">
                        {!! Form::checkbox('pwd_change_required', 1, 0, [ 'id' => 'pwd_change_required','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('users.attributes.pwd_change_required_on'), 'data-off' => trans('users.attributes.pwd_change_required_off'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="auth_type_id"
                           class="col-md-3 control-label">@lang('users.attributes.auth_type_id')</label>
                    <div class="col-md-9">
                        {!! Form::select('auth_type_id', $auth_types, null, [ 'id' => 'auth_type_id','class' => 'form-control','style' => 'width: 100%', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="" class="col-md-3 control-label">@lang('users.attributes.name')</label>
                    <div class="col-md-9">
                        {!! Form::text('name', null, [ 'class' => 'form-control','style' => 'width: 100%', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="" class="col-md-3 control-label">@lang('users.attributes.surname')</label>
                    <div class="col-md-9">
                        {!! Form::text('surname', null, [ 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="" class="col-md-3 control-label">@lang('users.attributes.email')</label>
                    <div class="col-md-9">
                        {!! Form::email('email', null, [ 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="user" class="col-md-3 control-label">@lang('users.attributes.user')</label>
                    <div class="col-md-9">
                        {!! Form::text('user', null, ['id' => 'user', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="" class="col-md-3 control-label">@lang('users.attributes.roles')</label>
                    <div class="col-md-9">
                        {!! Form::select('roles[]', $roles, [3] , [ 'class' => 'form-control mdl-multiselect','multiple','required']) !!}
                    </div>

                </div>
                <div class="form-group">
                    <label for="enabled_from"
                           class="col-md-3 control-label">@lang('users.attributes.enabled_from')</label>
                    <div class="col-md-9">
                        {!! Form::text('enabled_from', null, ['id' => 'enabled_from_dtp', 'class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="enabled_to" class="col-md-3 control-label">@lang('users.attributes.enabled_to')</label>
                    <div class="col-md-9">
                        {!! Form::text('enabled_to', null, ['id' => 'enabled_to_dtp', 'class' => 'form-control']) !!}
                    </div>
                </div>
                <div id="password_fields">
                    <div class="form-group required">
                        <label for="" class="col-md-3 control-label">@lang('users.attributes.password')</label>
                        <div class="col-md-9">
                            {!! Form::password('password', ['id' => 'password', 'class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group required">
                        <label for="" class="col-md-3 control-label">@lang('users.attributes.password_check')</label>
                        <div class="col-md-9">
                            {!! Form::password('password_check', ['id' => 'password_check', 'class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">

                    <a href="{{ backtoSource('admin::users.index') }}">
                        <button type="button" class="btn btn-md btn-secondary">
                            <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                        </button>
                    </a>
                    <button type="submit" class="btn btn-md btn-primary">
                        <i class="fas fa-save fa-fw"></i>@lang('common.form.save')
                    </button>
                </div>
            @endslot

        @endcomponent

        {!! Form::close() !!}
    </div>
@endsection
@push('scripts')
    @component('components.select2_script', ['name' => 'roles[]', 'route' => route('admin::roles.select2')])
        @slot('format_selection')
            if(output.id !== '') {
            output = output.text
            return output;
            }
            return '';
        @endslot
        @slot('format_output')
            if(!output.loading) {
            output = output.text
            return output;
            }
            return output.text;
        @endslot
    @endcomponent
    <script type="text/javascript">
        pwd_change = $("#pwd_change_required");
        pwd_change.on('change', function () {
            checkPassword()
        });

        checkPassword();

        function checkPassword() {
            const pwd_fields = $("#password_fields");
            const password = $("#password");
            const conf_password = $("#password_check");

            if (pwd_change.prop("checked") === false) {
                pwd_fields.removeClass('hide');
            } else {
                pwd_fields.addClass('hide');
                password.attr('required', false);
                conf_password.attr('required', false);
            }
        }

        $(function () {
            $('#enabled_from_dtp').datetimepicker({
                locale: 'it',
                useCurrent: false
            });
            $('#enabled_to_dtp').datetimepicker({
                locale: 'it',
                useCurrent: false
            });
        });
    </script>
@endpush