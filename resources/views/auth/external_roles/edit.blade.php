@extends('layouts.adminlte.template',['page_title' => trans('external_roles.edit.title')])
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 12])
            @slot('title')@lang('external_roles.edit.title'): {{$external_role->external_role_id}}@endslot
            @slot('body')
                {!! Form::open(['route' =>['admin::external_roles.update',$external_role->id],'class' => 'form-horizontal','method'=>'PATCH']) !!}
                    <div class="form-group required row">
                        <label for="external_role_id"
                               class="col-md-2 control-label">@lang('external_roles.attributes.external_role_id')</label>
                        <div class="col-md-10">
                            <div class="col-md-4 ">
                                {!! Form::select('external_role_id', [$external_role->external_role_id], $external_role->external_role_id,  ['class' => 'form-control', 'id' => 'external_role_id', 'required','disabled']) !!}
                            </div>
                        </div>
                    </div>
                <div class="form-group required row">
                    <label for="auth_type_id"
                           class="col-md-2 control-label">@lang('external_roles.attributes.auth_type_id')</label>
                    <div class="col-md-10">
                        <div class="col-md-4 ">
                            {!! Form::select('auth_type_id', $auth_types, $external_role->auth_type_id,  ['class' => 'form-control', 'id' => 'auth_type_id', 'required']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group  row">
                    <label for="roles"
                           class="col-md-2 control-label">@lang('external_roles.attributes.role_id')</label>
                    <div class="col-md-10">
                        <div class="col-md-4 ">
                            {!! Form::select('roles[]', $roles, $selected_roles,  ['class' => 'form-control', 'id' => 'roles', 'multiple','style' => 'width:100%']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="auto_register_users"
                           class="col-md-2 control-label">@lang('external_roles.attributes.auto_register_users')</label>
                    <div class="col-md-10">
                        <div class="col-md-4 ">
                            {!! Form::select('auto_register_users', [0=>trans('common.no'), 1=>trans('common.yes')], $external_role->auto_register_users,  ['class' => 'form-control', 'id' => 'auto_register_users']) !!}
                        </div>
                    </div>
                </div>


            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    <a href="{{ backtoSource('admin::external_roles.index') }}">
                        <button type="button" class="btn btn-md btn-secondary">
                            <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                        </button>
                    </a>

                    <button type="submit" class="btn btn-md btn-primary control-btn"><i
                                class="fas fa-save fa-fw"></i> @lang('common.form.save')</button>

                </div>
            @endslot
        @endcomponent
        {!! Form::close() !!}
    </div>
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

    @endpush
@endsection
