@extends('layouts.adminlte.template',['page_title' => trans('app_configuration.title'),'fa_icon_class' => 'fa fa-gears'])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget',['size' => 12 , 'hide_required_legend' => true])
            @slot('title')@lang('app_configuration.fieldset.security')@endslot
            @slot('body')
                <div class="form-horizontal">
                        <div class="form-group">
                            <label for="max_failed_login_attempts"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.max_failed_login_attempts')</label>
                            <div class="col-md-1">
                                <p class="form-control-static">{{ $app_config->max_failed_login_attempts }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="failed_login_reset_interval"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.failed_login_reset_interval')</label>
                            <div class="col-md-1">
                                <p class="form-control-static">{{ $app_config->failed_login_reset_interval }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pwd_reset_unlocks_account"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_reset_unlocks_account')</label>
                            <div class="col-md-1">
                                <p class="form-control-static">{{ ($app_config->pwd_reset_unlocks_account) ? trans('common.yes') : trans('common.no') }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pwd_min_length"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_min_length')</label>
                            <div class="col-md-1">
                                <p class="form-control-static">{{ $app_config->pwd_min_length }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pwd_regexp"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_regexp')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{ $app_config->pwd_regexp }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pwd_complexity_err_msg"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_complexity_err_msg')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{ $app_config->pwd_complexity_err_msg }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pwd_history"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_history')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{ $app_config->pwd_history }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for=""
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_never_expires')</label>
                            <div class="col-md-9">
                                <p class="form-control-static"><i
                                            class="fas {{$app_config->pwd_never_expires ? 'fas-check text-success' : 'fas-times text-danger' }}"></i> {{$app_config->pwd_never_expires ? trans('common.yes') : trans('common.no') }}
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pwd_expires_in"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.pwd_expires_in')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{ $app_config->pwd_expires_in }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="allow_pwd_reset"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.allow_pwd_reset')</label>
                            <div class="col-md-9">
                                <p class="form-control-static"><i class="fas {{$app_config->allow_pwd_reset ? 'fas-check text-success' : 'fas-times text-danger'}}"></i> {{$app_config->allow_pwd_reset ? trans('common.yes') : trans('common.no')}} </p>
                            </div>
                        </div>

                    @endslot
                    @slot('footer')
                        <div class="btn-toolbar">
                            @can('update', \App\AppConfiguration::class)<a
                                    href="{{ route('admin::app_configuration.edit') }}"
                                    class="btn btn-md btn-primary pull-right"><i class="fas fa-pen fa-fw"></i> @lang('common.form.edit')</a>
                            @endcan
                        </div>
                </div>
            @endslot
        @endcomponent
        @component('components.widget',['size' => 12 , 'hide_required_legend' => true])
            @slot('title')@lang('app_configuration.fieldset.manual')@endslot
            @slot('body')
                <div class="form-horizontal">
                        <div class="form-group">
                            <label for="manual_file_name"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.manual_file_name')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{ $app_config->manual_file_name }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="manual_file_path"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.manual_file_path')</label>
                            <div class="col-md-9">
                                <a target="_blank" href="{{ route('admin::app_configuration.view_manual') }}"><p
                                            class="form-control-static">{{ $app_config->manual_file_path ? trans('app_configuration.attributes.file_exists') : trans('app_configuration.attributes.file_404')  }}</p>
                                </a>
                            </div>
                        </div>
                    @endslot
                    @slot('footer')
                        <div class="btn-toolbar">
                            @can('update', \App\AppConfiguration::class)<a
                                    href="{{ route('admin::app_configuration.edit') }}"
                                    class="btn btn-md btn-primary pull-right"><i class="fas fa-pen fa-fw"></i> @lang('common.form.edit')</a>
                            @endcan
                        </div>
                </div>
            @endslot
        @endcomponent
        @component('components.widget',['size' => 12 , 'hide_required_legend' => true])
            @slot('title')@lang('app_configuration.fieldset.password_recovery_user')@endslot
            @slot('body')
                <div class="form-horizontal">
                        <div class="form-group">
                            <label for="pwdr_mail_obj_u"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.object')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{$app_config->pwdr_mail_obj_u}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pwdr_mail_body_u"
                                   class="col-md-3 control-label">@lang('app_configuration.attributes.body')</label>
                            <div class="col-md-9">
                                {!! Form::textarea('pwdr_mail_body_u', $app_config->pwdr_mail_body_u, ['class' => 'form-control height-md vertical tinyText','readonly']) !!}
                                <small class="form-control-static"><i
                                            class="fa fa-info-circle text-primary"></i> @lang('app_configuration.placeholders_hint.pwdr_u')
                                </small>
                            </div>
                        </div>
                    @endslot
                    @slot('footer')
                        <div class="btn-toolbar">
                            @can('update', \App\AppConfiguration::class)<a
                                    href="{{ route('admin::app_configuration.edit') }}"
                                    class="btn btn-md btn-primary pull-right"><i class="fas fa-pen fa-fw"></i> @lang('common.form.edit')</a>
                            @endcan
                        </div>
                </div>
            @endslot
        @endcomponent
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
            readonly: true,
            toolbar: 'media | underline bold italic | undo redo | image | link, alignleft aligncenter alignright | outdent indent | code',
        });
    </script>

@endpush
