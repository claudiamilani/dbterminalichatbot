@extends('layouts.adminlte.template',['page_title' => trans('app_configuration.title'),'fa_icon_class' => 'fa fa-gears'])
@push('styles')
@endpush
@section('content')
    <div class="row">
        {!! Form::model($app_config, ['method' => 'patch', 'route' => 'admin::app_configuration.update','class' => 'form-horizontal','files'=>'true']) !!}
        @component('components.widget', ['size' => 12])
            @slot('title')@lang('app_configuration.fieldset.security')@endslot
            @slot('body')
                <div class="form-group required">
                    <label for="max_failed_login_attempts"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.max_failed_login_attempts')</label>
                    <div class="col-md-2">
                        {!! Form::number('max_failed_login_attempts', null, ['class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="failed_login_reset_interval"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.failed_login_reset_interval')</label>
                    <div class="col-md-2">
                        {!! Form::number('failed_login_reset_interval', null, ['class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="pwd_reset_unlocks_account"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_reset_unlocks_account')</label>
                    <div class="col-md-2">
                        {!! Form::select('pwd_reset_unlocks_account', [0 => trans('common.no'),1 => trans('common.yes')], null, ['class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="pwd_min_length"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_min_length')</label>
                    <div class="col-md-2">
                        {!! Form::number('pwd_min_length', null, ['class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="pwd_regexp"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_regexp')</label>
                    <div class="col-md-9">
                        {!! Form::text('pwd_regexp', null, ['class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="pwd_complexity_err_msg"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_complexity_err_msg')</label>
                    <div class="col-md-9">
                        {!! Form::text('pwd_complexity_err_msg', null, ['class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="pwd_history"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_history')</label>
                    <div class="col-md-2">
                        {!! Form::number('pwd_history', null, ['class' => 'form-control','required']) !!}
                    </div>
                    <div class="col-md-12 col-md-offset-3">
                        <small><i class="fa fa-warning text-danger"></i> @lang('app_configuration.edit.passwords_reset_warning')
                        </small>
                    </div>
                </div>
                <div class="form-group required">
                    <label for="pwd_never_expires"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_never_expires')</label>
                    <div class="col-md-2">
                        {!! Form::select('pwd_never_expires', [0 => trans('common.no'),1 => trans('common.yes')], null, ['id' => 'pwd_never_expires', 'class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="pwd_expires_in"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_expires_in')</label>
                    <div class="col-md-2">
                        {!! Form::number('pwd_expires_in', null, ['class' => 'form-control','required', 'id' => 'pwd_expires_in', $app_config->pwd_never_expires ? 'disabled' : '']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="allow_pwd_reset"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.allow_pwd_reset')</label>
                    <div class="col-md-2">
                        {!! Form::select('allow_pwd_reset', [0 => trans('common.no'),1 => trans('common.yes')], null, ['id' => 'allow_pwd_reset', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            @endslot

            @slot('footer')
                <div class="btn-toolbar">
                    @can('update', \App\AppConfiguration::class)
                        <button type="submit" class="btn btn-md btn-primary pull-right"><i class="fas fa-save fa-fw"></i> @lang('common.form.save')</button>
                    @endcan
                    <a href="{{ route('admin::app_configuration.show') }}">
                        <button type="button" class="btn btn-md btn-secondary pull-right"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button></a>
                </div>
            @endslot
        @endcomponent
        @component('components.widget', ['size' => 12])
            @slot('title')@lang('app_configuration.fieldset.manual')@endslot
            @slot('body')
                <div class="form-group">
                    <label for="manual_file_name"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.manual_file_name')</label>
                    <div class="col-md-9">
                        {!! Form::text('manual_file_name', null, ['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label for="manual_file_path"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.manual_file_path')</label>
                    <div class="col-md-9">
                        {!! Form::file('manual_file_path', ['class' => 'form-control']) !!}
                        <small><i class="fa fa-warning text-danger"></i> @lang('app_configuration.edit.file_overwrite_warning')
                        </small>
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    @can('update', \App\AppConfiguration::class)
                        <button type="submit" class="btn btn-md btn-primary pull-right"><i class="fas fa-save fa-fw"></i> @lang('common.form.save')</button>
                    @endcan
                    <a href="{{ route('admin::app_configuration.show') }}">
                        <button type="button" class="btn btn-md btn-secondary pull-right"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button></a>
                </div>
            @endslot
        @endcomponent
        @component('components.widget', ['size' => 12])
            @slot('title')@lang('app_configuration.fieldset.password_recovery_user')@endslot
            @slot('body')
                <div class="form-group">
                    <label for="pwdr_mail_obj_u"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.pwdr_mail_obj_u')</label>
                    <div class="col-md-9">
                        {!! Form::text('pwdr_mail_obj_u', null, ['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label for="pwdr_mail_body_u"
                           class="col-md-3 control-label">@lang('app_configuration.attributes.pwdr_mail_body_u')</label>
                    <div class="col-md-9">
                        {!! Form::textarea('pwdr_mail_body_u', null, ['class' => 'form-control tinyText']) !!}
                        <small class="form-control-static"><i
                                    class="fa fa-info-circle text-primary"></i> @lang('app_configuration.placeholders_hint.pwdr_u')
                        </small>
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    @can('update', \App\AppConfiguration::class)
                        <button type="submit" class="btn btn-md btn-primary pull-right"><i class="fas fa-save fa-fw"></i> @lang('common.form.save')</button>
                    @endcan
                    <a href="{{ route('admin::app_configuration.show') }}">
                        <button type="button" class="btn btn-md btn-secondary pull-right"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button></a>
                </div>
            @endslot
        @endcomponent
        {!! Form::close() !!}
    </div>
@endsection

@push('scripts')
    <!-- TinyMCE -->
    <script src="{{ @asset('/vendor/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector: '.tinyText',
            menubar: false,
            plugins: 'code',
            toolbar: 'media | underline bold italic | undo redo | image | link, alignleft aligncenter alignright | outdent indent | code',
        });
    </script>

    <script>
        pwd_never_expires = $('#pwd_never_expires');
        pwd_expires_in = $('#pwd_expires_in');

        if (pwd_never_expires.val() === '0') {
            pwd_expires_in.attr('disabled', true);
        }

        pwd_never_expires.on('change', function () {
            if ($(this).val() === '0') {
                pwd_expires_in.attr('disabled', true);
            } else {
                pwd_expires_in.attr('disabled', false);
            }
        });
    </script>
@endpush
