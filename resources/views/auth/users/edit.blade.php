@extends('layouts.adminlte.template',['page_title' => trans('users.title'), 'fa_icon_class' => 'fa-users'])

@push('styles')

@endpush
@section('content')
    <div class="row">
        {!! Form::model($account, ['method' => 'patch', 'route' => ['admin::users.update',$account->id],'class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
        @component('components.widget',['size' => 8, 'title' => trans('users.edit.title')])
            @slot('body')
            @can('manageStatus',$account)
                <div class="form-group">
                    <label for="enabled" class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <input type="hidden" name="enabled" value="0">
                        {!! Form::checkbox('enabled', 1, null, [ 'id' => 'enabled','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('common.active'), 'data-off' => trans('common.disabled'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>
                @endcan
                @canany(['resetPassword','managePassword'], $account)
                <div class="form-group">
                    <label for="pwd_change_required" class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <input type="hidden" name="pwd_change_required" value="0">
                        {!! Form::checkbox('pwd_change_required', 1, null, [ 'id' => 'pwd_change_required','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('users.attributes.pwd_change_required_on'), 'data-off' => trans('users.attributes.pwd_change_required_off'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>
                @endcanany
                <div class="form-group required">
                    <label for="" class="col-md-3 control-label">@lang('users.attributes.name')</label>
                    <div class="col-md-9">
                        {!! Form::text('name',null, ['id' => 'name', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="" class="col-md-3 control-label">@lang('users.attributes.surname')</label>
                    <div class="col-md-9">
                        {!! Form::text('surname', null, ['id' => 'surname', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="" class="col-md-3 control-label">@lang('users.attributes.email')</label>
                    <div class="col-md-9">
                        {!! Form::email('email',null, ['id' => 'email', 'class' => 'form-control', 'required' , Auth::user()->isAzure() ? 'readonly' : '']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="user" class="col-md-3 control-label">@lang('users.attributes.user')</label>
                    <div class="col-md-9">
                        {!! Form::text('user', null, ['id' => 'user', 'class' => 'form-control', 'required' , Auth::user()->isAzure() ? 'readonly' : '']) !!}
                    </div>
                </div>
                @can('manageRoles',$account)
                    <div class="form-group">
                        <label for="" class="col-md-3 control-label">@lang('users.attributes.roles')</label>
                        <div class="col-md-9">
                            {!! Form::select('roles[]', $roles, optional($account->roles)->pluck('id') , [ 'class' => 'form-control mdl-multiselect','multiple']) !!}
                        </div>
                    </div>
                @endcan
                @cannot('manageRoles',$account)
                    <div class="form-group">
                        <label for="" class="col-md-3 control-label">@lang('users.attributes.roles')</label>
                        <div class="col-md-9">
                            <p class="form-control">@foreach($account->roles as $role) {{ $role->name }}@if($loop->count > 1 && !$loop->last)
                                    , @endif @endforeach</p>
                        </div>
                    </div>
                @endcannot
                @can('manageStatus',$account)
                    <div class="form-group">
                        <label for="enabled_from"
                               class="col-md-3 control-label">@lang('users.attributes.enabled_from')</label>
                        <div class="col-md-9">
                            {!! Form::text('enabled_from', $account->itEnabledFrom, ['id' => 'enabled_from_dtp', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="enabled_to"
                               class="col-md-3 control-label">@lang('users.attributes.enabled_to')</label>
                        <div class="col-md-9">
                            {!! Form::text('enabled_to', $account->itEnabledTo, ['id' => 'enabled_to_dtp', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                @endcan

                <fieldset>
                    <legend>Dati sugli accessi</legend>
                    <div class="form-group">
                        <label for="failed_login_count"
                               class="col-md-3 control-label">@lang('auth_types.title')</label>
                        <div class="col-md-2">
                            <p class="form-control-static">{{ $account->authType->name }}</p>
                        </div>
                    </div>
                    @can('manageStatus',$account)
                        <div class="form-group">
                            <label for="locked" class="col-md-3 control-label">@lang('users.attributes.locked')</label>
                            <div class="col-md-2">
                                {!! Form::select('locked', [0 => trans('common.no'),1 => trans('common.yes')],null,['id' => 'locked', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    @endcan
                    @cannot('manageStatus',$account)
                        <div class="form-group">
                            <label for="locked" class="col-md-3 control-label">@lang('users.attributes.locked')</label>
                            <div class="col-md-2">
                                <p class="form-control-static">@if($account->locked) @lang('common.yes') @else @lang('common.no') @endif</p>
                            </div>
                        </div>
                    @endcannot
                    <div class="form-group">
                        <label for="failed_login_count"
                               class="col-md-3 control-label">@lang('users.attributes.failed_login_count')</label>
                        <div class="col-md-2">
                            <p class="form-control-static">{{ $account->failed_login_count }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="login_success_on"
                               class="col-md-3 control-label">@lang('users.attributes.login_success_on')</label>
                        <div class="col-md-2">
                            <p class="form-control-static">{{ optional($account->login_success_on)->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <label for="locked"
                               class="col-md-3 control-label">@lang('users.attributes.login_success_ipv4')</label>
                        <div class="col-md-2">
                            <p class="form-control-static">{{ $account->login_success_ipv4 }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="login_failed_on"
                               class="col-md-3 control-label">@lang('users.attributes.login_failed_on')</label>
                        <div class="col-md-2">
                            <p class="form-control-static">{{ optional($account->login_failed_on)->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <label for="login_failed_ipv4"
                               class="col-md-3 control-label">@lang('users.attributes.login_failed_ipv4')</label>
                        <div class="col-md-2">
                            <p class="form-control-static">{{ $account->login_failed_ipv4 }}</p>
                        </div>
                    </div>

                </fieldset>
                @can('changePassword',$account)
                    <fieldset>
                        <legend>Variazione password</legend>
                        <div class="form-group">
                            <label for="pwd_changed_at"
                                   class="col-md-3 control-label">@lang('users.attributes.pwd_changed_at')</label>
                            <div class="col-md-2">
                                <p class="form-control-static">{{ optional($account->pwd_changed_at)->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for=""
                                   class="col-md-3 control-label">@lang('users.attributes.current_password')</label>
                            <div class="col-md-9">
                                {!! Form::password('current_password', [ 'class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">@lang('users.attributes.password')</label>
                            <div class="col-md-9">
                                {!! Form::password('password', [ 'class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for=""
                                   class="col-md-3 control-label">@lang('users.attributes.password_check')</label>
                            <div class="col-md-9">
                                {!! Form::password('password_check', [ 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    </fieldset>
                @endcan
            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    @can('delete',$account)
                        <a href="{{ route('admin::users.delete',$account->id) }}"
                           class="btn btn-md btn-danger"
                           data-toggle="modal"
                           data-target="#myModal"><i class="fas fa-fw fa-trash-alt"></i>@lang('common.form.delete')
                        </a>@endcan
                    <a href="@can('list','App\Auth\User'){{ backtoSource('admin::users.index') }}@endcan
                    @cannot('list','App\Auth\User'){{ backtoSource('admin::dashboard') }} @endcannot" class="btn btn-md btn-secondary">
                        <i class="fas fa-fw fa-arrow-left" ></i>@lang('common.form.back')
                    </a>

                    <button type="submit" class="btn btn-md btn-primary control-btn"><i
                                class="fas fa-save fa-fw"></i> @lang('common.form.save')</button>
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